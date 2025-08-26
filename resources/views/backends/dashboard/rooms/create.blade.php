<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createRoomForm" method="POST" action="{{ route('landlord.rooms.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        
                        <div class="col-md-6 mb-3">
                            <label for="property_id" class="form-label">Property Type</label>
                            <select class="form-control" id="property_id" name="property_id" required>
                                <option value="" disabled selected>Select a property...</option>
                                @foreach ($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="room_type_id" class="form-label">Property Type</label>
                            <select class="form-control" id="room_type_id" name="room_type_id" required>
                                <option value="" disabled selected>Select a type...</option>
                                @foreach ($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="room_number" class="form-label">Room Number / Name</label>
                            <input type="text" class="form-control" id="room_number" name="room_number"
                                placeholder="e.g. 101, P-02" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="floor" class="form-label">Floor</label>
                            <input type="number" class="form-control" id="floor" name="floor"
                                placeholder="e.g. 1, 2">
                        </div>
                    </div>

                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <label for="size" class="form-label">Size (e.g., sqm or sqft)</label>
                            <input type="text" class="form-control" id="size" name="size" placeholder="e.g. 45 sqm">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control select2" id="status" name="status" required>
                                <option value="available" selected>Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Enter a brief description of the room"></textarea>
                    </div>

                    {{-- Amenities Checklist Section --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.amenities') }}</label>
                        
                        <div class="row">

                            @forelse ($amenities as $amenity)
                                <div class="col-md-4 col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="create_amenity_{{ $amenity->id }}">
                                        <label class="form-check-label" for="create_amenity_{{ $amenity->id }}">
                                            {{ $amenity->name }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">No amenities have been created yet. Please create an amenity first.</p>
                                </div>
                            @endforelse

                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Room</button>
                </div>
            </form>
        </div>
    </div>
</div>