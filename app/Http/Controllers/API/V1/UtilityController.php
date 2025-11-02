<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\UtilityBill;
use App\Models\UtilityType;
use App\Models\Room;
use App\Services\Utility\UtilityBillingService;
use App\Services\Invoice\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UtilityController extends BaseController
{
    protected UtilityBillingService $utilityBillingService;
    protected InvoiceService $invoiceService;

    public function __construct(
        UtilityBillingService $utilityBillingService,
        InvoiceService $invoiceService
    ) {
        $this->utilityBillingService = $utilityBillingService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Get all meters
     */
    public function getMeters(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('landlord')) {
                return $this->sendError('Only landlords can view meters', [], 403);
            }

            $query = Meter::whereHas('room.property', function ($q) use ($user) {
                $q->where('landlord_id', $user->id);
            })->with(['room.property', 'utilityType']);

            // Apply filters
            if ($request->has('property_id')) {
                $query->whereHas('room', function ($q) use ($request) {
                    $q->where('property_id', $request->property_id);
                });
            }

            if ($request->has('room_id')) {
                $query->where('room_id', $request->room_id);
            }

            if ($request->has('meter_type')) {
                $query->where('meter_type', $request->meter_type);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $meters = $query->get();

            $data = $meters->map(function ($meter) {
                return $this->transformMeter($meter);
            });

            return $this->sendResponse($data, 'Meters retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve meters', [$e->getMessage()], 500);
        }
    }

    /**
     * Create a new meter
     */
    public function createMeter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'meter_number' => 'required|string|unique:meters,meter_number',
            'meter_type' => 'required|in:electricity,water,gas',
            'initial_reading' => 'required|numeric|min:0',
            'rate_per_unit' => 'required|numeric|min:0',
            'utility_type_id' => 'nullable|exists:utility_types,id',
            'installation_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $room = Room::findOrFail($request->room_id);

            // Check authorization
            if ($room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if meter of same type already exists for room
            $existingMeter = Meter::where('room_id', $room->id)
                ->where('meter_type', $request->meter_type)
                ->where('status', 'active')
                ->exists();

            if ($existingMeter) {
                return $this->sendError("Active {$request->meter_type} meter already exists for this room", [], 400);
            }

            $meter = Meter::create([
                'room_id' => $request->room_id,
                'meter_number' => $request->meter_number,
                'meter_type' => $request->meter_type,
                'initial_reading' => $request->initial_reading,
                'current_reading' => $request->initial_reading,
                'rate_per_unit' => $request->rate_per_unit,
                'utility_type_id' => $request->utility_type_id,
                'installation_date' => $request->installation_date ?? now(),
                'status' => $request->status,
            ]);

            // Create initial reading
            MeterReading::create([
                'meter_id' => $meter->id,
                'reading_date' => now(),
                'reading_value' => $request->initial_reading,
                'previous_reading' => 0,
                'consumption' => 0,
                'reading_type' => 'initial',
                'recorded_by' => Auth::id(),
            ]);

            $data = $this->transformMeter($meter->fresh(['room.property', 'utilityType']));

            return $this->sendResponse($data, 'Meter created successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create meter', [$e->getMessage()], 500);
        }
    }

    /**
     * Update meter
     */
    public function updateMeter(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'meter_number' => 'sometimes|required|string|unique:meters,meter_number,' . $id,
            'rate_per_unit' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:active,inactive,maintenance',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $meter = Meter::findOrFail($id);

            // Check authorization
            if ($meter->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $meter->update($request->only(['meter_number', 'rate_per_unit', 'status']));

            $data = $this->transformMeter($meter->fresh(['room.property', 'utilityType']));

            return $this->sendResponse($data, 'Meter updated successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update meter', [$e->getMessage()], 500);
        }
    }

    /**
     * Delete meter
     */
    public function deleteMeter($id)
    {
        try {
            $meter = Meter::findOrFail($id);

            // Check authorization
            if ($meter->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if meter has readings
            if ($meter->meterReadings()->count() > 1) { // More than initial reading
                return $this->sendError('Cannot delete meter with reading history', [], 400);
            }

            $meter->delete();

            return $this->sendResponse(null, 'Meter deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete meter', [$e->getMessage()], 500);
        }
    }

    /**
     * Record meter reading
     */
    public function recordReading(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meter_id' => 'required|exists:meters,id',
            'reading_value' => 'required|numeric|min:0',
            'reading_date' => 'nullable|date|before_or_equal:today',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $meter = Meter::findOrFail($request->meter_id);

            // Check authorization
            if ($meter->room->property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            // Check if reading is greater than current reading
            if ($request->reading_value < $meter->current_reading) {
                return $this->sendError('Reading value cannot be less than current reading', [], 400);
            }

            // Check for duplicate reading on same date
            $readingDate = $request->reading_date ? Carbon::parse($request->reading_date) : now();
            $existingReading = MeterReading::where('meter_id', $meter->id)
                ->whereDate('reading_date', $readingDate->toDateString())
                ->exists();

            if ($existingReading) {
                return $this->sendError('Reading already exists for this date', [], 400);
            }

            // Calculate consumption
            $consumption = $request->reading_value - $meter->current_reading;

            // Check for abnormal consumption
            $abnormalCheck = $this->utilityBillingService->detectAbnormalConsumption(
                $meter,
                $consumption,
                30 // Assuming monthly reading
            );

            // Create reading
            $reading = MeterReading::create([
                'meter_id' => $meter->id,
                'reading_date' => $readingDate,
                'reading_value' => $request->reading_value,
                'previous_reading' => $meter->current_reading,
                'consumption' => $consumption,
                'reading_type' => 'manual',
                'recorded_by' => Auth::id(),
                'notes' => $request->notes,
                'is_abnormal' => $abnormalCheck['is_abnormal'],
            ]);

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'reading_' . $reading->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/meter_readings', $filename);
                $reading->update(['image_path' => 'meter_readings/' . $filename]);
            }

            // Update meter's current reading
            $meter->update(['current_reading' => $request->reading_value]);

            // Calculate utility bill if contract exists
            if ($meter->room->activeContract) {
                $bill = $this->utilityBillingService->calculateUtilityBill(
                    $meter,
                    Carbon::parse($meter->updated_at),
                    $readingDate
                );

                // Add to invoice if needed
                if ($bill && $meter->room->activeContract) {
                    // This would integrate with your invoice system
                }
            }

            $data = [
                'reading_id' => $reading->id,
                'consumption' => $consumption,
                'cost' => $consumption * $meter->rate_per_unit,
                'is_abnormal' => $abnormalCheck['is_abnormal'],
                'abnormal_reason' => $abnormalCheck['reason'] ?? null,
            ];

            return $this->sendResponse($data, 'Reading recorded successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to record reading', [$e->getMessage()], 500);
        }
    }

    /**
     * Get meter readings
     */
    public function getMeterReadings($meterId)
    {
        try {
            $meter = Meter::findOrFail($meterId);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($meter->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if (!$meter->room->activeContract || $meter->room->activeContract->user_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            $readings = MeterReading::where('meter_id', $meterId)
                ->orderBy('reading_date', 'desc')
                ->get();

            $data = $readings->map(function ($reading) {
                return [
                    'id' => $reading->id,
                    'reading_date' => $reading->reading_date,
                    'reading_value' => $reading->reading_value,
                    'consumption' => $reading->consumption,
                    'cost' => $reading->consumption * $reading->meter->rate_per_unit,
                    'is_abnormal' => $reading->is_abnormal,
                    'notes' => $reading->notes,
                    'recorded_by' => $reading->recordedBy ? $reading->recordedBy->name : null,
                ];
            });

            return $this->sendResponse($data, 'Readings retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve readings', [$e->getMessage()], 500);
        }
    }

    /**
     * Get utility bills
     */
    public function getUtilityBills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'property_id' => 'nullable|exists:properties,id',
            'room_id' => 'nullable|exists:rooms,id',
            'status' => 'nullable|in:pending,paid,overdue',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $user = Auth::user();

            $query = UtilityBill::whereHas('contract.room.property', function ($q) use ($user) {
                if ($user->hasRole('landlord')) {
                    $q->where('landlord_id', $user->id);
                } elseif ($user->hasRole('tenant')) {
                    $q->whereHas('contracts', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
                }
            })->with(['contract.tenant', 'contract.room', 'utilityType']);

            // Apply filters
            if ($request->from_date) {
                $query->where('billing_period_start', '>=', $request->from_date);
            }

            if ($request->to_date) {
                $query->where('billing_period_end', '<=', $request->to_date);
            }

            if ($request->property_id) {
                $query->whereHas('contract.room', function ($q) use ($request) {
                    $q->where('property_id', $request->property_id);
                });
            }

            if ($request->room_id) {
                $query->whereHas('contract', function ($q) use ($request) {
                    $q->where('room_id', $request->room_id);
                });
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $bills = $query->orderBy('billing_period_end', 'desc')->get();

            $data = $bills->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'tenant' => $bill->contract->tenant->name,
                    'room' => $bill->contract->room->room_number,
                    'property' => $bill->contract->room->property->name,
                    'utility_type' => $bill->utilityType->name,
                    'billing_period' => [
                        'start' => $bill->billing_period_start,
                        'end' => $bill->billing_period_end,
                    ],
                    'consumption' => $bill->consumption,
                    'rate' => $bill->rate_per_unit,
                    'amount' => $bill->amount,
                    'status' => $bill->status,
                ];
            });

            return $this->sendResponse($data, 'Utility bills retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve utility bills', [$e->getMessage()], 500);
        }
    }

    /**
     * Calculate utility bills for a period
     */
    public function calculateBills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'billing_month' => 'required|date_format:Y-m',
            'auto_add_to_invoice' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator);
        }

        try {
            $property = \App\Models\Property::findOrFail($request->property_id);

            // Check authorization
            if ($property->landlord_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $billingMonth = Carbon::createFromFormat('Y-m', $request->billing_month);
            $startDate = $billingMonth->copy()->startOfMonth();
            $endDate = $billingMonth->copy()->endOfMonth();

            // Get all active meters for the property
            $meters = Meter::whereHas('room', function ($q) use ($property) {
                $q->where('property_id', $property->id);
            })
                ->where('status', 'active')
                ->get();

            $billsCreated = [];
            $errors = [];

            foreach ($meters as $meter) {
                try {
                    // Check if bill already exists for this period
                    $existingBill = UtilityBill::where('meter_id', $meter->id)
                        ->where('billing_period_start', $startDate)
                        ->where('billing_period_end', $endDate)
                        ->exists();

                    if ($existingBill) {
                        $errors[] = "Bill already exists for meter {$meter->meter_number}";
                        continue;
                    }

                    // Calculate bill
                    $bill = $this->utilityBillingService->calculateUtilityBill($meter, $startDate, $endDate);

                    if ($bill) {
                        $billsCreated[] = [
                            'meter' => $meter->meter_number,
                            'room' => $meter->room->room_number,
                            'amount' => $bill['amount'],
                            'consumption' => $bill['consumption'],
                        ];

                        // Add to invoice if requested
                        if ($request->auto_add_to_invoice && $meter->room->activeContract) {
                            // Find or create invoice for the month
                            $invoice = Invoice::firstOrCreate([
                                'contract_id' => $meter->room->activeContract->id,
                                'issue_date' => $endDate,
                            ], [
                                'invoice_number' => $this->invoiceService->generateInvoiceNumber(Auth::user()),
                                'due_date' => $endDate->copy()->addDays(15),
                                'status' => 'pending',
                                'total_amount' => 0,
                            ]);

                            // Add utility line item
                            $invoice->lineItems()->create([
                                'description' => "{$meter->meter_type} - {$billingMonth->format('F Y')}",
                                'amount' => $bill['amount'],
                                'lineable_type' => UtilityBill::class,
                                'lineable_id' => $bill['id'],
                            ]);

                            // Update invoice total
                            $invoice->update([
                                'total_amount' => $invoice->lineItems()->sum('amount'),
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed to calculate bill for meter {$meter->meter_number}: " . $e->getMessage();
                }
            }

            $response = [
                'bills_created' => count($billsCreated),
                'total_amount' => collect($billsCreated)->sum('amount'),
                'details' => $billsCreated,
                'errors' => $errors,
            ];

            return $this->sendResponse($response, 'Utility bills calculated');
        } catch (\Exception $e) {
            return $this->sendError('Failed to calculate bills', [$e->getMessage()], 500);
        }
    }

    /**
     * Get consumption history for a meter
     */
    public function getConsumptionHistory($meterId)
    {
        try {
            $meter = Meter::findOrFail($meterId);

            // Check authorization
            $user = Auth::user();
            if ($user->hasRole('landlord')) {
                if ($meter->room->property->landlord_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            } elseif ($user->hasRole('tenant')) {
                if (!$meter->room->activeContract || $meter->room->activeContract->user_id !== $user->id) {
                    return $this->sendError('Unauthorized', [], 403);
                }
            }

            // Get last 12 months of consumption
            $readings = MeterReading::where('meter_id', $meterId)
                ->where('reading_date', '>=', now()->subMonths(12))
                ->orderBy('reading_date', 'asc')
                ->get();

            $history = [];
            $totalConsumption = 0;
            $totalCost = 0;

            foreach ($readings as $reading) {
                $cost = $reading->consumption * $meter->rate_per_unit;
                $totalConsumption += $reading->consumption;
                $totalCost += $cost;

                $history[] = [
                    'month' => Carbon::parse($reading->reading_date)->format('Y-m'),
                    'reading_date' => $reading->reading_date,
                    'reading_value' => $reading->reading_value,
                    'consumption' => $reading->consumption,
                    'cost' => $cost,
                    'is_abnormal' => $reading->is_abnormal,
                ];
            }

            $averageConsumption = count($history) > 0 ? $totalConsumption / count($history) : 0;
            $averageCost = count($history) > 0 ? $totalCost / count($history) : 0;

            $data = [
                'meter' => [
                    'meter_number' => $meter->meter_number,
                    'meter_type' => $meter->meter_type,
                    'room' => $meter->room->room_number,
                    'rate_per_unit' => $meter->rate_per_unit,
                ],
                'history' => $history,
                'summary' => [
                    'total_consumption' => $totalConsumption,
                    'total_cost' => $totalCost,
                    'average_consumption' => round($averageConsumption, 2),
                    'average_cost' => round($averageCost, 2),
                    'months_count' => count($history),
                ],
            ];

            return $this->sendResponse($data, 'Consumption history retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve consumption history', [$e->getMessage()], 500);
        }
    }

    /**
     * Get utility types
     */
    public function getUtilityTypes()
    {
        try {
            $types = UtilityType::where('is_active', true)
                ->get(['id', 'name', 'unit', 'default_rate']);

            return $this->sendResponse($types, 'Utility types retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve utility types', [$e->getMessage()], 500);
        }
    }

    /**
     * Transform meter for API response
     */
    protected function transformMeter($meter)
    {
        return [
            'id' => $meter->id,
            'meter_number' => $meter->meter_number,
            'meter_type' => $meter->meter_type,
            'room' => [
                'id' => $meter->room->id,
                'number' => $meter->room->room_number,
                'property' => $meter->room->property->name,
            ],
            'current_reading' => $meter->current_reading,
            'rate_per_unit' => $meter->rate_per_unit,
            'status' => $meter->status,
            'installation_date' => $meter->installation_date,
            'last_reading_date' => $meter->meterReadings()->latest()->first()?->reading_date,
        ];
    }
}