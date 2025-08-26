@extends('backends.layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number)

@push('style')
    <style>
        /* --- General Styles --- */
        body {
            font-family: sans-serif;
            -webkit-print-color-adjust: exact;
            background-color: #f8f9fa;
        }

        .invoice-card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* --- Desktop Table Styles --- */
        @media (min-width: 768px) {

            .invoice-items-table .totals-row td,
            .invoice-items-table .final-total-row th,
            .invoice-items-table .final-total-row td {
                border: none;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }

            .invoice-items-table .totals-row td:nth-last-child(-n+2) {
                border-top: 1px solid #e9ecef;
            }

            .invoice-items-table .final-total-row th,
            .invoice-items-table .final-total-row td {
                border-top: 2px solid #212529;
                border-bottom: 2px solid #212529;
            }
        }

        /* --- Mobile Responsive Styles --- */
        @media (max-width: 767px) {
            .invoice-items-table thead {
                display: none;
            }

            .invoice-items-table,
            .invoice-items-table tbody,
            .invoice-items-table tr,
            .invoice-items-table td {
                display: block;
                width: 100%;
            }

            .invoice-items-table tr {
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                overflow: hidden;
            }

            .invoice-items-table tr.totals-row,
            .invoice-items-table tr.final-total-row {
                border: 1px solid #dee2e6;
            }

            .invoice-items-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 0.75rem;
                border: none;
                border-bottom: 1px solid #e9ecef;
            }

            .invoice-items-table tr td:last-child {
                border-bottom: none;
            }

            .invoice-items-table td[data-label]::before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
                margin-right: 1rem;
            }

            .invoice-items-table tr.totals-row td,
            .invoice-items-table tr.final-total-row td {
                display: flex;
                justify-content: space-between;
                font-weight: 600;
            }

            .invoice-items-table .final-total-row {
                background-color: #f8f9fa;
                font-size: 1.1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <a href="{{ route('landlord.payments.index') }}" class="btn btn-outline-secondary" aria-label="Back to Invoices">
                <i class="ti ti-arrow-left"></i>
                <span class="d-none d-sm-inline ms-1">Back</span>
            </a>

            <div class="d-flex align-items-center">
                <span class="fw-bold me-2 d-none d-md-inline">Status:</span>
                <select class="form-select" style="width: auto;" aria-label="Change invoice status" id="invoiceStatusSelect"
                    data-update-url="{{ route('landlord.payments.updateStatus', $invoice) }}">

                    <option value="draft" {{ strtolower($invoice->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ strtolower($invoice->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="paid" {{ strtolower($invoice->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ strtolower($invoice->status) == 'partial' ? 'selected' : '' }}>Partial
                    </option>
                    <option value="overdue" {{ strtolower($invoice->status) == 'overdue' ? 'selected' : '' }}>Overdue
                    </option>
                    <option value="void" {{ strtolower($invoice->status) == 'void' ? 'selected' : '' }}>Void</option>

                </select>
            </div>
        </div>

        <div class="card invoice-card mb-4">
            <div class="card-body p-lg-5">
                <div class="row align-items-center mb-4">
                    <div class="col-6"><img src="{{ asset('assets/images/logo-dark.png') }}" height="60"
                            alt="Company Logo">
                    </div>
                    <div class="col-6 text-end">
                        <h6>Invoice #{{ $invoice->invoice_number }}</h6>
                    </div>
                </div>
                <hr>

                <div class="row mb-4">
                    <div class="col-6">
                        <p class="fw-bold">Bill To:</p>
                        <address>
                            <strong>{{ $invoice->contract?->tenant?->name ?? 'N/A' }}</strong><br>
                            Room {{ $invoice->contract?->room?->room_number ?? 'N/A' }}<br>
                            {{ $invoice->contract?->room?->property?->name ?? 'N/A' }}
                        </address>
                    </div>
                    <div class="col-6 text-end">
                        <p><span class="fw-bold">Invoice Date:</span> {{ $invoice->issue_date->format('F d, Y') }}</p>
                        <p><span class="fw-bold">Due Date:</span> {{ $invoice->due_date->format('F d, Y') }}</p>

                        @if (strtolower($invoice->status) == 'paid' && $invoice->payment_date)
                            <p><span class="fw-bold">Payment Date:</span>
                                {{ $invoice->payment_date->format('F d, Y') }}</p>
                        @endif
                    </div>
                </div>

                <table class="table invoice-items-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th class="text-start">Item Details</th>
                            <th class="text-start">Consumption</th>
                            <th class="text-start">Unit Price</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Use the reject() method to filter out any line items for discounts.
                            $mainItems = $invoice->lineItems->reject(function ($item) {
                                return str_contains(strtolower($item->description), 'discount');
                            });
                            $discountItem = $invoice->lineItems->first(function ($item) {
                                return str_contains(strtolower($item->description), 'discount');
                            });
                        @endphp

                        @forelse ($mainItems as $index => $item)
                            <tr>
                                <td data-label="#">{{ $index + 1 }}</td>
                                <td data-label="Item Details" class="text-start">{{ $item->description }}</td>
                                <td data-label="Consumption">{{ $item->lineable?->consumption ?? 'N/A' }}</td>
                                <td data-label="Unit Price">
                                    {!! format_money($item->lineable?->rate_applied ?? $item->amount) !!}</td>
                                <td data-label="Amount" class="text-end">{!! format_money($item->amount) !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No line items found for this invoice.
                                </td>
                            </tr>
                        @endforelse

                        @if ($mainItems->isNotEmpty())
                            <tr class="totals-row">
                                <td colspan="4" class="text-end">Subtotal</td>
                                <td class="text-end" data-label="Subtotal">
                                    {!! format_money($mainItems->sum('amount')) !!}</td>
                            </tr>
                            <tr class="totals-row">
                                <td colspan="4" class="text-end">Discount</td>
                                <td class="text-end" data-label="Discount">
                                    -{!! format_money(abs($discountItem?->amount ?? 0)) !!}</td>
                            </tr>
                            <tr class="final-total-row fw-bold">
                                <th colspan="4" class="text-end">Total Amount</th>
                                <td class="text-end" data-label="Total Amount">
                                    {!! format_money($invoice->total_amount) !!}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('invoiceStatusSelect');

            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    const newStatus = this.value;
                    const url = this.dataset.updateUrl;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content');

                    this.disabled = true;

                    fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw err;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            // SUCCESS: Trigger your styled success alert here
                            Swal.fire({
                                position: "top-end",
                                title: data.message ||
                                'Status updated!', // Message from controller
                                width: 500,
                                padding: 30,
                                background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-5.jpg') }}) no-repeat center",
                                showConfirmButton: false,
                                timer: 4000,
                                customClass: {
                                    title: 'swal-title-success'
                                }
                            });
                        })
                        .catch(error => {
                            // ERROR: Trigger your styled error alert here
                            console.error('Error updating status:', error);
                            const errorMessage = error.message || 'An unknown error occurred.';

                            Swal.fire({
                                position: "top-end",
                                title: 'Update Failed',
                                text: errorMessage,
                                width: 500,
                                padding: 30,
                                background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-4.jpg') }}) no-repeat center",
                                showConfirmButton: false, // You might want this to be true for errors
                                timer: 5000, // A bit longer for errors
                                customClass: {
                                    title: 'swal-title-error'
                                }
                            });
                        })
                        .finally(() => {
                            this.disabled = false;
                        });
                });
            }
        });
    </script>
@endpush
