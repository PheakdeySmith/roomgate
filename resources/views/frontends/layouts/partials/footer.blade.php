<footer class="footer">
    <div class="container-default">
        <div class="footer-box">
            <div class="spacer-large"></div>
            <nav class="footer_nav">
                <div class="footer_nav-column">
                    <div class="text-style-badge">Pages</div>
                    <div class="footer_nav-list">
                        <a href="/home-v1" aria-current="page" class="footer_nav-link w--current">Home</a>
                        <a href="/about" class="footer_nav-link">About</a>
                        <a href="/contact" class="footer_nav-link">Contact</a>
                        <a href="{{ route('login') }}" class="footer_nav-link">Sign in</a>
                        <a href="{{ route('register') }}" class="footer_nav-link">Sign up</a>
                    </div>
                </div>
                <div class="footer_nav-column">
                    <div class="text-style-badge">Features</div>
                    <div class="footer_nav-list">
                        <a href="{{ route('features') }}" class="footer_nav-link">Features</a>
                        <a href="{{ route('features') }}" class="footer_nav-link">Benefits</a>
                    </div>
                </div>
                <div class="footer_nav-column">
                    <div class="text-style-badge">Product</div>
                    <div class="footer_nav-list">
                        <a href="{{ route('pricing') }}" class="footer_nav-link">Pricing</a>
                        <a href="{{ route('register') }}" class="footer_nav-link">Try For Free</a>
                    </div>
                </div>
                <div class="footer_nav-column">
                    <div class="text-style-badge">Terms &amp; Conditions</div>
                    <div class="footer_nav-list">
                        <a href="{{ route('terms')}}" class="footer_nav-link">Terms &amp; Conditions</a>
                        <a href="/utility/terms" class="footer_nav-link">Support</a>
                    </div>
                </div>
            </nav>
            <div class="spacer-large"></div>
            <div class="footer_info">
                <div class="text-size-tiny">
                    <script>document.write(new Date().getFullYear())</script> © RoomGate
                </div>
                <div class="footer_socials">
                    <a href="#" class="social-link w-inline-block">
                        <img loading="lazy"
                            src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb70f4f_linkedin.svg"
                            alt="linkedin" class="social-link-image" />
                    </a>
                    <a href="#" class="social-link w-inline-block">
                        <img loading="lazy"
                            src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb70f56_instagram.svg"
                            alt="instagram" class="social-link-image" />
                    </a>
                    <a href="#" class="social-link w-inline-block">
                        <img loading="lazy"
                            src="{{ asset('asset_frontend') }}/images/683588d6afb7bd5a9fb70f46_twitter.svg"
                            alt="twitter" class="social-link-image" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
