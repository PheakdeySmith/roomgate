<!DOCTYPE html>
<html lang="en" data-sidenav-size="default" data-bs-theme="dark" data-menu-color="dark" data-topbar-color="dark" data-layout-mode="fluid">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>@yield('title', 'RoomGate')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
    <meta content="Coderthemes" name="author">
    <link rel="shortcut icon" href="{{ asset('assets') }}/images/favicon.ico">

    @stack('style')

    <script src="{{ asset('assets') }}/js/config.js"></script>

    <link href="{{ asset('assets') }}/css/vendor.min.css" rel="stylesheet" type="text/css">

    <link href="{{ asset('assets') }}/css/app.min.css" rel="stylesheet" type="text/css" id="app-style">

    <link href="{{ asset('assets') }}/css/icons.min.css" rel="stylesheet" type="text/css">

    <link href="{{ asset('assets') }}/css/sweetalert2.min.css" rel="stylesheet" type="text/css">

    <script src="{{ asset('js') }}/currency-format.js"></script>

</head>

<body
    class="{{ session('subscription_status.active', false) === false ? 'has-inactive-subscription' : '' }} {{ session('subscription_status.payment_status', 'paid') !== 'paid' ? 'has-unpaid-subscription' : '' }}">
    @if(Auth::user()->hasRole('landlord'))
    <div id="subscription-status-data" style="display:none;"
        data-status="{{ json_encode(session('subscription_status', ['active' => false])) }}"></div>
    @endif
    <div class="wrapper">

        @include('backends.partials.sidebar')
        @include('backends.partials.navbar')
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-transparent">
                    <div class="card mb-0 shadow-none">
                        <div class="px-3 py-2 d-flex flex-row align-items-center" id="top-search">
                            <i class="ti ti-search fs-22"></i>
                            <input type="search" class="form-control border-0" id="search-modal-input"
                                placeholder="Search for actions, people,">
                            <button type="button" class="btn p-0" data-bs-dismiss="modal"
                                aria-label="Close">[esc]</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-content">
            @include('backends.partials.subscription_warning')
            
            @include('backends.partials.subscription_banner')
            
            @yield('content')


            @include('backends.partials.footer')
            </div>

        </div>
    @include('backends.partials.theme-settings')


    <script src="{{ asset('assets') }}/js/vendor.min.js"></script>

    <script src="{{ asset('assets') }}/js/app.js"></script>

    <script src="{{ asset('assets') }}/js/gridjs.umd.js"></script>

    <script src="{{ asset('assets') }}/js/sweetalert2.min.js"></script>
    
    <script src="{{ asset('assets') }}/js/debug-navigation.js"></script>
    
    <script src="{{ asset('assets') }}/js/navigation-fix.js"></script>
    

    <script>
        const subscriptionPlansUrl = "{{ route('landlord.subscription.plans') }}";
    </script>
    
    @if(Auth::user()->hasRole('landlord'))
        <script src="{{ asset('assets') }}/js/subscription-check.js"></script>
    @endif
    
    @stack('script')

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    position: "top-end",
                    title: "{{ session('success') }}",
                    width: 500,
                    padding: 30,
                    background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-5.jpg') }}) no-repeat center",
                    showConfirmButton: false,
                    timer: 4000,
                    customClass: {
                        title: 'swal-title-success'
                    }
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    position: "top-end",
                    icon: 'error', // Add an error icon for clarity
                    title: "{{ session('error') }}", 
                    width: 500,
                    padding: 30,
                    background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-4.jpg') }}) no-repeat center",
                    showConfirmButton: false,
                    timer: 6000, // Give a little more time to read errors
                    customClass: {
                        title: 'swal-title-error' // Use your existing error style
                    }
                });
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '• {{ $error }}\n';
                @endforeach

                Swal.fire({
                    position: "top-end",
                    title: 'Please Fix The Errors',
                    text: errorMessages,
                    width: 500,
                    padding: 30,
                    background: "var(--bs-secondary-bg) url({{ asset('assets/images/small-4.jpg') }}) no-repeat center",
                    showConfirmButton: false,
                    customClass: {
                        title: 'swal-title-error'
                    }
                });
            });
        </script>
    @endif


    @once
        <style>
            .swal-title-success {
                color: rgb(85, 133, 142) !important;
                font-size: 28px !important;
                font-weight: bold;
                margin-bottom: 20px;
            }

            .swal-title-error {
                color: rgb(142, 85, 85) !important;
                font-size: 28px !important;
                font-weight: bold;
                margin-bottom: 20px;
            }
        </style>
    @endonce

    <script>
        @if(Auth::check())
            // Initialize with user's currency settings if available
            @if(isset(Auth::user()->currency_code) && Auth::user()->currency_code)
                setCurrentCurrency('{{ Auth::user()->currency_code }}', {{ Auth::user()->exchange_rate ?? 1 }});
            @else
                setCurrentCurrency('USD', 1);
            @endif
        @endif
    </script>


</body>

</html>