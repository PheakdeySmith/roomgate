<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editContractForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <input type="hidden" id="editContractId" name="contract_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_tenant_id" class="form-label">Tenant</label>
                            <select class="form-control select2 @error('user_id') is-invalid @enderror" id="edit_tenant_id" name="user_id" required>
                                <option value="">Select a Tenant</option>
                                @foreach ($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_room_id" class="form-label">Room</label>
                            <select class="form-control select2 @error('room_id') is-invalid @enderror" id="edit_room_id" name="room_id" required>
                                <option value="">Select a Room</option>
                                @foreach ($allRooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->property->name }} - {{ $room->room_number }}</option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="edit_start_date" name="start_date" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="edit_end_date" name="end_date" required>
                             @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_rent_amount" class="form-label">Rent Amount ($)</label>
                            <input type="number" step="0.01" class="form-control @error('rent_amount') is-invalid @enderror" id="edit_rent_amount" name="rent_amount" placeholder="e.g. 500.00">
                            <small class="form-text text-muted">Optional. Leave blank if rent is not applicable.</small>
                            @error('rent_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_billing_cycle" class="form-label">Billing Cycle</label>
                            <select class="form-control select2 @error('billing_cycle') is-invalid @enderror" id="edit_billing_cycle" name="billing_cycle" required>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                                <option value="daily">Daily</option>
                            </select>
                             @error('billing_cycle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control select2 @error('status') is-invalid @enderror" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                                <option value="terminated">Terminated</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="edit_contract_image" class="form-label">Upload New Contract Scan</label>
                            <input type="file" class="form-control" id="edit_contract_image" name="contract_image">
                            <small class="form-text">Leave blank to keep the current file.</small>
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