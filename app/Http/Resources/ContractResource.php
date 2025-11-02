<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $now = Carbon::now();
        $endDate = Carbon::parse($this->end_date);
        $startDate = Carbon::parse($this->start_date);

        return [
            'id' => $this->id,
            'contract_number' => $this->contract_number,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'monthly_rent' => $this->monthly_rent,
            'deposit_amount' => $this->deposit_amount,
            'deposit_status' => $this->deposit_status,
            'status' => $this->status,
            'payment_day' => $this->payment_day,
            'terms' => $this->terms,
            'special_conditions' => $this->special_conditions,
            'utilities_included' => $this->utilities_included ?? [],
            'additional_fees' => $this->additional_fees ?? [],
            'duration_months' => $startDate->diffInMonths($endDate),
            'days_remaining' => $this->status === 'active' ? max(0, $now->diffInDays($endDate, false)) : null,
            'is_expiring_soon' => $this->status === 'active' && $now->diffInDays($endDate) <= 30,
            'renewal_date' => $this->renewal_date,
            'auto_renew' => $this->auto_renew ?? false,
            'tenant' => new UserResource($this->whenLoaded('tenant')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'property' => $this->when($this->relationLoaded('room') && $this->room->relationLoaded('property'), function () {
                return new PropertyResource($this->room->property);
            }),
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'total_paid' => $this->when($request->user()?->can('view-payments', $this), function () {
                return $this->invoices()
                    ->whereIn('status', ['paid', 'partial'])
                    ->with('payments')
                    ->get()
                    ->pluck('payments')
                    ->flatten()
                    ->sum('amount');
            }),
            'outstanding_balance' => $this->when($request->user()?->can('view-payments', $this), function () {
                return $this->invoices()
                    ->whereIn('status', ['pending', 'overdue', 'partial'])
                    ->sum('total_amount') -
                    $this->invoices()
                    ->whereIn('status', ['partial'])
                    ->with('payments')
                    ->get()
                    ->pluck('payments')
                    ->flatten()
                    ->sum('amount');
            }),
            'last_payment_date' => $this->when($request->user()?->can('view-payments', $this), function () {
                $lastPayment = $this->invoices()
                    ->with('payments')
                    ->get()
                    ->pluck('payments')
                    ->flatten()
                    ->sortByDesc('created_at')
                    ->first();
                return $lastPayment ? $lastPayment->created_at->toDateString() : null;
            }),
            'emergency_contact' => [
                'name' => $this->emergency_contact_name,
                'phone' => $this->emergency_contact_phone,
                'relationship' => $this->emergency_contact_relationship,
            ],
            'signed_date' => $this->signed_date,
            'move_in_date' => $this->move_in_date,
            'move_out_date' => $this->move_out_date,
            'termination_date' => $this->termination_date,
            'termination_reason' => $this->termination_reason,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}