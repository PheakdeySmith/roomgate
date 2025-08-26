@extends('frontends.layouts.app')

@section('title', $content['feature_intro']->title ?? 'RoomGate - Home')

@section('content')

    <main class="main-wrapper">
        <section class="section_terms">
            <div class="padding-global padding-section-medium">
                <div class="container-medium">
                    <div class="terms_header">
                        <div class="text-style-badge">{{ $content['terms-and-conditions']->subtitle ?? '/ Terms' }}</div>

                        {{-- Display the title from the database --}}
                        <h1 class="heading-style-h2">{{ $content['terms-and-conditions']->title ?? 'Terms & Conditions' }}
                        </h1>

                        {{-- You can make this dynamic too if needed --}}
                        <div>Last updated: {{ $content['terms-and-conditions']->updated_at->format('F d, Y') }}</div>
                    </div>

                    <div class="spacer-xxsmall"></div>

                    {{-- Keep your image static or make it dynamic from the DB --}}
                    <div class="spacer-xxsmall"></div><img class="terms_image"
                        src="{{ asset('asset_frontend') }}/images/terms.avif"
                        alt="B2B SaaS software Webflow template terms"
                        style="transform: translate3d(0px, 0rem, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;"
                        sizes="(max-width: 981px) 100vw, 981px" data-w-id="92546ecf-656b-0260-4073-6d8ce5d504dc"
                        loading="lazy"
                        srcset="{{ asset('asset_frontend') }}/images/terms-p-500.avif 500w, {{ asset('asset_frontend') }}/images/terms.avif 981w">

                    <div class="spacer-large"></div>

                    {{-- You can store this intro separately or include it in the main content --}}
                    <div class="heading-style-h4">By accessing or using RoomGate, you agree to be bound by these Terms and our
                        Privacy Policy. If you do not agree, please discontinue use of the service.</div>

                    <div class="spacer-large"></div>

                    {{-- Render the rich-text HTML content from the database --}}
                    <div class="text-rich-text w-richtext">
                        {!! $content['terms-and-conditions']->content ?? 'Content' !!}
                    </div>

                </div>
            </div>
        </section>
    </main>

@endsection