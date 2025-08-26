<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Utility Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- The form's ID and action are now for utility types. The action URL will be set by JavaScript. --}}
            <form id="editUtilityTypeForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    
                    {{-- Name Field --}}
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="editName" name="name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            {{-- Unit of Measure Field --}}
                            <label for="editUnitOfMeasure" class="form-label">Unit of Measure</label>
                            <input type="text" class="form-control @error('unit_of_measure') is-invalid @enderror"
                                   id="editUnitOfMeasure" name="unit_of_measure" required>
                            @error('unit_of_measure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                             {{-- Billing Type Field --}}
                            <label for="editBillingType" class="form-label">Billing Type</label>
                            <select class="form-select @error('billing_type') is-invalid @enderror" id="editBillingType" name="billing_type" required>
                                <option value="metered">Metered</option>
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
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>