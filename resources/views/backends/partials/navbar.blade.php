<header class="app-topbar">
    <div class="page-container topbar-menu">
        <div class="d-flex align-items-center gap-2">

            <!-- Brand Logo -->
            <a href="https://coderthemes.com/boron/layouts/index.html" class="logo">
                <span class="logo-light">
                    <span class="logo-lg"><img src="{{ asset('assets') }}/images/logo.png" alt="logo"></span>
                    <span class="logo-sm"><img src="{{ asset('assets') }}/images/logo-sm.png" alt="small logo"></span>
                </span>

                <span class="logo-dark">
                    <span class="logo-lg"><img src="{{ asset('assets') }}/images/logo-dark.png" alt="dark logo"></span>
                    <span class="logo-sm"><img src="{{ asset('assets') }}/images/logo-sm.png" alt="small logo"></span>
                </span>
            </a>

            <!-- Sidebar Menu Toggle Button -->
            <button class="sidenav-toggle-button btn btn-secondary btn-icon">
                <i class="ti ti-menu-deep fs-24"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="topnav-toggle-button" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="ti ti-menu-deep fs-22"></i>
            </button>

            <!-- Button Trigger Search Modal -->
            <div class="topbar-search text-muted d-none d-xl-flex gap-2 align-items-center" data-bs-toggle="modal"
                data-bs-target="#searchModal" type="button">
                <i class="ti ti-search fs-18"></i>
                <span class="me-2">Search something..</span>
                <button type="submit" class="ms-auto btn btn-sm btn-primary shadow-none">⌘K
                </button>
            </div>

            <!-- Mega Menu Dropdown -->
            <div class="topbar-item d-none d-md-flex">
                <div class="dropdown">
                    <a href="" class="topbar-link btn btn-link px-2 dropdown-toggle drop-arrow-none fw-medium"
                        data-bs-toggle="dropdown" data-bs-trigger="hover" data-bs-offset="0,24" aria-haspopup="false"
                        aria-expanded="false">
                        Pages <i class="ti ti-chevron-down ms-1"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-xxl p-0">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <h5 class="mb-2 fw-semibold">UI Components</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li>
                                            <a href="">Posts</a>
                                        </li>
                                        <li>
                                            <a
                                                href="https://coderthemes.com/boron/layouts/extended-dragula.html">Dragula</a>
                                        </li>
                                        <li>
                                            <a
                                                href="https://coderthemes.com/boron/layouts/ui-dropdowns.html">Dropdowns</a>
                                        </li>
                                        <li>
                                            <a
                                                href="https://coderthemes.com/boron/layouts/extended-ratings.html">Ratings</a>
                                        </li>
                                        <li>
                                            <a href="https://coderthemes.com/boron/layouts/extended-sweetalerts.html">Sweet
                                                Alerts</a>
                                        </li>
                                        <li>
                                            <a
                                                href="https://coderthemes.com/boron/layouts/extended-scrollbar.html">Scrollbar</a>
                                        </li>
                                        <li>
                                            <a href="https://coderthemes.com/boron/layouts/form-range-slider.html">Range
                                                Slider</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3">
                                    <h5 class="mb-2 fw-semibold">Applications</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li>
                                            <a
                                                href="https://coderthemes.com/boron/layouts/apps-ecommerce-products.html">eCommerce
                                                Pages</a>
                                        </li>
                                        <li>
                                            <a
                                                href="https://coderthemes.com/boron/layouts/tables-gridjs.html#!">Hospital</a>
                                        </li>
                                        <li>
                                            <a href="https://coderthemes.com/boron/layouts/apps-email.html">Email</a>
                                        </li>
                                        <li>
                                            <a
                                                href="https://coderthemes.com/boron/layouts/apps-calendar.html">Calendar</a>
                                        </li>
                                        <li>
                                            <a href="https://coderthemes.com/boron/layouts/tables-gridjs.html#!">Kanban
                                                Board</a>
                                        </li>
                                        <li>
                                            <a href="https://coderthemes.com/boron/layouts/apps-invoices.html">Invoice
                                                Management</a>
                                        </li>
                                        <li>
                                            <a
                                                href="https://coderthemes.com/boron/layouts/pages-pricing.html">Pricing</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4 bg-light bg-opacity-50">
                                <div class="p-3">
                                    <h5 class="mb-2 fw-semibold">Extra Pages</h5>
                                    <ul class="list-unstyled megamenu-list">
                                        <li>
                                            <a href="javascript:void(0);">Left Sidebar with User</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);">Menu Collapsed</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);">Small Left Sidebar</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);">New Header Style</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);">My Account</a>
                                        </li>
                                        <li>
                                            <a href="https://coderthemes.com/boron/layouts/pages-coming-soon.html">Maintenance
                                                &amp; Coming Soon</a>
                                        </li>
                                    </ul>
                                </div> <!-- end .bg-light-->
                            </div> <!-- end col-->
                        </div> <!-- end row-->
                    </div> <!-- .dropdown-menu-->
                </div> <!-- .dropdown-->
            </div> <!-- end topbar-item -->
        </div>

        <div class="d-flex align-items-center gap-2">

            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link btn btn-outline-primary btn-icon" data-bs-toggle="dropdown"
                        data-bs-offset="0,24" type="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ asset('assets') }}/images/{{ session('locale', 'en') == 'en' ? 'us' : 'kh' }}.svg"
                            alt="user-image" class="w-100 rounded" height="18" id="selected-language-image">
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="{{ route('language.switch', 'en') }}" class="dropdown-item">
                            <img src="{{ asset('assets') }}/images/us.svg" alt="user-image" class="me-1 rounded"
                                height="18">
                            <span class="align-middle">English</span>
                        </a>

                        <a href="{{ route('language.switch', 'kh') }}" class="dropdown-item">
                            <img src="{{ asset('assets') }}/images/kh.svg" alt="user-image" class="me-1 rounded"
                                height="18">
                            <span class="align-middle">Khmer</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notification Dropdown -->
            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link btn btn-outline-primary btn-icon dropdown-toggle drop-arrow-none"
                        data-bs-toggle="dropdown" data-bs-offset="0,24" type="button" data-bs-auto-close="outside"
                        aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-bell animate-ring fs-22"></i>
                        <span class="noti-icon-badge {{ Auth::user()->hasRole('landlord') && ((!Auth::user()->activeSubscription()) || (Auth::user()->activeSubscription() && Auth::user()->activeSubscription()->status !== 'active') || (Auth::user()->activeSubscription() && Auth::user()->activeSubscription()->payment_status !== 'paid' && Auth::user()->activeSubscription()->payment_status !== 'trial')) ? 'bg-danger' : '' }}"></span>
                    </button>

                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" style="min-height: 300px;">
                        <div class="p-3 border-bottom border-dashed">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold"> Notifications</h6>
                                </div>
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <a href="https://coderthemes.com/boron/layouts/tables-gridjs.html#"
                                            class="dropdown-toggle drop-arrow-none link-dark" data-bs-toggle="dropdown"
                                            data-bs-offset="0,15" aria-expanded="false">
                                            <i class="ti ti-settings fs-22 align-middle"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <!-- item-->
                                            <a href="javascript:void(0);" class="dropdown-item">Mark as
                                                Read</a>
                                            <!-- item-->
                                            <a href="javascript:void(0);" class="dropdown-item">Delete All</a>
                                            <!-- item-->
                                            <a href="javascript:void(0);" class="dropdown-item">Do not
                                                Disturb</a>
                                            <!-- item-->
                                            <a href="javascript:void(0);" class="dropdown-item">Other
                                                Settings</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Subscription Alert Notification - Fixed position -->
                        @php
                            $hasActiveSubscription = Auth::user()->activeSubscription();
                            $latestSubscription = Auth::user()->latestSubscription();
                            $status = $latestSubscription ? $latestSubscription->status : 'none';
                            $paymentStatus = $hasActiveSubscription ? $hasActiveSubscription->payment_status : 'none';
                        @endphp
                        
                        @if(Auth::user()->hasRole('landlord'))
                            @if(!$hasActiveSubscription || ($hasActiveSubscription && $hasActiveSubscription->status !== 'active'))
                            <div class="p-3 border-bottom border-dashed bg-warning-subtle">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ti ti-alert-triangle fs-22 text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="m-0 text-danger fs-14">Subscription Required</h6>
                                        <p class="m-0 fs-13 text-muted">
                                            @if(!$hasActiveSubscription && $latestSubscription && $latestSubscription->status === 'cancelled')
                                                Your subscription has been cancelled.
                                            @elseif(!$hasActiveSubscription && $latestSubscription && $latestSubscription->status === 'inactive')
                                                Your subscription is inactive.
                                            @elseif(!$hasActiveSubscription && $latestSubscription && $latestSubscription->end_date < now())
                                                Your subscription has expired.
                                            @elseif(!$hasActiveSubscription)
                                                You don't have an active subscription.
                                            @endif
                                        </p>
                                        <a href="{{ route('landlord.subscription.plans') }}" class="fs-12 text-warning">Subscribe now</a>
                                    </div>
                                </div>
                            </div>
                            @elseif($hasActiveSubscription && $paymentStatus !== 'paid' && $paymentStatus !== 'trial')
                            <div class="p-3 border-bottom border-dashed bg-info-subtle">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ti ti-credit-card fs-22 text-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="m-0 text-danger fs-14">Payment Required</h6>
                                        <p class="m-0 fs-13 text-muted">Your subscription payment is pending.</p>
                                        <a href="{{ route('landlord.subscription.plans') }}" class="fs-12 text-info">Complete payment</a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif

                        <div class="position-relative z-2 rounded-0" style="max-height: 300px;" data-simplebar="init">
                            <div class="simplebar-wrapper" style="margin: 0px;">
                                <div class="simplebar-height-auto-observer-wrapper">
                                    <div class="simplebar-height-auto-observer"></div>
                                </div>
                                <div class="simplebar-mask">
                                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                        <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                            aria-label="scrollable content" style="height: auto; overflow: hidden;">
                                            <div class="simplebar-content" style="padding: 0px;">
                                                
                                                <!-- item-->
                                                <div class="dropdown-item notification-item py-2 text-wrap {{ !Auth::user()->hasRole('landlord') ? 'active' : '' }}"
                                                    id="notification-1">
                                                    <span class="d-flex align-items-center">
                                                        <span class="me-3 position-relative flex-shrink-0">
                                                            <img src="{{ asset('assets') }}/images/avatar-2.jpg"
                                                                class="avatar-md rounded-circle" alt="">
                                                            <span
                                                                class="position-absolute rounded-pill bg-danger notification-badge">
                                                                <i class="ti ti-message-circle"></i>
                                                                <span class="visually-hidden">unread
                                                                    messages</span>
                                                            </span>
                                                        </span>
                                                        <span class="flex-grow-1 text-muted">
                                                            <span class="fw-medium text-body">Glady Haid</span>
                                                            commented on <span class="fw-medium text-body">paces admin
                                                                status</span>
                                                            <br>
                                                            <span class="fs-12">25m ago</span>
                                                        </span>
                                                        <span class="notification-item-close">
                                                            <button type="button"
                                                                class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                                                data-dismissible="#notification-1">
                                                                <i class="ti ti-x fs-16"></i>
                                                            </button>
                                                        </span>
                                                    </span>
                                                </div>

                                                <!-- item-->
                                                <div class="dropdown-item notification-item py-2 text-wrap"
                                                    id="notification-2">
                                                    <span class="d-flex align-items-center">
                                                        <span class="me-3 position-relative flex-shrink-0">
                                                            <img src="{{ asset('assets') }}/images/avatar-4.jpg"
                                                                class="avatar-md rounded-circle" alt="">
                                                            <span
                                                                class="position-absolute rounded-pill bg-info notification-badge">
                                                                <i class="ti ti-currency-dollar"></i>
                                                                <span class="visually-hidden">unread
                                                                    messages</span>
                                                            </span>
                                                        </span>
                                                        <span class="flex-grow-1 text-muted">
                                                            <span class="fw-medium text-body">Tommy
                                                                Berry</span> donated <span
                                                                class="text-success">$100.00</span> for <span
                                                                class="fw-medium text-body">Carbon removal
                                                                program</span>
                                                            <br>
                                                            <span class="fs-12">58m ago</span>
                                                        </span>
                                                        <span class="notification-item-close">
                                                            <button type="button"
                                                                class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                                                data-dismissible="#notification-2">
                                                                <i class="ti ti-x fs-16"></i>
                                                            </button>
                                                        </span>
                                                    </span>
                                                </div>

                                                <!-- item-->
                                                <div class="dropdown-item notification-item py-2 text-wrap"
                                                    id="notification-3">
                                                    <span class="d-flex align-items-center">
                                                        <div class="avatar-md flex-shrink-0 me-3">
                                                            <span
                                                                class="avatar-title bg-success-subtle text-success rounded-circle fs-22">
                                                                <iconify-icon
                                                                    icon="solar:wallet-money-bold-duotone"><template
                                                                        shadowrootmode="open">
                                                                        <style data-style="data-style">
                                                                            :host {
                                                                                display: inline-block;
                                                                                vertical-align: 0
                                                                            }

                                                                            span,
                                                                            svg {
                                                                                display: block
                                                                            }
                                                                        </style><svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="1em" height="1em"
                                                                            viewBox="0 0 24 24">
                                                                            <path fill="currentColor"
                                                                                d="M4.892 9.614c0-.402.323-.728.722-.728H9.47c.4 0 .723.326.723.728a.726.726 0 0 1-.723.729H5.614a.726.726 0 0 1-.722-.729">
                                                                            </path>
                                                                            <path fill="currentColor"
                                                                                fill-rule="evenodd"
                                                                                d="M21.188 10.004q-.094-.005-.2-.004h-2.773C15.944 10 14 11.736 14 14s1.944 4 4.215 4h2.773q.106.001.2-.004c.923-.056 1.739-.757 1.808-1.737c.004-.064.004-.133.004-.197v-4.124c0-.064 0-.133-.004-.197c-.069-.98-.885-1.68-1.808-1.737m-3.217 5.063c.584 0 1.058-.478 1.058-1.067c0-.59-.474-1.067-1.058-1.067s-1.06.478-1.06 1.067c0 .59.475 1.067 1.06 1.067"
                                                                                clip-rule="evenodd"></path>
                                                                            <path fill="currentColor"
                                                                                d="M21.14 10.002c0-1.181-.044-2.448-.798-3.355a4 4 0 0 0-.233-.256c-.749-.748-1.698-1.08-2.87-1.238C16.099 5 14.644 5 12.806 5h-2.112C8.856 5 7.4 5 6.26 5.153c-1.172.158-2.121.49-2.87 1.238c-.748.749-1.08 1.698-1.238 2.87C2 10.401 2 11.856 2 13.694v.112c0 1.838 0 3.294.153 4.433c.158 1.172.49 2.121 1.238 2.87c.749.748 1.698 1.08 2.87 1.238c1.14.153 2.595.153 4.433.153h2.112c1.838 0 3.294 0 4.433-.153c1.172-.158 2.121-.49 2.87-1.238q.305-.308.526-.66c.45-.72.504-1.602.504-2.45l-.15.001h-2.774C15.944 18 14 16.264 14 14s1.944-4 4.215-4h2.773q.079 0 .151.002"
                                                                                opacity=".5"></path>
                                                                            <path fill="currentColor"
                                                                                d="M10.101 2.572L8 3.992l-1.733 1.16C7.405 5 8.859 5 10.694 5h2.112c1.838 0 3.294 0 4.433.153q.344.045.662.114L16 4l-2.113-1.428a3.42 3.42 0 0 0-3.786 0">
                                                                            </path>
                                                                        </svg>
                                                                    </template></iconify-icon>
                                                            </span>
                                                        </div>
                                                        <span class="flex-grow-1 text-muted">
                                                            You withdraw a <span class="fw-medium text-body">$500</span>
                                                            by
                                                            <span class="fw-medium text-body">New York
                                                                ATM</span>
                                                            <br>
                                                            <span class="fs-12">2h ago</span>
                                                        </span>
                                                        <span class="notification-item-close">
                                                            <button type="button"
                                                                class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                                                data-dismissible="#notification-3">
                                                                <i class="ti ti-x fs-16"></i>
                                                            </button>
                                                        </span>
                                                    </span>
                                                </div>

                                                <!-- item-->
                                                <div class="dropdown-item notification-item py-2 text-wrap"
                                                    id="notification-4">
                                                    <span class="d-flex align-items-center">
                                                        <span class="me-3 position-relative flex-shrink-0">
                                                            <img src="{{ asset('assets') }}/images/avatar-7.jpg"
                                                                class="avatar-md rounded-circle" alt="">
                                                            <span
                                                                class="position-absolute rounded-pill bg-secondary notification-badge">
                                                                <i class="ti ti-plus"></i>
                                                                <span class="visually-hidden">unread
                                                                    messages</span>
                                                            </span>
                                                        </span>
                                                        <span class="flex-grow-1 text-muted">
                                                            <span class="fw-medium text-body">Richard
                                                                Allen</span> followed you in <span
                                                                class="fw-medium text-body">Facebook</span>
                                                            <br>
                                                            <span class="fs-12">3h ago</span>
                                                        </span>
                                                        <span class="notification-item-close">
                                                            <button type="button"
                                                                class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                                                data-dismissible="#notification-4">
                                                                <i class="ti ti-x fs-16"></i>
                                                            </button>
                                                        </span>
                                                    </span>
                                                </div>

                                                <!-- item-->
                                                <div class="dropdown-item notification-item py-2 text-wrap"
                                                    id="notification-5">
                                                    <span class="d-flex align-items-center">
                                                        <span class="me-3 position-relative flex-shrink-0">
                                                            <img src="{{ asset('assets') }}/images/avatar-10.jpg"
                                                                class="avatar-md rounded-circle" alt="">
                                                            <span
                                                                class="position-absolute rounded-pill bg-danger notification-badge">
                                                                <i class="ti ti-heart-filled"></i>
                                                                <span class="visually-hidden">unread
                                                                    messages</span>
                                                            </span>
                                                        </span>
                                                        <span class="flex-grow-1 text-muted">
                                                            <span class="fw-medium text-body">Victor
                                                                Collier</span> liked you recent photo in <span
                                                                class="fw-medium text-body">Instagram</span>
                                                            <br>
                                                            <span class="fs-12">10h ago</span>
                                                        </span>
                                                        <span class="notification-item-close">
                                                            <button type="button"
                                                                class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                                                data-dismissible="#notification-5">
                                                                <i class="ti ti-x fs-16"></i>
                                                            </button>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="simplebar-placeholder" style="width: 0px; height: 0px;"></div>
                            </div>
                            <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                            </div>
                            <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                                <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
                            </div>
                        </div>

                        <!-- All-->
                        <a href="javascript:void(0);"
                            class="dropdown-item notification-item text-center text-reset text-decoration-underline link-offset-2 fw-bold notify-item border-top border-light py-2">
                            View All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Button Trigger Customizer Offcanvas -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link btn btn-outline-primary btn-icon" data-bs-toggle="offcanvas"
                    data-bs-target="#theme-settings-offcanvas" type="button">
                    <i class="ti ti-settings fs-22"></i>
                </button>
            </div>
            

            <!-- Light/Dark Mode Button -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link btn btn-outline-primary btn-icon" id="light-dark-mode" type="button">
                    <i class="ti ti-moon fs-22"></i>
                </button>
            </div>

            <!-- User Dropdown -->
            <div class="topbar-item">
                <div class="dropdown">
                    <a class="topbar-link btn btn-outline-primary dropdown-toggle drop-arrow-none"
                        data-bs-toggle="dropdown" data-bs-offset="0,22" type="button" aria-haspopup="false"
                        aria-expanded="false">
                        <img src="{{ Auth::user()->image ? asset(Auth::user()->image) : asset('assets/images/default_image.png') }}"
                            width="24" height="24" class="rounded me-lg-2" alt="User Profile Picture">
                        <span class="d-lg-flex flex-column gap-1 d-none">
                            {{ Auth::user()->name }}
                        </span>
                        <i class="ti ti-chevron-down d-none d-lg-block align-middle ms-2"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>

                        <!-- item-->
                        @if(Auth::user()->hasRole('landlord'))
                        <a href="{{ route('landlord.profile.index') }}" class="dropdown-item">
                            <i class="ti ti-user-hexagon me-1 fs-17 align-middle"></i>
                            <span class="align-middle">My Account</span>
                        </a>
                        @elseif(Auth::user()->hasRole('tenant'))
                        <a href="{{ route('tenant.profile') }}" class="dropdown-item">
                            <i class="ti ti-user-hexagon me-1 fs-17 align-middle"></i>
                            <span class="align-middle">My Account</span>
                        </a>
                        @else
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="ti ti-user-hexagon me-1 fs-17 align-middle"></i>
                            <span class="align-middle">My Account</span>
                        </a>
                        @endif

                        <!-- item-->
                        <a href="{{ route('contact') }}" class="dropdown-item">
                            <i class="ti ti-lifebuoy me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Contact Support</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item active fw-semibold text-danger" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                <i class="ti ti-logout me-1 fs-17 align-middle"></i>
                                <span class="align-middle">Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>