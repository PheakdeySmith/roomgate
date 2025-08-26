<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">{{ __('messages.edit') }} {{ __('messages.amenities') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- NOTE: This form ID 'editRoomTypeForm' matches your JS. The action URL is set dynamically. --}}
            <form id="editRoomTypeForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    {{-- This hidden input is required by your JS to hold the amenity ID --}}
                    {{-- NOTE: The ID 'editRoomTypeId' must match your JS --}}
                    <input type="hidden" id="editRoomTypeId" name="id">

                    <div class="row">
                        <div class="col-md-12 mb-3">
                             {{-- NOTE: This ID 'editName' must match your JS --}}
                            <label for="editName" class="form-label">{{ __('messages.name') }}</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                             {{-- NOTE: This ID 'editDescription' must match your JS --}}
                            <label for="editDescription" class="form-label">{{ __('messages.description') }}</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            {{-- NOTE: This ID 'editAmenityPrice' must match your JS --}}
                            <label for="editAmenityPrice" class="form-label">{{ __('messages.price') }} ($)</label>
                            <input type="number" step="0.01" class="form-control" id="editAmenityPrice" name="amenity_price" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            {{-- NOTE: This ID 'editStatus' must match your JS --}}
                            <label for="editStatus" class="form-label">{{ __('messages.status') }}</label>
                            <select class="form-control select2" id="editStatus" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
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