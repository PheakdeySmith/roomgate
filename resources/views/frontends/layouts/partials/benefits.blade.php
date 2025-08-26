
<section class="section_home1_benefits">
    <div class="padding-global padding-section-small">
        <div class="container-default">
            <div data-w-id="c986fbf3-b9e6-ad42-2cda-26d4387bd7f5"
                style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0"
                class="section_header is-centered">
                <div class="section_heading">
                    <div class="text-style-badge">{{ $content['benefits_intro']->subtitle ?? '/ Benefits' }}</div>
                    <div class="spacer-xxsmall"></div>
                    <h2>{{ $content['benefits_intro']->title ?? 'Why Choose RoomGate' }}</h2>
                </div>
            </div>
            <div class="spacer-medium"></div>
            <div data-w-id="9faa6f1a-38c0-72ae-3a77-972386a2669e"
                style="-webkit-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 1.5rem, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);opacity:0"
                class="home1_benefits_grid">
                <img src="{{ asset($content['benefits_intro']->image_path) }}"
                    loading="lazy" id="w-node-_97fbcd01-05f4-4cf2-b6ae-bb7b5204ad5e-4f6e2f6e"
                    alt="{{ $content['benefits_intro']->title ?? 'Why Choose RoomGate' }}" class="home1_benefits_image" />
                
                @foreach($benefits as $benefit)
                    <div class="home1_benefits_card">
                        <img src="{{ asset($benefit->icon_path) }}" loading="lazy"
                            alt="{{ $benefit->title }}" class="icon-height-huge" />
                        <div class="spacer-medium"></div>
                        <div>
                            <div>{{ $benefit->title }}</div>
                            <div class="text-style-muted60">{{ $benefit->description }}
                            </div>
                        </div>
                    </div>
                @endforeach
                
            </div>
        </div>
    </div>
</section>