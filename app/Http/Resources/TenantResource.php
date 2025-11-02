<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $activeContract = $this->tenantContracts()->active()->with(['room.property'])->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'national_id' => $this->national_id,
            'date_of_birth' => $this->date_of_birth,
            'occupation' => $this->occupation,
            'employer' => $this->employer,
            'emergency_contact' => [
                'name' => $this->emergency_contact_name,
                'phone' => $this->emergency_contact_phone,
                'relationship' => $this->emergency_contact_relationship,
            ],
            'current_rental' => $activeContract ? [
                'contract_id' => $activeContract->id,
                'property' => $activeContract->room->property->name,
                'room' => $activeContract->room->room_number,
                'monthly_rent' => $activeContract->monthly_rent,
                'start_date' => $activeContract->start_date,
                'end_date' => $activeContract->end_date,
                'deposit_amount' => $activeContract->deposit_amount,
                'deposit_status' => $activeContract->deposit_status,
            ] : null,
            'rental_history' => $this->whenLoaded('tenantContracts', function () {
                return $this->tenantContracts->map(function ($contract) {
                    return [
                        'id' => $contract->id,
                        'property' => $contract->room?->property?->name,
                        'room' => $contract->room?->room_number,
                        'start_date' => $contract->start_date,
                        'end_date' => $contract->end_date,
                        'status' => $contract->status,
                        'monthly_rent' => $contract->monthly_rent,
                    ];
                });
            }),
            'payment_history' => [
                'total_paid' => $this->tenantContracts()
                    ->with('invoices.payments')
                    ->get()
                    ->pluck('invoices')
                    ->flatten()
                    ->pluck('payments')
                    ->flatten()
                    ->sum('amount'),
                'on_time_payments' => $this->tenantContracts()
                    ->with('invoices.payments')
                    ->get()
                    ->pluck('invoices')
                    ->flatten()
                    ->filter(function ($invoice) {
                        return $invoice->payments->where('payment_date', '<=', $invoice->due_date)->isNotEmpty();
                    })
                    ->count(),
                'late_payments' => $this->tenantContracts()
                    ->with('invoices.payments')
                    ->get()
                    ->pluck('invoices')
                    ->flatten()
                    ->filter(function ($invoice) {
                        return $invoice->payments->where('payment_date', '>', $invoice->due_date)->isNotEmpty();
                    })
                    ->count(),
                'outstanding_balance' => $this->tenantContracts()
                    ->with('invoices')
                    ->get()
                    ->pluck('invoices')
                    ->flatten()
                    ->whereIn('status', ['pending', 'overdue', 'partial'])
                    ->sum('total_amount') -
                    $this->tenantContracts()
                    ->with('invoices.payments')
                    ->get()
                    ->pluck('invoices')
                    ->flatten()
                    ->whereIn('status', ['partial'])
                    ->pluck('payments')
                    ->flatten()
                    ->sum('amount'),
            ],
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'references' => $this->references ?? [],
            'notes' => $this->when(
                $request->user()?->hasRole('landlord') || $request->user()?->hasRole('admin'),
                $this->notes
            ),
            'rating' => $this->when(
                $request->user()?->hasRole('landlord') || $request->user()?->hasRole('admin'),
                [
                    'score' => $this->rating_score ?? null,
                    'payment_reliability' => $this->payment_reliability ?? null,
                    'property_care' => $this->property_care ?? null,
                    'communication' => $this->communication ?? null,
                    'would_rent_again' => $this->would_rent_again ?? null,
                ]
            ),
            'blacklisted' => $this->blacklisted ?? false,
            'blacklist_reason' => $this->when($this->blacklisted, $this->blacklist_reason),
            'email_verified' => !is_null($this->email_verified_at),
            'joined_at' => $this->created_at->toISOString(),
            'last_active' => $this->last_active_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}