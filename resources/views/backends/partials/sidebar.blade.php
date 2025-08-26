<div class="sidenav-menu">

    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="logo">
        <span class="logo-light">
            <span class="logo-lg">
                @if(session('locale') == 'kh')
                    <img src="{{ asset('assets/images/logo(kh).png') }}" alt="logo">
                @else
                    <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
                @endif
            </span>
            <span class="logo-sm text-center"><img src="{{ asset('assets') }}/images/logo-sm.png"
                    alt="small logo"></span>
        </span>

        <span class="logo-dark">
            
            <span class="logo-lg">
                @if(session('locale') == 'kh')
                    <img src="{{ asset('assets/images/logo-dark(kh).png') }}" alt="logo">
                @else
                    <span class="logo-lg"><img src="{{ asset('assets') }}/images/logo-dark.png" alt="dark logo"></span>
                @endif
            </span>
            <span class="logo-sm text-center"><img src="{{ asset('assets') }}/images/logo-sm.png"
                    alt="small logo"></span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-sm-hover">
        <i class="ti ti-circle align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-fullsidebar">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div data-simplebar="init" class="simplebar-scrollable-y">
        <div class="simplebar-content-wrapper active" tabindex="0" role="region" aria-label="scrollable content"
            style="height: 100%;">
            <div class="simplebar-content">

                <!--- Sidenav Menu -->
                <ul class="side-nav">

                    <!-- Dashboard -->
                    @hasrole('landlord')
                    <li class="side-nav-item">
                        <a href="{{ route('dashboard') }}" class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                            <span class="menu-text"> {{ __('messages.dashboard') }} </span>
                            <span class="badge bg-success rounded-pill">5</span>
                        </a>
                    </li>
                    @endhasrole
                    
                    @hasrole('admin')
                    <li class="side-nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                            <span class="menu-text"> {{ __('messages.dashboard') }} </span>
                            <span class="badge bg-primary rounded-pill">Admin</span>
                        </a>
                    </li>
                    @endhasrole
                    
                    @hasanyrole('tenant')
                    <li class="side-nav-item">
                        <a href="{{ route('tenant.dashboard') }}" class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                            <span class="menu-text"> {{ __('messages.dashboard') }} </span>
                        </a>
                    </li>
                    @endhasanyrole

                    <li class="side-nav-title mt-2">Apps &amp; Pages</li>

                    @hasanyrole('admin')
                    @php
                        $isUserActive = request()->is('admin/users*');
                    @endphp
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarUser"
                            aria-expanded="{{ $isUserActive ? 'true' : 'false' }}" aria-controls="sidebarUser"
                            class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-users"></i></span>
                            <span class="menu-text"> User </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isUserActive ? 'show' : '' }}" id="sidebarUser">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/users') }}" class="side-nav-link">
                                        <span class="menu-text">View All</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    @php
                        $isSubscriptionActive = request()->is('admin/subscription*') || request()->is('admin/subscriptions*');
                    @endphp
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarSubscription"
                            aria-expanded="{{ $isSubscriptionActive ? 'true' : 'false' }}" aria-controls="sidebarSubscription"
                            class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-credit-card"></i></span>
                            <span class="menu-text"> Subscriptions </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isSubscriptionActive ? 'show' : '' }}" id="sidebarSubscription">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.subscription-plans.index') }}" class="side-nav-link">
                                        <span class="menu-text">Subscription Plans</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.subscriptions.index') }}" class="side-nav-link">
                                        <span class="menu-text">User Subscriptions</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.subscriptions.create') }}" class="side-nav-link">
                                        <span class="menu-text">Create Subscription</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endhasanyrole

                    @hasanyrole('landlord')
                    @php
                        $isUserActive = request()->is('landlord/users*');
                    @endphp
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarUser"
                            aria-expanded="{{ $isUserActive ? 'true' : 'false' }}" aria-controls="sidebarUser"
                            class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-users"></i></span>
                            <span class="menu-text"> {{ __('messages.user') }} </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isUserActive ? 'show' : '' }}" id="sidebarUser">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/users') }}" class="side-nav-link">
                                        <span class="menu-text">{{ __('messages.user_data') }}</span>
                                    </a>
                                </li>

                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/contracts') }}" class="side-nav-link">
                                        <span class="menu-text">{{ __('messages.contracts') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endhasanyrole

                    @hasanyrole('landlord')
                    @php
                        $isPropertyActive = request()->is('landlord/properties*');
                    @endphp
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarProperty"
                            aria-expanded="{{ $isPropertyActive ? 'true' : 'false' }}" aria-controls="sidebarProperty"
                            class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-building-community"></i></span>
                            <span class="menu-text"> {{ __('messages.property') }} </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isPropertyActive ? 'show' : '' }}" id="sidebarProperty">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/properties') }}" class="side-nav-link">
                                        <span class="menu-text">{{ __('messages.data') }}
                                            {{ __('messages.property') }}</span>
                                    </a>
                                </li>

                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/room_types') }}" class="side-nav-link">
                                        <span class="menu-text">{{ __('messages.type') }}</span>
                                    </a>
                                </li>

                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/amenities') }}" class="side-nav-link">
                                        <span class="menu-text">
                                            {{ __('messages.amenities') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                    @endhasanyrole

                    <!-- Room Menu (Landlord only) -->
                    @hasanyrole('landlord')
                    @php
                        $isRoomActive = request()->is('landlord/rooms*');
                    @endphp
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarRoom"
                            aria-expanded="{{ $isRoomActive ? 'true' : 'false' }}" aria-controls="sidebarRoom"
                            class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-door"></i></span>
                            <span class="menu-text"> {{ __('messages.manage') }} {{ __('messages.room') }} </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isRoomActive ? 'show' : '' }}" id="sidebarRoom">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/rooms') }}" class="side-nav-link">
                                        <span class="menu-text">{{ __('messages.room_data') }}</span>
                                    </a>
                                </li>
                                
                            </ul>
                        </div>
                    </li>
                    @endhasanyrole

                    @hasanyrole('admin')
                    @php
                        $isUtilityTypeActive = request()->is('admin/utility_types*');
                    @endphp
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarUtilityType"
                            aria-expanded="{{ $isUtilityTypeActive ? 'true' : 'false' }}" aria-controls="sidebarUtilityType"
                            class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-bolt"></i></span>
                            <span class="menu-text"> Manage Utilities </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isUtilityTypeActive ? 'show' : '' }}" id="sidebarUtilityType">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/utility_types') }}" class="side-nav-link">
                                        <span class="menu-text">Utility Records</span>
                                    </a>
                                </li>
                                
                            </ul>
                        </div>
                    </li>
                    @endhasanyrole


                    @hasanyrole('landlord')
                    @php
                        $isPaymentActive = request()->is('landlord/payments*');
                    @endphp
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarPayment"
                            aria-expanded="{{ $isPaymentActive ? 'true' : 'false' }}" aria-controls="sidebarPayment"
                            class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-credit-card"></i></span>
                            <span class="menu-text"> Invoice </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isPaymentActive ? 'show' : '' }}" id="sidebarPayment">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/payments') }}" class="side-nav-link">
                                        <span class="menu-text">Invoices</span>
                                    </a>
                                </li>

                                <li class="side-nav-item">
                                    <a href="{{ url(userRolePrefix() . '/payments/create') }}" class="side-nav-link">
                                        <span class="menu-text">Create Invoice</span>
                                    </a>
                                </li>
                                
                            </ul>
                        </div>
                    </li>

                    @php
                        $isReportActive = request()->is('landlord/reports*');
                    @endphp
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarReports"
                            aria-expanded="{{ $isReportActive ? 'true' : 'false' }}" aria-controls="sidebarReports"
                            class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-chart-bar"></i></span>
                            <span class="menu-text"> Reports </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isReportActive ? 'show' : '' }}" id="sidebarReports">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ route('landlord.reports.index') }}" class="side-nav-link">
                                        <span class="menu-text">Dashboard</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('landlord.reports.room-occupancy') }}" class="side-nav-link">
                                        <span class="menu-text">Room Occupancy</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('landlord.reports.tenant-report') }}" class="side-nav-link">
                                        <span class="menu-text">Tenant Report</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('landlord.reports.financial-report') }}" class="side-nav-link">
                                        <span class="menu-text">Financial Report</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endhasanyrole


                    @hasanyrole('admin')

                    @php
                        $isFrontendActive = request()->is('admin/frontend*');
                    @endphp
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarFrontend"
                            aria-expanded="{{ $isFrontendActive ? 'true' : 'false' }}" aria-controls="sidebarFrontend"
                            class="side-nav-link">
                            <span class="menu-icon"><i class="ti ti-layout-grid"></i></span>
                            <span class="menu-text"> Frontend </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isFrontendActive ? 'show' : '' }}" id="sidebarFrontend">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.hero') }}" class="side-nav-link">
                                        <span class="menu-text">Hero</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.benefit') }}" class="side-nav-link">
                                        <span class="menu-text">Benefit</span>
                                    </a>
                                </li>

                                <li class="side-nav-item">
                                    <a href="{{ route('admin.feature') }}" class="side-nav-link">
                                        <span class="menu-text">Feature</span>
                                    </a>
                                </li>

                                <li class="side-nav-item">
                                    <a href="{{ route('admin.faq') }}" class="side-nav-link">
                                        <span class="menu-text">FAQ</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endhasanyrole
                </ul>
                

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>