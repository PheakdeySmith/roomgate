<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $dueDate = Carbon::parse($this->due_date);
        $now = Carbon::now();

        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'contract_id' => $this->contract_id,
            'billing_period_start' => $this->billing_period_start,
            'billing_period_end' => $this->billing_period_end,
            'due_date' => $this->due_date,
            'days_until_due' => $this->status === 'pending' ? $now->diffInDays($dueDate, false) : null,
            'is_overdue' => $this->status === 'pending' && $dueDate->isPast(),
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount ?? 0,
            'balance_due' => $this->total_amount - ($this->paid_amount ?? 0),
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            'currency' => $this->currency ?? 'USD',
            'exchange_rate' => $this->exchange_rate ?? 1,
            'line_items' => $this->whenLoaded('lineItems', function () {
                return $this->lineItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'description' => $item->description,
                        'type' => $item->type,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'amount' => $item->amount,
                        'tax_rate' => $item->tax_rate,
                        'tax_amount' => $item->tax_amount,
                        'total' => $item->total,
                        'period_start' => $item->period_start,
                        'period_end' => $item->period_end,
                        'meter_reading_start' => $item->meter_reading_start,
                        'meter_reading_end' => $item->meter_reading_end,
                        'consumption' => $item->consumption,
                    ];
                });
            }),
            'contract' => new ContractResource($this->whenLoaded('contract')),
            'tenant' => $this->when($this->relationLoaded('contract') && $this->contract->relationLoaded('tenant'), function () {
                return new UserResource($this->contract->tenant);
            }),
            'property' => $this->when(
                $this->relationLoaded('contract') &&
                $this->contract->relationLoaded('room') &&
                $this->contract->room->relationLoaded('property'),
                function () {
                    return new PropertyResource($this->contract->room->property);
                }
            ),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'last_payment_date' => $this->when($this->payments_count > 0, function () {
                return $this->payments()
                    ->latest()
                    ->first()
                    ?->created_at
                    ->toDateString();
            }),
            'reminder_sent_at' => $this->reminder_sent_at,
            'overdue_notice_sent_at' => $this->overdue_notice_sent_at,
            'generated_at' => $this->generated_at,
            'sent_at' => $this->sent_at,
            'viewed_at' => $this->viewed_at,
            'pdf_url' => $this->pdf_path ? asset('storage/' . $this->pdf_path) : null,
            'can_pay' => $this->status === 'pending' || $this->status === 'partial',
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}