@extends('backends.layouts.blank')

@section('title', 'Login | RoomGate')

@push('style')
    {{-- {{ asset('assets') }}/css/ --}}
@endpush



@section('content')
    <div class="auth-bg d-flex min-vh-100 justify-content-center align-items-center">
        <div class="row g-0 justify-content-center w-100 m-xxl-5 px-xxl-4 m-3">
            <div class="col-xl-4 col-lg-5 col-md-6">
                <div class="card overflow-hidden text-center h-100 p-xxl-4 p-3 mb-0">
                    <a href="/" class="auth-brand mb-3">
                        <img src="{{ asset('assets') }}/images/logo-dark.png" alt="dark logo" height="70" class="logo-dark">
                        <img src="{{ asset('assets') }}/images/logo.png" alt="logo light" height="70" class="logo-light">
                    </a>

                    <h4 class="fw-semibold mb-2">Login your account</h4>

                    <p class="text-muted mb-4">Enter your email address and password to access admin panel.</p>

                    <form method="POST" action="{{ route('login') }}" class="text-start mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="example-email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email"
                                :value="old('email')" required autofocus autocomplete="username" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="example-password">Password</label>
                            <input type="password" id="example-password" class="form-control"
                                placeholder="Enter your password" name="password" required
                                autocomplete="current-password" />
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                                <label class="form-check-label" for="checkbox-signin">Remember me</label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-muted border-bottom border-dashed">{{ __('Forgot your password?') }}</a>
                            @endif
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary" type="submit">Login</button>
                        </div>
                    </form>

                    <p class="text-danger fs-14 mb-4">Don't have an account? <a href="{{ route('register') }}"
                            class="fw-semibold text-dark ms-1">Sign Up !</a></p>

                    {{-- <p class="fs-13 fw-semibold">Or Login with Social</p>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <a href="" class="btn btn-soft-danger avatar-lg"><i class="ti ti-brand-google-filled fs-24"></i></a>
                        <a href="" class="btn btn-soft-success avatar-lg"><i class="ti ti-brand-apple fs-24"></i></a>
                        <a href="" class="btn btn-soft-primary avatar-lg"><i class="ti ti-brand-facebook fs-24"></i></a>
                        <a href="" class="btn btn-soft-info avatar-lg"><i class="ti ti-brand-linkedin fs-24"></i></a>
                    </div>

                    <p class="mt-auto mb-0">
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © Boron - By <span
                            class="fw-bold text-decoration-underline text-uppercase text-reset fs-12">Coderthemes</span>
                    </p> --}}
                </div>
            </div>
        </div>
    </div>
@endsection



@push('script')
@endpush