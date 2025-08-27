{{-- @include('frontends.layouts.partials.pricing') --}}

<style>
    /* --- Billing Toggle Styles --- */
    .billing-toggle-wrapper { display: flex; justify-content: center; }
    .billing-toggle { display: inline-flex; background-color: #e9ecef; border-radius: 99px; padding: 5px; }
    .toggle-button { padding: 8px 20px; border: none; background-color: transparent; border-radius: 99px; cursor: pointer; font-weight: 600; transition: background-color 0.3s, color 0.3s; color: #6c757d; }
    .toggle-button.active { background-color: white; color: #1a1b1f; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .discount-tag { background-color: #28a745; color: white; font-size: 0.75em; padding: 3px 8px; border-radius: 4px; margin-left: 8px; font-weight: bold; }
    /* --- Price Block Visibility --- */
    .price-block { display: none; }
    .price-block.active { display: block; }
    /* --- Pricing Card Additions --- */
    .original-price { text-decoration: line-through; color: #adb5bd; font-size: 1.1rem; font-weight: 400; }
    .plan-details { text-align: left; color: #6c757d; }
    .plan-details ul { padding-left: 0; list-style: none; }
    .plan-details li { margin-bottom: 10px; display: flex; align-items: baseline; }
    .plan-details li:before { content: '✓'; color: #28a745; margin-right: 10px; font-weight: bold; }
    .plan-limits { display: flex; justify-content: space-around; padding-bottom: 15px; border-bottom: 1px solid #dee2e6; margin-bottom: 15px; font-size: 0.9rem; }
    .plan_plan-box.is-dark { background-color: #212529; color: white; }
    .plan_plan-box.is-dark .text-style-muted60, .plan_plan-box.is-dark .text-size-small, .plan_plan-box.is-dark .original-price { color: rgba(255, 255, 255, 0.7); }
    .plan_plan-box.is-dark .plan-details { color: rgba(255, 255, 255, 0.8); }
    .plan_plan-box.is-dark .plan-limits { border-bottom-color: #495057; }
</style>

<section class="section_home1_plans background-color-secondary">
    <div class="padding-global padding-section-medium">
        <div class="container-default">
            <div class="section_header is-centered">
                <div class="section_heading">
                    <div class="text-style-badge">/ PRICING</div>
                    <div class="spacer-xxsmall"></div>
                    <h2>RoomGate Pricing</h2>
                    <div class="spacer-xxsmall"></div>
                    <div>Choose the perfect plan to manage your properties efficiently.</div>
                </div>
            </div>
            <div class="spacer-medium"></div>

            <div class="billing-toggle-wrapper">
                <div class="billing-toggle">
                    <button class="toggle-button" data-period="monthly">Monthly</button>
                    <button class="toggle-button active" data-period="annually">Annually <span class="discount-tag">SAVE</span></button>
                </div>
            </div>
            <div class="spacer-medium"></div>

            <div class="plan_grid">

                @foreach($plans as $group => $planOptions)
                    @php
                        // Find the specific monthly and annual plans from the collection
                        $monthlyPlan = $planOptions->firstWhere('duration_days', 30) ?? $planOptions->first();
                        $annualPlan = $planOptions->firstWhere('duration_days', 365) ?? $monthlyPlan;
                        
                        // Calculate annual discount details if applicable
                        $annualPricePerMonth = ($annualPlan->price > 0) ? $annualPlan->price / 12 : 0;
                    @endphp

                    {{-- <div class="plan_box" 
                         data-monthly-url="{{ route('subscribe', ['code' => $monthlyPlan->code]) }}" 
                         data-annual-url="{{ route('subscribe', ['code' => $annualPlan->code]) }}"> --}}
                    <div class="plan_box" 
                         data-monthly-url="" 
                         data-annual-url=""> 
                        <div class="plan_plan-box @if($monthlyPlan->is_featured) is-dark @endif">
                            <div class="plan_plan-title">
                                <div class="heading-style-h5">{{ ucfirst($group) }}</div>
                                @if($monthlyPlan->is_featured)
                                    <div class="pricing_popular">Most Popular</div>
                                @endif
                            </div>
                            <div class="spacer-small"></div>

                            <div class="home1_pricing_price price-block" data-period="monthly">
                                <div class="heading-style-h2">${{ number_format($monthlyPlan->price, 2) }}</div>
                                <div class="text-style-muted60">per month</div>
                            </div>
                            
                            <div class="home1_pricing_price price-block active" data-period="annually">
                                @if($monthlyPlan->price > $annualPricePerMonth && $annualPricePerMonth > 0)
                                    <span class="original-price">${{ number_format($monthlyPlan->base_monthly_price, 2) }}</span>
                                @endif
                                <div class="heading-style-h2">${{ number_format($annualPricePerMonth, 2) }}</div>
                                <div class="text-style-muted60">per month</div>
                            </div>

                            <div class="spacer-small"></div>
                            <div class="text-size-small">{{ $monthlyPlan->description }}</div>
                            <div class="spacer-small"></div>
                            {{-- <a href="{{ route('subscribe', ['code' => $annualPlan->code]) }}" class="button @if($monthlyPlan->is_featured) is-secondary @endif max-width-full w-button plan-cta-button">
                                Get {{ ucfirst($group) }} Plan
                            </a> --}}
                            <a href="" class="button @if($monthlyPlan->is_featured) is-secondary @endif max-width-full w-button plan-cta-button">
                                Get {{ ucfirst($group) }} Plan
                            </a>
                        </div>
                        <div class="spacer-small"></div>
                        <div class="plan-details">
                            <div class="plan-limits">
                                <span><strong>{{ $monthlyPlan->properties_limit >= 999 ? 'Unlimited' : $monthlyPlan->properties_limit }}</strong> Properties</span>
                                <span><strong>{{ $monthlyPlan->rooms_limit >= 9999 ? 'Unlimited' : $monthlyPlan->rooms_limit }}</strong> Rooms</span>
                            </div>
                            <div>What’s included?</div>
                            <div class="spacer-xsmall"></div>
                            <ul role="list">
                                @foreach(json_decode($monthlyPlan->features ?? '[]') as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('.toggle-button');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const selectedPeriod = this.dataset.period;

                toggleButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const allPriceBlocks = document.querySelectorAll('.price-block');
                allPriceBlocks.forEach(block => {
                    block.classList.toggle('active', block.dataset.period === selectedPeriod);
                });

                const allPlanCards = document.querySelectorAll('.plan_box[data-monthly-url]');
                allPlanCards.forEach(card => {
                    const ctaButton = card.querySelector('.plan-cta-button');
                    if (ctaButton) {
                        ctaButton.href = (selectedPeriod === 'annually')
                            ? card.dataset.annualUrl
                            : card.dataset.monthlyUrl;
                    }
                });
            });
        });
    });
</script>