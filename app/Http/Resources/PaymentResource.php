<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_number' => $this->payment_number,
            'invoice_id' => $this->invoice_id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'payment_date' => $this->payment_date,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'gateway' => $this->gateway,
            'gateway_response' => $this->when(
                $request->user()?->hasRole('admin') || $request->user()?->hasRole('landlord'),
                $this->gateway_response
            ),
            'reference_number' => $this->reference_number,
            'bank_name' => $this->bank_name,
            'account_number' => $this->when(
                $request->user()?->id === $this->payer_id,
                $this->account_number ? substr($this->account_number, -4) : null
            ),
            'check_number' => $this->check_number,
            'notes' => $this->notes,
            'receipt_url' => $this->receipt_path ? asset('storage/' . $this->receipt_path) : null,
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            'payer' => new UserResource($this->whenLoaded('payer')),
            'processed_by' => new UserResource($this->whenLoaded('processedBy')),
            'contract' => $this->when(
                $this->relationLoaded('invoice') && $this->invoice->relationLoaded('contract'),
                function () {
                    return new ContractResource($this->invoice->contract);
                }
            ),
            'is_refundable' => $this->status === 'completed' &&
                              $this->payment_date &&
                              now()->diffInDays($this->payment_date) <= 30,
            'refund' => $this->when($this->refund_id, [
                'id' => $this->refund_id,
                'amount' => $this->refund_amount,
                'reason' => $this->refund_reason,
                'date' => $this->refund_date,
                'status' => $this->refund_status,
            ]),
            'processing_fee' => $this->processing_fee,
            'net_amount' => $this->amount - ($this->processing_fee ?? 0),
            'currency' => $this->currency ?? 'USD',
            'exchange_rate' => $this->exchange_rate ?? 1,
            'confirmed_at' => $this->confirmed_at,
            'failed_at' => $this->failed_at,
            'failure_reason' => $this->failure_reason,
            'retry_count' => $this->retry_count ?? 0,
            'metadata' => $this->metadata ?? [],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}