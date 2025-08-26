@extends('backends.layouts.blank')


@push('style')

{{-- {{ asset('assets') }}/css/ --}}

@endpush



@section('content')
<div class="auth-bg d-flex min-vh-100 justify-content-center align-items-center">
    <div class="row g-0 justify-content-center w-100 m-xxl-5 px-xxl-4 m-3">
        <div class="col-xl-4 col-lg-5 col-md-6">
            <div class="card overflow-hidden text-center h-100 p-xxl-4 p-3 mb-0">
                <a href="https://coderthemes.com/boron/layouts/index.html" class="auth-brand mb-3">
                    <img src="{{ asset('assets') }}/images/logo-dark.png" alt="dark logo" height="24" class="logo-dark">
                    <img src="{{ asset('assets') }}/images/logo.png" alt="logo light" height="24" class="logo-light">
                </a>

                <h4 class="fw-semibold mb-4">Deactivation Account</h4>

                <div class="d-flex align-items-center gap-2 mb-3 text-start">
                    <img src="{{ asset('assets') }}/images/avatar-1.jpg" alt="" class="avatar-xl rounded img-thumbnail">
                    <div>
                        <h4 class="fw-semibold text-dark">Hi ! Dhanoo K.</h4>
                        <p class="mb-0">Temporarily Deactivate your account instead of Deleting?</p>
                    </div>
                </div>

                <div class="mb-3 text-start">
                    <div class="alert alert-danger fw-medium mb-0" role="alert">
                        Your profile will be temporarily hidden until you activate it again by logging back in
                    </div>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary" type="submit">Deactivate Account</button>
                </div>
                <p class="text-danger fs-14 my-3">Back to <a href="https://coderthemes.com/boron/layouts/auth-login.html" class="text-dark fw-semibold ms-1">Login !</a>

                </p><p class="mt-auto mb-0">
                    <script>document.write(new Date().getFullYear())</script> © Boron - By <span class="fw-bold text-decoration-underline text-uppercase text-reset fs-12">Coderthemes</span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection



@push('script')

@endpush
