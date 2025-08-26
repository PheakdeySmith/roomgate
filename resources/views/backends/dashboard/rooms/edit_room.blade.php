<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editRoomForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <input type="hidden" id="editRoomId" name="room_id">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="editRoomNumber" class="form-label">Room Number / Name</label>
                            <input type="text" class="form-control @error('room_number') is-invalid @enderror"
                                id="editRoomNumber" name="room_number" required>
                            @error('room_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_room_type_id" class="form-label">Room Type</label>
                            <select class="form-control @error('room_type_id') is-invalid @enderror"
                                id="edit_room_type_id" name="room_type_id" required>
                                <option value="">Select a Room Type</option>
                                @foreach ($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                                @endforeach
                            </select>
                            @error('room_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="editFloor" class="form-label">Floor</label>
                            <input type="number" class="form-control @error('floor') is-invalid @enderror"
                                id="editFloor" name="floor">
                            @error('floor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editSize" class="form-label">Size</label>
                            <input type="text" class="form-control @error('size') is-invalid @enderror" id="editSize"
                                name="size">
                            @error('size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="editDescription"
                            name="description" rows="3"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="editStatus"
                                name="status" required>
                                <option value="" disabled>Select a country...</option>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    {{-- --- NEW AMENITIES SECTION --- --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.amenities') }}</label>
                        <div class="row">
                            @forelse ($amenities as $amenity)
                                <div class="col-md-4 col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]"
                                            value="{{ $amenity->id }}" id="edit_amenity_{{ $amenity->id }}">
                                        <label class="form-check-label" for="edit_amenity_{{ $amenity->id }}">
                                            {{ $amenity->name }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">No amenities available.</p>
                                </div>
                            @endforelse
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