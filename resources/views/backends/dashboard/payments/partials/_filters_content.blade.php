<div class="row">
    {{-- Filter Group 1: Property --}}
    <div class="col-md-4 border-end-md">
        <div class="filter-group">
            <h6 class="text-uppercase text-muted fs-13">Property</h6>
            <div class="filter-option-list" data-filter-group="property_id">
                <a href="#" class="filter-option @if(!request('property_id')) active @endif" data-value="">All Properties</a>
                @foreach ($properties as $property)
                    <a href="#" class="filter-option @if(request('property_id') == $property->id) active @endif" data-value="{{ $property->id }}">{{ $property->name }}</a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filter Group 2: Room Type --}}
    <div class="col-md-4 border-end-md mt-3 mt-md-0">
        <div class="filter-group">
            <h6 class="text-uppercase text-muted fs-13">Room Type</h6>
            <div class="filter-option-list" data-filter-group="room_type_id">
                <a href="#" class="filter-option @if(!request('room_type_id')) active @endif" data-value="">All Room Types</a>
                @foreach ($roomTypes as $roomType)
                    <a href="#" class="filter-option @if(request('room_type_id') == $roomType->id) active @endif" data-value="{{ $roomType->id }}">{{ $roomType->name }}</a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filter Group 3: Status (with corrected values) --}}
    <div class="col-md-4 mt-3 mt-md-0">
        <div class="filter-group">
            <h6 class="text-uppercase text-muted fs-13">Status</h6>
            <div class="filter-option-list" data-filter-group="status">
                <a href="#" class="filter-option @if(!request('status') || request('status') == 'any-status') active @endif" data-value="any-status">Any Status</a>
                <a href="#" class="filter-option @if(request('status') == 'paid') active @endif" data-value="paid">Paid</a>
                <a href="#" class="filter-option @if(request('status') == 'overdue') active @endif" data-value="overdue">Overdue</a>
                <a href="#" class="filter-option @if(request('status') == 'sent') active @endif" data-value="sent">Sent</a>
                <a href="#" class="filter-option @if(request('status') == 'draft') active @endif" data-value="draft">Draft</a>
                <a href="#" class="filter-option @if(request('status') == 'void') active @endif" data-value="void">Void</a>
            </div>
        </div>
    </div>
</div>