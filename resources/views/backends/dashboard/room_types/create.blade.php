<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">{{ __('messages.create') }} {{ __('messages.room_type') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="createRoomTypeForm" method="POST" action="{{ route('landlord.room_types.store') }}">
                @csrf
                <div class="modal-body">

                    {{-- Name and Capacity Fields --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">{{ __('messages.name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                placeholder="e.g. Deluxe King, Standard Double">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">{{ __('messages.capacity') }} (Max Guests)</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required
                                placeholder="e.g. 2">
                        </div>
                    </div>

                    {{-- Description Field --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('messages.description') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Enter a brief description of the room type (optional)"></textarea>
                    </div>

                    <hr>

                    {{-- Amenities Checklist Section --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.amenities') }}</label>
                        <p class="text-muted small">Select the standard amenities that come with this room type.</p>
                        
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.create') }} {{ __('messages.room_type') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>