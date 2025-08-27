@extends('backends.layouts.blank')

@section('title', 'Register | RoomGate')

@push('style')
    {{-- You can add custom styles here if needed --}}
@endpush

@section('content')
<div class="auth-bg d-flex min-vh-100 justify-content-center align-items-center">
    <div class="row g-0 justify-content-center w-100 m-xxl-5 px-xxl-4 m-3">
        <div class="col-xl-4 col-lg-5 col-md-6">
            <div class="card overflow-hidden text-center h-100 p-xxl-4 p-3 mb-0">
                <a href="{{ url('/') }}" class="auth-brand mb-3">
                    <img src="{{ asset('assets') }}/images/logo-dark.png" alt="dark logo" height="70" class="logo-dark">
                    <img src="{{ asset('assets') }}/images/logo.png" alt="logo light" height="70" class="logo-light">
                </a>

                <h4 class="fw-semibold mb-2">Welcome to RoomGate</h4>
                <p class="text-muted mb-4">Enter your name, email address and password to create an account.</p>

                <form method="POST" action="{{ route('register') }}" class="text-start mb-3">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label" for="name">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter your name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" placeholder="Enter your password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    {{-- Confirm Password --}}
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="Confirm your password">
                    </div>


                    {{-- Terms and Conditions --}}
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">I agree to all <a href="#" class="link-dark text-decoration-underline">Terms &amp; Condition</a> </label>
                             @error('terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary" type="submit">Sign Up</button>
                    </div>
                </form>

                <p class="mb-4">Already have an account? <a href="{{ route('login')}}" class="fw-semibold text-dark ms-1">Login</a></p>

                <p class="mt-auto mb-0">
                    <script>document.write(new Date().getFullYear())</script> © RoomGate
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
@endpush