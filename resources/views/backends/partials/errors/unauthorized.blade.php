@extends('backends.layouts.blank')

@section('title', 'Login | RoomGate')

@push('style')
    {{-- {{ asset('assets') }}/css/ --}}
@endpush


@section('content')
    <div class="auth-bg d-flex min-vh-100 justify-content-center align-items-center">
        <div class="row g-0 justify-content-center w-100 m-xxl-5 px-xxl-4 m-3">
            <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6">
                <div class="card overflow-hidden text-center h-100 p-xxl-4 p-3 mb-0">
                    <a href="{{ route('dashboard') }}" class="auth-brand mb-3">
                        <img src="{{ asset('assets') }}/images/logo-dark.png" alt="dark logo" height="70" class="logo-dark">
                        <img src="{{ asset('assets') }}/images/logo.png" alt="logo light" height="70" class="logo-light">
                    </a>


                    <div class="mx-auto text-center">
                        <img src="{{ asset('assets') }}/images/error-401.png" alt="" class="mt-3 mb-2" height="230">

                        <h2 class="fw-bold mt-3 text-primary lh-base">Error Unauthorized ! </h2>

                        <h4 class="mt-2 text-dark lh-base">Access to allowed only for registered user</h4>

                        <p class="text-muted fs-15 mb-3">Sorry, but you are not authorized to view this page.</p>

                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Back To Home <i class="ti ti-home ms-1"></i></a>
                    </div>

                    <p class="mt-3 mb-0">
                        <script>document.write(new Date().getFullYear())</script> © RoomGate
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('script')
@endpush