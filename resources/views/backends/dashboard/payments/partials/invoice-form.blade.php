
<div class="card position-relative">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <form id="invoice-form-{{ $type }}" novalidate>
        <div class="card-body">
            <div class="row g-3 align-items-center mb-4">
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-secondary back-to-selection-btn" aria-label="Back to selection">
                        <i class="ti ti-arrow-left"></i>
                        <span class="d-none d-sm-inline ms-1">Back</span>
                    </button>
                </div>
                <div class="col text-center">
                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt="Company Logo" height="50">
                </div>
                <div class="col-auto" style="min-width: 190px;">
                    <div class="input-group">
                        <label for="invoice_number_{{ $type }}" class="input-group-text fw-bold">#</label>
                        <input type="text" id="invoice_number_{{ $type }}" class="form-control invoice-no-input" value="{{ $invoiceNumber }}" readonly>
                    </div>
                </div>
            </div>
            <hr>

            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="contract_id_{{ $type }}" class="form-label">Select Contract</label>
                        <select id="contract_id_{{ $type }}" name="contract_id" class="form-select contract-select" required>
                            <option value="" selected disabled>Choose a contract...</option>
                            @foreach ($contracts as $contract)
                                <option value="{{ $contract->id }}">
                                    Room {{ $contract->room->room_number }} - {{ $contract->tenant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="room_number_{{ $type }}" class="form-label">Room Number</label>
                        <input type="text" id="room_number_{{ $type }}" class="form-control room-number-input" readonly>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="issue_date_{{ $type }}" class="form-label">Invoice Date</label>
                            <input type="date" id="issue_date_{{ $type }}" name="issue_date" class="form-control issue-date-input" value="{{ $issueDate }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="due_date_{{ $type }}" class="form-label">Due Date</label>
                            <input type="date" id="due_date_{{ $type }}" name="due_date" class="form-control due-date-input" value="{{ $dueDate }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Included Amenities</label>
                        <div class="border rounded p-3 bg-light amenities-display" style="min-height: 80px;">
                            <small class="text-muted">Select a contract to see amenities.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Invoice Items (Responsive Toggle) --}}
            {{-- Desktop Table --}}
            <div class="d-none d-md-block">
                @include('backends.dashboard.payments.partials.invoice-table', ['type' => $type])
            </div>
            {{-- Mobile Card List --}}
            <div class="d-block d-md-none accordion" id="invoiceItemsAccordion-{{$type}}">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseItems-{{$type}}">
                            <strong>Invoice Items</strong>
                        </button>
                    </h2>
                    <div id="collapseItems-{{$type}}" class="accordion-collapse collapse show">
                        <div class="accordion-body p-0">
                            <div class="invoice-items-list-mobile p-3">
                                <p class="text-muted text-center m-0">Please select a contract to load invoice items.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4: Invoice Summary --}}
            <div class="row justify-content-end mt-3">
                <div class="col-lg-5 col-md-8 invoice-summary-wrapper">
                    @include('backends.dashboard.payments.partials.invoice-summary')
                </div>
            </div>
        </div>

        {{-- Section 5: Form Actions --}}
        <div class="card-footer text-center">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <button type="button" class="btn btn-outline-primary gap-1 preview-btn" data-type="{{ $type }}">
                    <i class="ti ti-eye fs-16"></i> Preview
                </button>
                <button type="submit" class="btn btn-success gap-1">
                    <i class="ti ti-device-floppy fs-16"></i> Save Invoice
                </button>
            </div>
        </div>
    </form>
</div>