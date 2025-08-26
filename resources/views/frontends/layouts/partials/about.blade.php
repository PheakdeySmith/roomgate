{{-- We first check if a highlighted feature was passed from the controller --}}
@if(isset($highlightFeature))

<section class="section_home1_about">
    <div class="padding-global padding-section-small">
        <div class="container-default">
            <div data-w-id="226c9872-4f08-dadd-cfb6-78ec322c2464" class="home1_about_grid">
                
                {{-- DYNAMIC IMAGE: Replaced src and alt --}}
                <img src="{{ asset($highlightFeature->image_path) }}"
                     loading="lazy" 
                     alt="{{ $highlightFeature->title }}" 
                     class="home1_about_image" />
                
                <div class="home1_about_content">
                    <div class="text-style-badge">/ Why Flowis</div>
                    <div class="spacer-xxsmall"></div>

                    <h2>{{ $highlightFeature->title }}</h2>
                    <div class="spacer-small"></div>
                    <div>{{ $highlightFeature->description }}</div>
                    <div class="spacer-large"></div>

                    @if($highlightFeature->bullets && count($highlightFeature->bullets) > 0)
                        <div class="list">
                            @foreach($highlightFeature->bullets as $bullet)
                                <div class="divider"></div>
                                <div class="list-item">
                                    <img loading="lazy" src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71095_Checkmark.svg" alt="" class="icon-height-small" />
                                    <div class="text-size-small">{{ $bullet }}</div>
                                </div>
                            @endforeach
                            <div class="divider"></div>
                        </div>
                    @endif

                    <div class="spacer-large"></div>

                    <a href="{{ $highlightFeature->link }}" class="button w-button">See RoomGate in Action</a>
                </div>
            </div>
        </div>
    </div>
</section>

@endif