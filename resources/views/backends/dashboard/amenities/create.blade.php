<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">{{ __('messages.create') }} {{ __('messages.amenities') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- The form action points to the correct route for storing an amenity --}}
            <form id="createAmenityForm" method="POST" action="{{ route('landlord.amenities.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">{{ __('messages.name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Wi-Fi, Air Conditioning" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">{{ __('messages.description') }}</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a brief description (optional)"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amenity_price" class="form-label">{{ __('messages.price') }} ($)</label>
                            <input type="number" step="0.01" class="form-control" id="amenity_price" name="amenity_price" placeholder="e.g. 10.00" value="0" required>
                             <small class="form-text text-muted">Enter 0 if the amenity is free.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">{{ __('messages.status') }}</label>
                            <select class="form-control select2" id="status" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.create') }} {{ __('messages.amenities') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>