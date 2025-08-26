@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
@endphp

@extends('backends.layouts.app')

@section('title', 'My Profile | RoomGate')

@push('style')
<style>
    /* Modern Mobile App Design */
    :root {
        --primary-color: #47CFD1;
        --secondary-color: #2DB6B8;
        --accent-color: #70E1E3;
        --dark-color: #333333;
        --light-color: #FFFFFF;
        --bg-light: #FFFFFF;
        --bg-softer: #F8FDFD;
        --text-color: #333333;
        --text-muted: #718096;
        --card-radius: 1.25rem;
        --button-radius: 1.75rem;
        --progress-height: 0.5rem;
        --icon-bg: rgba(71, 207, 209, 0.15);
        --shadow-color: rgba(71, 207, 209, 0.2);
        --class-card-bg: rgba(112, 225, 227, 0.2);
    }
    
    [data-bs-theme="dark"] {
        --bg-light: #1A1A1A;
        --text-color: #E2E8F0;
        --text-muted: #A0AEC0;
        --icon-bg: rgba(71, 207, 209, 0.2);
    }
    
    .dashboard-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 1.25rem;
    }
    
    .dashboard-section {
        margin-bottom: 1.5rem;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .section-title {
        font-weight: 700;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .dashboard-header {
        position: sticky;
        top: 0;
        background-color: transparent;
        z-index: 100;
        padding: 1rem 0 1.5rem 0;
        margin-bottom: 1rem;
    }
    
    .dashboard-header h1 {
        font-weight: 600;
        font-size: 1.4rem;
        color: var(--text-color);
        margin: 0;
    }
    
    .back-button {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        color: var(--text-color);
        padding: 0;
        margin-right: 0.75rem;
    }
    
    .dashboard-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .header-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: var(--bg-softer);
        color: var(--text-color);
        border: none;
        padding: 0;
        transition: all 0.2s ease;
    }
    
    .header-icon:hover {
        background-color: var(--icon-bg);
        transform: translateY(-2px);
    }
    
    .profile-card {
        background-color: white;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .profile-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .profile-stats {
        display: flex;
        margin-top: 1.5rem;
        padding: 1rem;
        background-color: var(--bg-softer);
        border-radius: var(--card-radius);
    }
    
    .profile-stats-item {
        flex: 1;
        text-align: center;
        padding: 0.5rem;
    }
    
    .profile-stats-item:not(:last-child) {
        border-right: 1px solid rgba(0,0,0,0.05);
    }
    
    .profile-stats-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.25rem;
    }
    
    .profile-stats-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 500;
    }
    
    .detail-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .detail-item {
        padding: 1rem 0;
        display: flex;
        align-items: flex-start;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .detail-item:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 500;
        width: 180px;
        color: var(--text-muted);
    }
    
    .detail-value {
        flex: 1;
        font-weight: 500;
    }
    
    .contract-card {
        background-color: white;
        border-radius: var(--card-radius);
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        background-color: var(--bg-softer);
        border-radius: var(--card-radius);
    }
    
    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--text-muted);
    }
    
    .btn-action {
        border-radius: var(--button-radius);
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.2s;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    
    .settings-item {
        background-color: white;
        border-radius: var(--card-radius);
        padding: 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .settings-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .settings-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--icon-bg);
        color: var(--primary-color);
        font-size: 1.5rem;
        margin-right: 1rem;
    }
    
    .settings-details {
        flex: 1;
    }
    
    .settings-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .settings-description {
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    
    .progress {
        height: var(--progress-height);
        border-radius: 1rem;
        background-color: rgba(0,0,0,0.05);
        overflow: visible;
    }
    
    .progress-bar {
        border-radius: 1rem;
        background-color: var(--primary-color);
        position: relative;
    }
    
    /* Payment Methods */
    .payment-method-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: var(--card-radius);
        background-color: white;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .payment-method-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .payment-method-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        margin-right: 1rem;
    }
    
    .icon-credit-card {
        background-color: #4F46E5;
    }
    
    .icon-bank {
        background-color: #10B981;
    }
    
    .payment-method-details {
        flex: 1;
    }
    
    /* Documents */
    .document-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: var(--card-radius);
        background-color: white;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .document-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--icon-bg);
        color: var(--primary-color);
        font-size: 1.25rem;
        margin-right: 1rem;
    }
    
    .document-details {
        flex: 1;
    }
    
    /* Modal styling */
    .modal-content {
        border-radius: var(--card-radius);
        border: none;
        overflow: hidden;
    }
    
    .modal-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
    }
    
    .modal-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(71, 207, 209, 0.25);
    }
    
    .form-control {
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }
    
    .form-text {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    
    /* Mobile Navigation */
    .mobile-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: var(--light-color);
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        z-index: 1000;
        padding: 0.75rem;
    }
    
    .mobile-nav-wrapper {
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        position: relative;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }
    
    .mobile-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.5rem;
        color: var(--text-muted);
        text-decoration: none;
        position: relative;
        z-index: 2;
        flex: 1;
        text-align: center;
    }
    
    .mobile-nav-item.active {
        color: var(--primary-color);
    }
    
    .mobile-nav-icon {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }
    
    .mobile-nav-label {
        font-size: 0.7rem;
        font-weight: 500;
    }
    
    
    /* Animation keyframes */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease forwards;
    }
    
    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .dashboard-header h1 {
            font-size: 1.5rem;
        }
        
        .mobile-nav {
            display: flex;
            justify-content: space-around;
        }
        
        .container-fluid {
            padding-bottom: 80px;
        }
        
        .detail-item {
            flex-direction: column;
        }
        
        .detail-label {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid dashboard-container px-3">
    <!-- Dashboard Header -->
    <div class="dashboard-header animate-fade-in">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('tenant.dashboard') }}" class="back-button">
                    <i class="ti ti-chevron-left"></i>
                </a>
                <h1>My Profile</h1>
            </div>
            <div class="dashboard-header-actions">
                <button class="header-icon" data-bs-toggle="tooltip" title="Edit Profile" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="ti ti-pencil"></i>
                </button>
                <button class="header-icon" data-bs-toggle="tooltip" title="Settings">
                    <i class="ti ti-settings"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="mb-3">
        <!-- Success/Error messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show animate-fade-in" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Profile Header -->
        <div class="profile-card animate-fade-in mb-4">
            <div class="d-flex flex-column flex-md-row align-items-center">
                <img src="{{ Auth::user()->image ? asset(Auth::user()->image) : asset('assets/images/default_image.png') }}" 
                     alt="Profile Avatar" class="profile-avatar mb-3 mb-md-0 me-md-4">
                <div class="text-center text-md-start">
                    <h2 class="fw-bold mb-1">{{ Auth::user()->name }}</h2>
                    <p class="mb-1 text-muted">
                        <i class="ti ti-mail me-1"></i> {{ Auth::user()->email }}
                    </p>
                    <p class="mb-2 text-muted">
                        <i class="ti ti-phone me-1"></i> {{ Auth::user()->phone ?? 'No phone number' }}
                    </p>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="ti ti-pencil me-1"></i> Edit Profile
                    </button>
                </div>
            </div>
            
            <div class="profile-stats mt-4">
                <div class="profile-stats-item">
                    <div class="profile-stats-value">{{ $stats['total_contracts'] ?? 0 }}</div>
                    <div class="profile-stats-label">Contracts</div>
                </div>
                <div class="profile-stats-item">
                    <div class="profile-stats-value">{{ $stats['months_as_tenant'] ?? 0 }}</div>
                    <div class="profile-stats-label">Months as Tenant</div>
                </div>
                <div class="profile-stats-item">
                    <div class="profile-stats-value">{{ $stats['properties_rented'] ?? 0 }}</div>
                    <div class="profile-stats-label">Properties</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="profile-card animate-fade-in mb-4">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="ti ti-user me-2"></i>
                            Personal Information
                        </h3>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="ti ti-edit me-1"></i> Edit
                        </button>
                    </div>
                    
                    <ul class="detail-list mt-3">
                        <li class="detail-item">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Email Address</div>
                            <div class="detail-value">{{ Auth::user()->email }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Phone Number</div>
                            <div class="detail-value">{{ Auth::user()->phone ?? 'Not provided' }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Date of Birth</div>
                            <div class="detail-value">{{ Auth::user()->date_of_birth ? Carbon::parse(Auth::user()->date_of_birth)->format('M d, Y') : 'Not provided' }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Emergency Contact</div>
                            <div class="detail-value">{{ Auth::user()->emergency_contact ?? 'Not provided' }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Member Since</div>
                            <div class="detail-value">{{ Auth::user()->created_at->format('M d, Y') }}</div>
                        </li>
                    </ul>
                </div>
                
                <!-- Current Contract -->
                <div class="profile-card animate-fade-in mb-4">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="ti ti-file-contract me-2"></i>
                            Current Contract
                        </h3>
                    </div>
                    
                    @if($currentContract)
                    <ul class="detail-list mt-3">
                        <li class="detail-item">
                            <div class="detail-label">Property</div>
                            <div class="detail-value">{{ $currentContract->room->property->name }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Room Number</div>
                            <div class="detail-value">{{ $currentContract->room->room_number }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Room Type</div>
                            <div class="detail-value">{{ $currentContract->room->roomType->name }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Contract Period</div>
                            <div class="detail-value">{{ $currentContract->start_date->format('M d, Y') }} - {{ $currentContract->end_date->format('M d, Y') }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Monthly Rent</div>
                            <div class="detail-value">${{ number_format($currentContract->monthly_rent, 2) }}</div>
                        </li>
                        <li class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Active</span>
                            </div>
                        </li>
                    </ul>
                    
                    <div class="mt-4">
                        <h6 class="mb-2 fw-bold">Contract Duration</h6>
                        @php
                            $totalDays = $currentContract->start_date->diffInDays($currentContract->end_date);
                            $daysLeft = (int)now()->diffInDays($currentContract->end_date, false); // Cast to integer to remove decimals
                            $progress = max(0, min(100, ($totalDays - $daysLeft) / $totalDays * 100));
                        @endphp
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%" 
                                 aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>{{ $currentContract->start_date->format('M d, Y') }}</span>
                            <span>{{ $currentContract->end_date->format('M d, Y') }}</span>
                        </div>
                        <p class="small text-center mt-2 fw-medium {{ $daysLeft < 30 ? 'text-warning' : 'text-muted' }}">
                            {{ $daysLeft > 0 ? "$daysLeft days remaining" : "Contract expired" }}
                        </p>
                    </div>
                    @else
                    <div class="empty-state mt-3">
                        <div class="empty-state-icon">
                            <i class="ti ti-file-off"></i>
                        </div>
                        <h5 class="mt-3">No Active Contract</h5>
                        <p class="text-muted">You don't have an active contract at the moment.</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Account Settings -->
                <div class="profile-card animate-fade-in mb-4">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="ti ti-settings me-2"></i>
                            Account Settings
                        </h3>
                    </div>
                    
                    <div class="mt-3">
                        <div class="settings-item" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <div class="settings-icon">
                                <i class="ti ti-lock"></i>
                            </div>
                            <div class="settings-details">
                                <div class="settings-title">Change Password</div>
                                <div class="settings-description">Update your password for security</div>
                            </div>
                            <div>
                                <i class="ti ti-chevron-right text-muted"></i>
                            </div>
                        </div>
                        
                        <div class="settings-item" data-bs-toggle="modal" data-bs-target="#notificationSettingsModal">
                            <div class="settings-icon">
                                <i class="ti ti-bell"></i>
                            </div>
                            <div class="settings-details">
                                <div class="settings-title">Notification Settings</div>
                                <div class="settings-description">Manage your notification preferences</div>
                            </div>
                            <div>
                                <i class="ti ti-chevron-right text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tenant Information -->
                <div class="profile-card animate-fade-in mb-4">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="ti ti-info-circle me-2"></i>
                            Tenant Information
                        </h3>
                    </div>
                    
                    <div class="mt-3">
                        <div class="text-muted mb-3">
                            <p>As a tenant, you can view your contract details, access utility information, and manage your profile from this dashboard.</p>
                        </div>
                        
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3">
                                <i class="ti ti-file-invoice text-primary fs-24"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">View Your Invoices</h6>
                                <p class="text-muted small mb-0">Access and manage all your payment records</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="ti ti-bulb text-warning fs-24"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Track Utility Usage</h6>
                                <p class="text-muted small mb-0">Monitor your electricity and water consumption</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Documents -->
                <div class="profile-card animate-fade-in">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="ti ti-files me-2"></i>
                            Documents
                        </h3>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="ti ti-upload me-1"></i> Upload
                        </button>
                    </div>
                    
                    @if(isset($documents) && count($documents) > 0)
                    <div class="mt-3">
                        @foreach($documents as $document)
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="ti ti-file-text"></i>
                            </div>
                            <div class="document-details">
                                <div class="fw-bold">{{ $document->name }}</div>
                                <div class="text-muted small">{{ $document->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="d-flex">
                                <a href="{{ route('tenant.document.download', $document->id) }}" class="btn btn-sm btn-link" title="Download">
                                    <i class="ti ti-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-link text-danger delete-document" 
                                        data-document-id="{{ $document->id }}" 
                                        data-document-name="{{ $document->name }}"
                                        data-action-url="{{ route('documents.delete', $document->id) }}"
                                        title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state mt-3">
                        <div class="empty-state-icon">
                            <i class="ti ti-file-off"></i>
                        </div>
                        <h5 class="mt-3">No Documents</h5>
                        <p class="text-muted">Upload important documents like ID, contracts, etc.</p>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="ti ti-upload me-1"></i> Upload Document
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Navigation for small screens -->
    <div class="d-md-none mobile-nav">
        <div class="mobile-nav-wrapper">
            <a href="{{ route('tenant.dashboard') }}" class="mobile-nav-item">
                <i class="ti ti-home mobile-nav-icon"></i>
                <span class="mobile-nav-label">Home</span>
            </a>
            <a href="{{ route('tenant.invoices') }}" class="mobile-nav-item">
                <i class="ti ti-receipt mobile-nav-icon"></i>
                <span class="mobile-nav-label">Invoices</span>
            </a>
            <a href="{{ route('tenant.utility-bills') }}" class="mobile-nav-item">
                <i class="ti ti-bolt mobile-nav-icon"></i>
                <span class="mobile-nav-label">Utilities</span>
            </a>
            <a href="{{ route('tenant.profile') }}" class="mobile-nav-item active">
                <i class="ti ti-user mobile-nav-icon"></i>
                <span class="mobile-nav-label">Profile</span>
            </a>
            
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('tenant.profile.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ Auth::user()->first_name }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ Auth::user()->last_name }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="{{ Auth::user()->phone }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                               value="{{ Auth::user()->date_of_birth ? Carbon::parse(Auth::user()->date_of_birth)->format('Y-m-d') : '' }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="emergency_contact" class="form-label">Emergency Contact</label>
                        <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="{{ Auth::user()->emergency_contact }}">
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-action">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-action">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Notification Settings Modal -->
<div class="modal fade" id="notificationSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Notification Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email Notifications</label>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="email_payment_reminders" name="notifications[email_payment_reminders]" checked>
                            <label class="form-check-label" for="email_payment_reminders">
                                Payment Reminders
                            </label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="email_contract_updates" name="notifications[email_contract_updates]" checked>
                            <label class="form-check-label" for="email_contract_updates">
                                Contract Updates
                            </label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="email_maintenance_updates" name="notifications[email_maintenance_updates]" checked>
                            <label class="form-check-label" for="email_maintenance_updates">
                                Maintenance Updates
                            </label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="email_news_promotions" name="notifications[email_news_promotions]">
                            <label class="form-check-label" for="email_news_promotions">
                                News and Promotions
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">SMS Notifications</label>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="sms_payment_reminders" name="notifications[sms_payment_reminders]" checked>
                            <label class="form-check-label" for="sms_payment_reminders">
                                Payment Reminders
                            </label>
                        </div>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="sms_maintenance_updates" name="notifications[sms_maintenance_updates]">
                            <label class="form-check-label" for="sms_maintenance_updates">
                                Maintenance Updates
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-action">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });
        
        // Document deletion with SweetAlert2
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-document')) {
                const button = e.target.closest('.delete-document');
                const documentId = button.getAttribute('data-document-id');
                const documentName = button.getAttribute('data-document-name') || 'this document';
                const actionUrl = button.getAttribute('data-action-url');

                if (!actionUrl) {
                    console.error('Delete action URL not found on the button.');
                    Swal.fire('Error!', 'Cannot proceed with deletion. Action URL is missing.', 'error');
                    return;
                }

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfMeta) {
                    console.error('CSRF token meta tag not found.');
                    Swal.fire('Error!', 'Cannot proceed: CSRF token not found.', 'error');
                    return;
                }
                const csrfToken = csrfMeta.getAttribute('content');

                Swal.fire({
                    title: "Are you sure?",
                    text: `Document "${documentName}" will be permanently deleted! This action cannot be undone.`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel",
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    customClass: {
                        confirmButton: "swal2-confirm btn btn-danger me-2 mt-2",
                        cancelButton: "swal2-cancel btn btn-secondary mt-2",
                    },
                    buttonsStyling: false,
                    showCloseButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = actionUrl;

                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrfToken;

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';

                        form.appendChild(tokenInput);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });
    });
</script>
@endpush
