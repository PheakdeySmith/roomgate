<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Add New Utility Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- The form now points to the utilityTypes.store route --}}
            <form id="createUtilityTypeForm" method="POST" action="{{ route('admin.utility_types.store') }}">
                @csrf
                <div class="modal-body">

                    {{-- Name Field --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" placeholder="e.g. Electricity, Water, Internet" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            {{-- Unit of Measure Field --}}
                            <label for="unit_of_measure" class="form-label">Unit of Measure</label>
                            <input type="text" class="form-control @error('unit_of_measure') is-invalid @enderror"
                                   id="unit_of_measure" name="unit_of_measure" placeholder="e.g. kWh, m³, Monthly" required>
                            @error('unit_of_measure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            {{-- Billing Type Field --}}
                            <label for="billing_type" class="form-label">Billing Type</label>
                            <select class="form-select @error('billing_type') is-invalid @enderror" id="billing_type" name="billing_type" required>
                                <option value="metered" selected>Metered</option>
                                <option value="flat_rate">Flat Rate</option>
                            </select>
                            @error('billing_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Utility Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
