@extends('backends.layouts.app')

@section('title', 'Contract Details')

@push('style')
    {{-- This custom CSS creates the responsive tab and timeline styles --}}
    <style>
        .nav-pills .nav-link {
            text-align: center;
        }

        .timeline-container {
            position: relative;
            padding-left: 30px;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 5px;
            bottom: 5px;
            width: 2px;
            background-color: var(--bs-border-color);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-pin {
            position: absolute;
            left: -30px;
            top: 2px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid var(--bs-body-bg);
        }

        .timeline-pin i {
            font-size: 14px;
            color: #fff;
        }

        .timeline-content {
            margin-left: 15px;
        }
        
        /* Document cards styling */
        .avatar-sm {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .avatar-lg {
            width: 5rem;
            height: 5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
    <div class="page-container">
        {{-- ======================= Page Header ======================= --}}
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Contract Details</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('landlord.contracts.index') }}">Contracts</a></li>
                    <li class="breadcrumb-item active">#{{ str_pad($contract->id, 6, '0', STR_PAD_LEFT) }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card bg-primary-subtle border-0">
                    <div class="card-body">
                        {{-- Responsive header: Stacks on mobile, row on desktop --}}
                        <div class="d-flex flex-column flex-lg-row align-items-center">
                            <img src="{{ $contract->tenant->image ? asset($contract->tenant->image) : asset('assets/images/default_image.png') }}"
                                class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                            <div class="ms-lg-3 mt-3 mt-lg-0 text-center text-lg-start flex-grow-1">
                                <h3 class="mb-0">{{ $contract->tenant->name }}</h3>
                                <p class="text-muted mb-0">
                                    Contract #{{ str_pad($contract->id, 6, '0', STR_PAD_LEFT) }} for Room
                                    {{ $contract->room->room_number }}
                                </p>
                            </div>
                            <div class="d-flex gap-2 mt-3 mt-lg-0 flex-shrink-0">
                                <a href="{{ route('landlord.payments.create', ['contract_id' => $contract->id]) }}"
                                    class="btn btn-primary"><i class="ti ti-plus me-1"></i>New Invoice</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- 2x2 grid on mobile, 4-across on desktop --}}
            <div class="col-6 col-lg-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-2">Monthly Rent</p>
                        <h4 class="mb-0">{!! format_money($totalMonthlyRent) !!}</h4>
                        <small class="text-muted">
                            Base: {!! format_money($rentAmount) !!} + 
                            Amenities: {!! format_money($contract->room->amenities->sum('amenity_price')) !!}
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-2">Current Balance</p>
                        <h4 class="mb-0 {{ $currentBalance > 0 ? 'text-danger' : 'text-success' }}">
                            {!! format_money($currentBalance) !!}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-2">Status</p>
                        <h4 class="mb-0 text-success">{{ ucfirst($contract->status) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted mb-2">Days Remaining</p>
                        <h4 class="mb-0">{{ $daysRemaining }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills card-header-pills" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#details-pane"
                            role="tab">
                            <i class="ti ti-file-text d-block d-lg-none fs-4"></i>
                            <span class="d-none d-lg-block">Details</span>
                        </a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#invoices-pane" role="tab">
                            <i class="ti ti-file-invoice d-block d-lg-none fs-4"></i>
                            <span class="d-none d-lg-block">Invoices</span>
                        </a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#usage-pane" role="tab">
                            <i class="ti ti-bolt d-block d-lg-none fs-4"></i>
                            <span class="d-none d-lg-block">Utility Usage</span>
                        </a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#documents-pane" role="tab">
                            <i class="ti ti-files d-block d-lg-none fs-4"></i>
                            <span class="d-none d-lg-block">Documents</span>
                        </a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane show active" id="details-pane" role="tabpanel">
                        <h5 class="mb-3">Contract Information</h5>
                        <dl class="row">
                            <dt class="col-sm-3">Start Date</dt>
                            <dd class="col-sm-9">{{ $contract->start_date->format('F d, Y') }}</dd>
                            <dt class="col-sm-3">End Date</dt>
                            <dd class="col-sm-9">{{ $contract->end_date->format('F d, Y') }}</dd>
                            <dt class="col-sm-3">Duration</dt>
                            <dd class="col-sm-9">
                                {{ $contract->start_date->diff($contract->end_date)->format('%y years, %m months, %d days') }}
                            </dd>
                            <dt class="col-sm-3">Billing Cycle</dt>
                            <dd class="col-sm-9">{{ ucfirst($contract->billing_cycle) }}</dd>
                        </dl>
                        <hr>
                        <h5 class="mb-3">Property Information</h5>
                        <dl class="row">
                            <dt class="col-sm-3">Property</dt>
                            <dd class="col-sm-9">{{ $contract->room->property->name }}</dd>
                            <dt class="col-sm-3">Room</dt>
                            <dd class="col-sm-9">{{ $contract->room->room_number }}
                                ({{ $contract->room->roomType->name }})</dd>
                            <dt class="col-sm-3">Address</dt>
                            <dd class="col-sm-9">{{ $contract->room->property->address }}</dd>
                        </dl>
                    </div>

                    <div class="tab-pane" id="invoices-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($invoices as $invoice)
                                        <tr>
                                            <td><a href="#"
                                                    class="text-dark fw-semibold">{{ $invoice->invoice_number }}</a></td>
                                            <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                                            <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                            <td class="text-end">{!! format_money($invoice->total_amount) !!}</td>
                                            <td class="text-center"><span
                                                    class="badge bg-primary-subtle text-primary">{{ ucfirst($invoice->status) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted p-4">No invoices found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $invoices->links('vendor.pagination.custom-pagination') }}</div>
                    </div>

                    <div class="tab-pane" id="usage-pane" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>Billing Period</th>
                                        <th>Utility</th>
                                        <th>Consumption</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($utilityHistory as $usage)
                                        <tr>
                                            <td>{{ $usage->billing_period_start->format('M Y') }}</td>
                                            <td>{{ $usage->utilityType->name }}</td>
                                            <td>{{ $usage->consumption }} {{ $usage->utilityType->unit_of_measure }}</td>
                                            <td class="text-end">{!! format_money($usage->amount) !!}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted p-4">No utility history
                                                found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $utilityHistory->links('vendor.pagination.custom-pagination') }}</div>
                    </div>

                    <div class="tab-pane" id="documents-pane" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Tenant Documents</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadContractDocumentModal">
                                <i class="ti ti-upload me-1"></i> Upload Document
                            </button>
                        </div>
                        
                        @if(isset($tenantDocuments) && count($tenantDocuments) > 0)
                            <div class="row">
                                @foreach($tenantDocuments as $document)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="avatar-sm bg-primary-subtle rounded me-3">
                                                        <i class="ti ti-file-text text-primary fs-24 d-flex justify-content-center align-items-center h-100"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 text-truncate">{{ $document->name }}</h6>
                                                        <p class="text-muted mb-0 small">{{ $document->created_at->format('M d, Y') }}</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    @php
                                                        $badgeClass = 'bg-primary-subtle text-primary';
                                                        if ($document->type == 'id') {
                                                            $badgeClass = 'bg-success-subtle text-success';
                                                        } elseif ($document->type == 'contract') {
                                                            $badgeClass = 'bg-warning-subtle text-warning';
                                                        } elseif ($document->type == 'proof_of_address') {
                                                            $badgeClass = 'bg-info-subtle text-info';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $document->type)) }}</span>
                                                </div>
                                                
                                                @if($document->description)
                                                    <p class="text-muted small mb-3">{{ Str::limit($document->description, 100) }}</p>
                                                @endif
                                                
                                                <div class="d-flex justify-content-end">
                                                    <a href="{{ asset($document->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="ti ti-eye me-1"></i> View
                                                    </a>
                                                    <a href="{{ route('tenant.document.download', $document->id) }}" class="btn btn-sm btn-primary ms-2">
                                                        <i class="ti ti-download me-1"></i> Download
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger ms-2" 
                                                        onclick="confirmDeleteDocument({{ $document->id }})">
                                                        <i class="ti ti-trash me-1"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-light text-primary rounded-circle">
                                        <i class="ti ti-file-off fs-24"></i>
                                    </div>
                                </div>
                                <h5>No Documents Found</h5>
                                <p class="text-muted">This tenant hasn't uploaded any documents yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadContractDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('landlord.contracts.document.upload', $contract->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="document_name" class="form-label">Document Name</label>
                        <input type="text" class="form-control" id="document_name" name="document_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type</label>
                        <select class="form-select" id="document_type" name="document_type" required>
                            <option value="">Select Type</option>
                            <option value="id">ID Document</option>
                            <option value="contract">Contract</option>
                            <option value="proof_of_address">Proof of Address</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="document_file" class="form-label">Document File</label>
                        <input type="file" class="form-control" id="document_file" name="document_file" required>
                        <div class="form-text">Accepted formats: PDF, JPG, JPEG, PNG (Max: 10MB)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="document_description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="document_description" name="document_description" rows="3"></textarea>
                    </div>
                    
                    <input type="hidden" name="tenant_id" value="{{ $contract->user_id }}">
                    <input type="hidden" name="room_id" value="{{ $contract->room_id }}">
                    <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeleteDocument(documentId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This document will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form for document deletion
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/documents/' + documentId;
                form.style.display = 'none';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
