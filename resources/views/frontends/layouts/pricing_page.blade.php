@extends('frontends.layouts.app')

@section('title', $content['seo']->title ?? 'RoomGate - Home')

@section('content')

    <main class="main-wrapper">
        <section data-w-id="1281549f-ebfa-10c6-64a4-570e19c779ac"
            style="transform: translate3d(0px, 0rem, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;"
            class="section_pricing_hero">
            <div class="padding-global">
                <div class="pricing_hero_box">
                    <div class="section_header is-centered">
                        <div class="section_heading is-centered">
                            <div class="text-style-badge text-color-white">/ Pricing</div>
                            <div class="spacer-xxsmall"></div>
                            <h1 class="max-width-large">Flexible Plans for B2B Teams That Move Fast</h1>
                            <div class="spacer-xsmall"></div>
                            <div class="text-style-muted80">Choose the pricing that fits your pipeline size, sales team
                                structure,
                                and scaling goals.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @include('frontends.layouts.partials.pricing')

        {{-- <section class="section_pricing_plan">
            <div class="padding-global padding-section-medium">
                <div class="container-default">
                    <div data-w-id="8eb2d6e0-36c6-22a8-e033-ed43c169adc8"
                        style="transform: translate3d(0px, 0rem, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;"
                        class="section_header is-centered">
                        <div class="section_heading is-centered">
                            <div class="text-style-badge-5">Plans</div>
                            <div class="spacer-xsmall"></div>
                            <h2>Find the Right Plan</h2>
                        </div>
                    </div>
                    <div class="spacer-large"></div>
                    <div data-w-id="8eb2d6e0-36c6-22a8-e033-ed43c169add1"
                        style="transform: translate3d(0px, 0rem, 0px) scale3d(1, 1, 1) rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg); opacity: 1; transform-style: preserve-3d;"
                        class="pricing_plan_table">
                        <div class="pricing_plan_header">
                            <div id="w-node-_8eb2d6e0-36c6-22a8-e033-ed43c169add3-9fb70f00"
                                class="pricing_plan_header-item">
                                <div class="pricing_plan_heading">
                                    <div class="heading-style-h5">Foundation</div>
                                </div>
                                <div class="pricing_price is-table">
                                    <div class="heading-style-h2">$29</div>
                                    <div class="text-style-muted60">per month</div>
                                </div>
                            </div>
                            <div id="w-node-_8eb2d6e0-36c6-22a8-e033-ed43c169addc-9fb70f00"
                                class="pricing_plan_header-item">
                                <div class="pricing_plan_heading">
                                    <div class="heading-style-h5">Growth</div>
                                    <div class="pricing_plan_header-popular">
                                        <div class="pricing_popular">Most Popular</div>
                                    </div>
                                </div>
                                <div class="pricing_price is-table">
                                    <div class="heading-style-h2">$59</div>
                                    <div class="text-style-muted60">per month</div>
                                </div>
                            </div>
                            <div id="w-node-_8eb2d6e0-36c6-22a8-e033-ed43c169ade8-9fb70f00"
                                class="pricing_plan_header-item">
                                <div class="pricing_plan_heading">
                                    <div class="heading-style-h5">Enterprise</div>
                                </div>
                                <div class="pricing_price is-table">
                                    <div class="heading-style-h2">$149</div>
                                    <div class="text-style-muted60">per month</div>
                                </div>
                            </div>
                        </div>
                        <div class="pricing_plan_row is-first">
                            <div id="w-node-_8eb2d6e0-36c6-22a8-e033-ed43c169adf2-9fb70f00" class="text-weight-medium">
                                Virtual &amp;
                                Physical Cards</div>
                            <div>1</div>
                            <div>3</div>
                            <div>Unlimited</div>
                        </div>
                        <div class="pricing_plan_row">
                            <div id="w-node-_2cad4af8-22e0-9cfd-38d5-2f713317ee9e-9fb70f00" class="text-weight-medium">
                                Custom Fields
                            </div>
                            <div>10</div>
                            <div>20</div>
                            <div>Unlimited</div>
                        </div>
                        <div class="pricing_plan_row is-first">
                            <div id="w-node-_85fbe0ce-070b-7079-aee9-e0922d29dfd6-9fb70f00" class="text-weight-medium">
                                Real-Time
                                Insights</div><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small">
                        </div>
                        <div class="pricing_plan_row">
                            <div id="w-node-_693586fa-81ec-84bb-3c24-652394d75cc7-9fb70f00" class="text-weight-medium">
                                Real-Time
                                Insights</div>
                            <div>3</div>
                            <div>10</div>
                            <div>Unlimited</div>
                        </div>
                        <div class="pricing_plan_row is-first">
                            <div id="w-node-_8eb2d6e0-36c6-22a8-e033-ed43c169adf8-9fb70f00" class="text-weight-medium">Team
                                Collaboration Tools</div><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small opacity0"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small">
                        </div>
                        <div class="pricing_plan_row">
                            <div id="w-node-d49ce697-340b-41d3-1acc-431df46ffea1-9fb70f00" class="text-weight-medium">
                                Third-Party
                                Integrations</div><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small opacity0"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small">
                        </div>
                        <div class="pricing_plan_row is-first">
                            <div id="w-node-_23b9b4d7-8f82-8078-db7f-af4ba479d7fb-9fb70f00" class="text-weight-medium">
                                Dedicated
                                Support</div><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small opacity0"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small">
                        </div>
                        <div class="pricing_plan_row">
                            <div id="w-node-_64af0b62-6e4d-4c7f-2518-7f528af2b1a1-9fb70f00" class="text-weight-medium">
                                Advanced
                                Reporting</div><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small opacity0"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small opacity0"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small">
                        </div>
                        <div class="pricing_plan_row is-first">
                            <div id="w-node-_5c2a18d8-d776-16d8-735c-4860b9f483b3-9fb70f00" class="text-weight-medium">
                                Unlimited
                                Users</div><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small opacity0"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small opacity0"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small">
                        </div>
                        <div class="pricing_plan_row">
                            <div id="w-node-_06dce3bd-d96a-a203-b714-7741fce3cd5a-9fb70f00" class="text-weight-medium">
                                Custom API
                                Access</div><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small opacity0"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small opacity0"><img loading="lazy"
                                src="./pricing_page_files/683979a1a6d345c5e185b01d_Check circle - 1.svg" alt="Yes"
                                class="icon-height-small">
                        </div>
                    </div>
                </div>
            </div>
        </section> --}}

        @include('frontends.layouts.partials.faq')
    </main>

@endsection
