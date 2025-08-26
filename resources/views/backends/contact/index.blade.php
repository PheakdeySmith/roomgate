@extends('backends.layouts.app')

@section('title', 'Contact Support')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Contact Support</li>
                    </ol>
                </div>
                <h4 class="page-title">Contact Support</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-xl-6 mx-auto">
            <div class="card">
                <div class="card-header bg-primary-subtle">
                    <h5 class="card-title mb-0 text-primary">How can we help?</h5>
                </div>
                <div class="card-body">
                    <p>Our support team is here to help with any questions, issues, or feedback you may have about the RoomGate system.</p>
                    
                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-info-subtle">
                    <h5 class="card-title mb-0 text-info">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <i class="ti ti-mail fs-24 text-muted"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mt-0">Email</h5>
                            <p class="mb-0">support@roomgate.example.com</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <i class="ti ti-phone fs-24 text-muted"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mt-0">Phone</h5>
                            <p class="mb-0">+1 (123) 456-7890</p>
                            <p class="text-muted">Monday to Friday, 9am to 6pm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
