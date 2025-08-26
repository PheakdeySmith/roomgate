<section class="section_home1_features">
    <div class="padding-global padding-section-medium">
        <div class="container-default">
            <div class="home1_features_grid">
                <div data-w-id="702d7d02-b5ea-c727-64e6-3f0b8fbbaeb0"
                    style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0"
                    class="home1_features_left">
                    <div class="max-width-small">
                        <div class="text-style-badge">{{ $content['features_intro']->subtitle ?? '/ Features' }}</div>
                        <div class="spacer-small"></div>
                        <h2>{{ $content['features_intro']->title ?? 'All Your Tools In One Platform' }}</h2>
                        <div class="spacer-xxsmall"></div>
                        <div>{{ $content['features_intro']->content ?? 'Default content...' }}</div>
                        <div class="spacer-small"></div>
                        <a href="{{ route('features') }}" class="button w-button">All
                            Features</a>
                        <div class="spacer-xlarge"></div>

                    </div>
                </div>
                <div data-w-id="e2583658-f811-8629-a888-a4a7f6711c9a"
                    style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0"
                    class="home1_features_right">

                    {{-- This will be the home1_features_right section --}}

                    @foreach($otherFeatures->take(3) as $feature)

                        <div class="home1_features_card">
                            <h3 class="heading-style-h5">{{ $feature->title }}</h3>
                            <div class="spacer-xxsmall"></div>
                            <div class="text-style-muted60">{{ $feature->description }}</div>
                            <div class="spacer-xsmall"></div>
                            <a data-w-id="0f6a1d6b-9f72-a276-9205-3603d5e0005e" href="{{ route('features') }}"
                                class="button-arrow w-inline-block">
                                <div class="text-size-medium">Learn More</div>
                                <div style="-webkit-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform-style:preserve-3d"
                                    class="button is-icon is-secondary">
                                    <img src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71081_Right%20-%206.svg"
                                        loading="lazy" alt="" class="icon-height-small" />
                                </div>
                            </a>
                            <div class="spacer-xsmall"></div>
                            <img src="{{ asset($feature->image_path) }}" loading="lazy" alt="{{ $feature->title }}"
                                class="home1_features_image" />
                        </div>

                    @endforeach

                </div>
            </div>
        </div>
    </div>
</section>