<section class="section_home1_hero">
    <div class="padding-global">
        <div class="home1_hero_grid">
            <div class="home1_hero_content">
                <div>
                    <div class="text-style-badge text-color-white">
                        {{ $content['hero']->subtitle ?? '/ Welcome to RoomGate' }}
                    </div>
                    <div class="spacer-small"></div>
                    <h1>{{ $content['hero']->title ?? 'Room Rental System That Powers Real Growth' }}</h1>
                    <div class="spacer-xsmall"></div>
                    <div class="max-width-medium text-style-muted80">
                        {{ $content['hero']->content ?? 'Connecting landlords and tenants for a seamless and transparent rental experience.' }}
                    </div>
                    <div class="spacer-small"></div>
                    <div class="button-group">
                        <a href="{{ $content['hero']->button_link ?? '#' }}"
                            class="button is-secondary w-button">{{ $content['hero']->button_text ?? 'Start Free Trial' }}</a>
                        <a href="{{ route('features') }}" class="button is-outline w-button">Explore Features</a>
                    </div>
                    <div class="spacer-small"></div>
                    <div class="home1_hero_reviews">
                        <div class="home1_hero_reviews-authors">
                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/avatar1.avif"
                                alt="B2B SaaS software Webflow template avatar1" class="home1_hero_image-2 is-first" />
                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/avatar2.avif"
                                alt="B2B SaaS software Webflow template avatar2" class="home1_hero_image-2" />
                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/avatar4.avif"
                                alt="B2B SaaS software Webflow template avatar4" class="home1_hero_image-2" />
                        </div>
                        <img loading="lazy" src="{{ asset('asset_frontend') }}/images/reviews_star.png" alt="5 Stars"
                            class="home1_hero_reviews-stars" />
                        <div class="text-size-tiny text-weight-medium">4.8/5</div>
                        <div class="text-size-tiny text-weight-medium">610+ Reviews</div>
                    </div>
                    <div class="spacer-medium"></div>
                </div>
                <a href="#" class="home1_hero_lightbox w-inline-block w-lightbox">
                    <div data-autoplay="true" data-loop="true" data-wf-ignore="true"
                        class="home1_hero_video w-background-video w-background-video-atom">
                        <video id="a74ced63-dc72-71c7-dc2b-a53869f694c7-video" autoplay="" loop=""
                            muted="" playsinline="" data-wf-ignore="true" data-object-fit="cover">
                            <source src="{{ asset('asset_frontend') }}/images/video.mp4" data-wf-ignore="true" />
                        </video>
                    </div>
                    <div class="play">
                        <img loading="lazy" src="{{ asset('asset_frontend') }}/images/Play.svg" alt=""
                            class="icon-height-small" />
                        <div class="text-color-alternate">See in action</div>
                    </div>

                    @php
                        $videoId = getYouTubeId($content['hero']->video_url ?? '');
                    @endphp

                    <script type="application/json" class="w-json">
                        {
                            "items": [
                                {
                                    "url": "{{ $content['hero']->video_url ?? '' }}",
                                    "originalUrl": "{{ $content['hero']->video_url ?? '' }}",
                                    "width": 940,
                                    "height": 528,
                                    "thumbnailUrl": "https://i.ytimg.com/vi/{{ $videoId }}/hqdefault.jpg",
                                    "html": "<iframe class=\"embedly-embed\" src=\"//cdn.embedly.com/widgets/media.html?src=https%3A%2F%2Fwww.youtube.com%2Fembed%2F{{ $videoId }}%3Ffeature%3Doembed&url={{ urlencode($content['hero']->video_url ?? '') }}&display_name=YouTube&key=96f1f04c5f4143bcb0f2e68c87d65feb&type=text%2Fhtml&schema=youtube\" width=\"940\" height=\"528\" scrolling=\"no\" title=\"YouTube embed\" frameborder=\"0\" allow=\"autoplay; fullscreen\" allowfullscreen=\"true\"></iframe>",
                                    "type": "video"
                                }
                            ],
                            "group": ""
                        }
                    </script>
                </a>
            </div>
            <div class="home1_hero_image-box">
                <img src="{{ asset($content['hero']->image_path ?? 'asset_frontend/images/dashboard.png') }}"
                    loading="lazy" sizes="(max-width: 1446px) 100vw, 1446px"
                    srcset="{{ asset('asset_frontend/images/dashboard-p-500.avif') }} 500w, 
                             {{ asset('asset_frontend/images/dashboard-p-800.avif') }} 800w, 
                             {{ asset('asset_frontend/images/dashboard.avif') }} 1446w"
                    alt="RoomGate dashboard" class="home1_hero_image" />
            </div>
        </div>
    </div>
</section>
