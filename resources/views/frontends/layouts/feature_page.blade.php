@extends('frontends.layouts.app')

@section('title', $content['feature_intro']->title ?? 'RoomGate - Home')

@section('content')

    <main class="main-wrapper">
        <section class="section_features1_hero">
            <div class="padding-global padding-section-medium">
                <div class="container-default">
                    <div data-w-id="cd564297-a54e-25c4-5da2-e300141abfc5"
                        style="transform: translate3d(0px, 0rem, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;"
                        class="section_header is-centered">
                        <div class="section_heading is-centered">
                            <div class="text-style-badge">{{ $content['features_intro']->subtitle ?? '/ Features' }}</div>
                            <div class="spacer-xxsmall"></div>
                            <h1 class="max-width-large">
                                {{ $content['features_intro']->title ?? 'All Your Tools In One Platform' }}</h1>
                            <div class="spacer-xsmall"></div>
                            <div class="text-style-muted80">
                                {{ $content['features_intro']->content ?? 'Default content...' }}</div>
                            {{-- <div class="spacer-small"></div>
                            <div class="button-group">
                                <a href="#features"
                                    class="button w-button">Explore Our Features</a>
                                <a href="https://flowis-b2b-saas-software-template.webflow.io/utility/demo"
                                class="button is-secondary w-button">Request a Demo</a>
                            </div> --}}
                        <div class="spacer-large"></div>
                    </div>
                </div>

                <div class="spacer-large"></div>
                <div data-w-id="b88ec990-d18a-f3cf-2201-0e0114ec1ae6"
                    style="transform: translate3d(0px, 0rem, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;"
                    class="features1_hero_benefits">

                    @foreach ($benefits as $benefit)
                        <div class="features1_hero_card"><img src="{{ asset($benefit->icon_path) }}" loading="lazy"
                                alt="{{ $benefit->title }}" class="icon-height-large">
                            <div class="text-size-large text-weight-medium">{{ $benefit->title }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            </div>
        </section>


        @foreach ($otherFeatures as $feature)
            <section class="section_features1_details">
                <div class="padding-global padding-section-small">
                    <div class="container-default">
                        <div class="features1_details_grid">

                            @if ($loop->iteration % 2 == 0)
                                @include('frontends.layouts.partials.feature-content', [
                                    'feature' => $feature,
                                    'is_first' => true,
                                ])
                                @include('frontends.layouts.partials.feature-image', [
                                    'feature' => $feature,
                                ])
                            @else
                                @include('frontends.layouts.partials.feature-image', [
                                    'feature' => $feature,
                                ])
                                @include('frontends.layouts.partials.feature-content', [
                                    'feature' => $feature,
                                    'is_first' => false,
                                ])
                            @endif

                        </div>
                    </div>
                </div>
            </section>
        @endforeach


        @include('frontends.layouts.partials.banner')

    </main>

@endsection
