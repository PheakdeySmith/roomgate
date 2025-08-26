<div class="features1_details_content {{ $is_first ? 'is-first' : '' }}">
    <div class="text-style-badge">/ Feature</div>
    <div class="spacer-xxsmall"></div>
    <h2>{{ $feature->title }}</h2>
    <div class="spacer-small"></div>
    <div class="max-width-medium">{{ $feature->description }}</div>
    <div class="spacer-large"></div>
    <div class="list">
        {{-- Loop through the 'bullets' array from your database --}}
        @if($feature->bullets)
            @foreach($feature->bullets as $bullet)
            <div class="divider"></div>
            <div class="list-item">
                <img loading="lazy" src="{{ asset('asset_frontend/images/683588d6afb7bd5a9fb71095_Checkmark.svg') }}" alt="Checkmark" class="icon-height-small">
                <div class="text-size-small">{{ $bullet }}</div>
            </div>
            @endforeach
        @endif
        <div class="divider"></div>
    </div>
    <div class="spacer-large"></div>
    <a href="{{ $feature->link }}" class="button w-button">Get Started</a>
</div>