<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InvoiceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => InvoiceResource::collection($this->collection),
            'summary' => [
                'total_invoices' => $this->collection->count(),
                'total_amount' => $this->collection->sum('total_amount'),
                'paid_amount' => $this->collection->where('status', 'paid')->sum('total_amount'),
                'pending_amount' => $this->collection->whereIn('status', ['pending', 'partial'])->sum(function ($invoice) {
                    return $invoice->total_amount - ($invoice->paid_amount ?? 0);
                }),
                'overdue_amount' => $this->collection->where('status', 'overdue')->sum('total_amount'),
                'by_status' => [
                    'pending' => $this->collection->where('status', 'pending')->count(),
                    'paid' => $this->collection->where('status', 'paid')->count(),
                    'partial' => $this->collection->where('status', 'partial')->count(),
                    'overdue' => $this->collection->where('status', 'overdue')->count(),
                    'cancelled' => $this->collection->where('status', 'cancelled')->count(),
                ],
                'average_invoice_amount' => $this->collection->avg('total_amount'),
                'total_tax' => $this->collection->sum('tax_amount'),
                'total_discount' => $this->collection->sum('discount_amount'),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param Request $request
     * @return array
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'filters_applied' => [
                    'status' => $request->status,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'property_id' => $request->property_id,
                    'tenant_id' => $request->tenant_id,
                ],
                'currency' => $request->currency ?? 'USD',
                'exchange_rates' => [
                    'USD_to_KHR' => 4100,
                ],
            ],
        ];
    }
}