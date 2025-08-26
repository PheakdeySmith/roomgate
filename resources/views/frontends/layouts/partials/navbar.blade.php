<div data-animation="over-left" class="navbar w-nav" data-easing2="ease" data-easing="ease" data-collapse="medium" role="banner" data-no-scroll="1" data-duration="400" data-doc-height="1">
    <header class="nav-content">
        <div class="nav-background"></div>
        <div class="container w-container">
            <div class="nav-wrapper">
                <a href="/" class="nav-brand w-nav-brand">
                    <img loading="lazy" src="{{ asset('asset_frontend/images/logo-dark.png') }}" alt="RoomGate Logo" class="nav-logo"/>
                </a>
                <nav role="navigation" class="nav-menu w-nav-menu">
                    <a href="{{ route('frontend') }}" class="nav-link w-nav-link">Overview</a>
                    <a href="{{ route('features') }}" class="nav-link w-nav-link">Features</a>
                    <a href="/pricing" class="nav-link w-nav-link">Pricing</a>
                </nav>
                <div class="nav-buttons">
                    <a href="{{ route('register') }}" target="_blank" class="button is-secondary hide-tablet w-button">Register</a>
                    <a href="{{ route('login') }}" class="button hide-tablet w-button">Login</a>
                    <div class="nav-menu-button w-nav-button">
                        <img loading="lazy" src="{{ asset('asset_frontend/images/680b71a3a9815f2cd1c0d00a_Hamburger%20menu.svg') }}" alt="" class="nav-menu-icon"/>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>