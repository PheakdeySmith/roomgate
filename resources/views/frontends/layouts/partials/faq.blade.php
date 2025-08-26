<section class="section_home1_faq">
    <div class="padding-global padding-section-medium">
        <div class="container-default">
            <div class="faq_grid">
                <div data-w-id="30290acf-d6d6-e849-11d7-33e63aca85a3" class="faq_left">
                    <div class="max-width-small">
                        <div class="text-style-badge">/ FAQ</div>
                        <div class="spacer-small"></div>
                        <h2>Everything You Need
                            to Know—Upfront</h2>
                        <div class="spacer-xxsmall"></div>
                        <div>From setup to support and pricing, here are quick answers to the most common questions we
                            get from teams considering Flowis</div>
                        <div class="spacer-small"></div>
                        <div class="spacer-xlarge"></div>
                    </div>
                    <a href="#" class="home1_hero_lightbox w-inline-block w-lightbox">
                        <div data-autoplay="true" data-loop="true" data-wf-ignore="true"
                            class="home1_hero_video w-background-video w-background-video-atom">
                            <video id="a74ced63-dc72-71c7-dc2b-a53869f694c7-video" autoplay="" loop="" muted=""
                                playsinline="" data-wf-ignore="true" data-object-fit="cover">
                                <source src="{{ asset('asset_frontend') }}/images/video.mp4" data-wf-ignore="true" />
                            </video>
                        </div>
                        <div class="play">
                            <img loading="lazy" src="{{ asset('asset_frontend') }}/images/Play.svg" alt=""
                                class="icon-height-small" />
                            <div class="text-color-alternate">Watch Demo</div>
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


                {{-- <div class="faq_box">
                    @foreach($faqs as $faq)
                    <div class="faq_accordion">
                        <div class="faq_question">
                            <div class="faq_question_header">
                                <div>{{ $faq->question }}</div>
                            </div>
                            <div class="faq_icon">
                                <img src="{{ asset('asset_frontend/images/683588d6afb7bd5a9fb71090_Plus%204.svg') }}"
                                    alt="" class="icon-height-small" />
                            </div>
                        </div>
                        <div class="faq_answer">
                            <div class="faq_answer-wrapper">
                                <p class="text-size-small text-style-muted60">{{ $faq->answer }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div> --}}
                <div data-w-id="30290acf-d6d6-e849-11d7-33e63aca85b5" class="faq_box">
                    @foreach($faqs as $faq)
                        <div data-w-id="25c4837b-3530-41a4-d13b-6cd7954f94bc" class="faq_accordion">

                            <div class="faq_question">
                                <div class="faq_question_header">
                                    <div>{{ $faq->question }}</div>
                                </div>
                                <div class="faq_icon">
                                    <img loading="lazy"
                                        src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb71090_Plus%204.svg"
                                        alt="" class="icon-height-small" />
                                </div>
                            </div>
                            <div class="faq_answer">
                                <div class="faq_answer-wrapper">
                                    <div class="max-width-large">
                                        <p class="text-size-small text-style-muted60">{{ $faq->answer }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

