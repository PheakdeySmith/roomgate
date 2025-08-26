@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
@endphp

@extends('backends.layouts.app')

@section('title', 'My Account | RoomGate')

@push('style')
<style>
    /* Profile Styles */
    .avatar-xl {
        width: 120px;
        height: 120px;
    }
    
    .profile-tab-nav {
        min-width: 250px;
    }
    
    .tab-content {
        flex: 1;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .nav-pills .nav-link.active {
        background-color: #47CFD1;
    }
    
    .avatar-upload {
        position: relative;
        max-width: 180px;
        margin: 0 auto;
    }
    
    .avatar-edit {
        position: absolute;
        right: 45px;
        z-index: 1;
        bottom: 5px;
    }
    
    .avatar-edit input {
        display: none;
    }
    
    .avatar-edit label {
        display: inline-block;
        width: 36px;
        height: 36px;
        margin-bottom: 0;
        border-radius: 100%;
        background: #47CFD1;
        border: 1px solid transparent;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
        cursor: pointer;
        font-weight: normal;
        transition: all .2s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .avatar-preview {
        width: 150px;
        height: 150px;
        position: relative;
        border-radius: 100%;
        border: 5px solid #F0F0F0;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
    }
    
    .avatar-preview > div {
        width: 100%;
        height: 100%;
        border-radius: 100%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
    
    .qr-code-upload {
        position: relative;
        border: 2px dashed #d9d9d9;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s;
        background-color: #f9f9f9;
        margin-bottom: 15px;
        height: 230px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .qr-code-upload:hover {
        border-color: #47CFD1;
    }
    
    .qr-code-upload img {
        max-width: 100%;
        max-height: 170px;
        margin-bottom: 10px;
    }
    
    .qr-code-upload input[type="file"] {
        display: none;
    }
    
    .qr-code-preview {
        width: 100%;
        height: 170px;
        object-fit: contain;
    }
    
    .qr-code-upload .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
        border-radius: 8px;
    }
    
    .qr-code-upload:hover .overlay {
        opacity: 1;
    }
    
    .qr-code-upload .overlay-content {
        color: white;
        text-align: center;
    }
    
    .qr-code-upload .overlay-content .d-flex {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        align-items: center; /* Ensure vertical alignment */
    }
    
    /* Ensure buttons have exactly the same height and no margins */
    .qr-code-upload .overlay-content .btn {
        margin: 0 !important;
        padding: 0.375rem 0.75rem;
        height: 31px !important; /* Fixed height for all buttons */
        line-height: 1.5;
        vertical-align: middle;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Target any margin-bottom that might be set elsewhere */
    .qr-code-upload .overlay-content .btn.btn-danger,
    .qr-code-upload .overlay-content .btn.btn-primary {
        margin-bottom: 0 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Account</li>
                    </ol>
                </div>
                <h4 class="page-title">My Account</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="profile-tab-nav border-right">
                                <div class="p-4">
                                    <div class="avatar-upload mb-3">
                                        <div class="avatar-preview">
                                            <div id="imagePreview" style="background-image: url('{{ Auth::user()->image ? asset(Auth::user()->image) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=7F9CF5&background=EBF4FF' }}');">
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type='file' id="imageUpload" name="image" form="profileForm" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload"><i class="ti ti-camera"></i></label>
                                        </div>
                                    </div>
                                    <h4 class="text-center mb-0">{{ Auth::user()->name }}</h4>
                                    <p class="text-center text-muted">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link active" id="account-tab" data-bs-toggle="pill" href="#account" role="tab" aria-controls="account" aria-selected="true">
                                        <i class="ti ti-user me-2"></i> 
                                        Account Information
                                    </a>
                                    <a class="nav-link" id="password-tab" data-bs-toggle="pill" href="#password" role="tab" aria-controls="password" aria-selected="false">
                                        <i class="ti ti-lock me-2"></i> 
                                        Password
                                    </a>
                                    <a class="nav-link" id="qr-codes-tab" data-bs-toggle="pill" href="#qr-codes" role="tab" aria-controls="qr-codes" aria-selected="false">
                                        <i class="ti ti-qrcode me-2"></i> 
                                        Payment QR Codes
                                    </a>
                                    <a class="nav-link" id="currency-tab" data-bs-toggle="pill" href="#currency" role="tab" aria-controls="currency" aria-selected="false">
                                        <i class="ti ti-currency-dollar me-2"></i> 
                                        Currency Settings
                                    </a>
                                    <a class="nav-link" id="security-tab" data-bs-toggle="pill" href="#security" role="tab" aria-controls="security" aria-selected="false">
                                        <i class="ti ti-shield-lock me-2"></i> 
                                        Security
                                    </a>
                                    <a class="nav-link" id="notification-tab" data-bs-toggle="pill" href="#notification" role="tab" aria-controls="notification" aria-selected="false">
                                        <i class="ti ti-bell me-2"></i> 
                                        Notification
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content p-4" id="v-pills-tabContent">
                                <!-- Account Tab Content -->
                                <div class="tab-pane fade show active" id="account" role="tabpanel" aria-labelledby="account-tab">
                                    <h3 class="mb-4">Account Information</h3>
                                    <form id="profileForm" method="POST" action="{{ route('landlord.profile.update') }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Full Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ Auth::user()->phone }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="company">Company</label>
                                                    <input type="text" class="form-control" id="company" name="company" value="{{ Auth::user()->company ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Update Profile</button>
                                            <button type="reset" class="btn btn-light">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Password Tab Content -->
                                <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                    <h3 class="mb-4">Password Settings</h3>
                                    <form method="POST" action="{{ route('landlord.password.update') }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="current_password">Current Password</label>
                                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="new_password">New Password</label>
                                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="new_password_confirmation">Confirm New Password</label>
                                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Update Password</button>
                                            <button type="reset" class="btn btn-light">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- QR Codes Tab Content -->
                                <div class="tab-pane fade" id="qr-codes" role="tabpanel" aria-labelledby="qr-codes-tab">
                                    <h3 class="mb-4">Payment QR Codes</h3>
                                    <p class="text-muted mb-4">
                                        Upload your payment QR codes that will appear on the invoices. You can upload up to 2 QR codes.
                                    </p>
                                    
                                    <form method="POST" action="{{ route('landlord.qrcodes.update') }}" enctype="multipart/form-data" id="qrcodesForm">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>QR Code 1</label>
                                                <div class="qr-code-upload">
                                                    @if(Auth::user()->qr_code_1)
                                                        <img src="{{ asset('uploads/qrcodes/' . Auth::user()->qr_code_1) }}?t={{ time() }}" 
                                                            alt="QR Code 1" 
                                                            class="qr-code-preview"
                                                            onerror="this.onerror=null; this.src='{{ asset('assets/images/qr-placeholder.png') }}';">
                                                        <div class="overlay">
                                                            <div class="overlay-content">
                                                                <button type="button" class="btn btn-sm btn-danger mb-2 remove-qr" data-qr="1">
                                                                    <i class="ti ti-trash me-1"></i> Remove
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-primary replace-qr" data-qr="1">
                                                                    <i class="ti ti-replace me-1"></i> Replace
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="remove_qr_1" id="remove_qr_1" value="0">
                                                    @else
                                                        <i class="ti ti-qrcode text-muted mb-2" style="font-size: 48px;"></i>
                                                        <h6>Upload QR Code</h6>
                                                        <p class="text-muted small">Click to browse or drag and drop<br>PNG or JPG, max 2MB</p>
                                                        <button type="button" class="btn btn-sm btn-primary upload-qr" data-qr="1">
                                                            <i class="ti ti-upload me-1"></i> Upload
                                                        </button>
                                                    @endif
                                                    <input type="file" name="qr_code_1" id="qr_code_1" accept=".png, .jpg, .jpeg" style="position: absolute; opacity: 0; width: 0; height: 0;" data-qr="1">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label>QR Code 2</label>
                                                <div class="qr-code-upload">
                                                    @if(Auth::user()->qr_code_2)
                                                        <img src="{{ asset('uploads/qrcodes/' . Auth::user()->qr_code_2) }}?t={{ time() }}" 
                                                            alt="QR Code 2" 
                                                            class="qr-code-preview"
                                                            onerror="this.onerror=null; this.src='{{ asset('assets/images/qr-placeholder.png') }}';">
                                                        <div class="overlay">
                                                            <div class="overlay-content">
                                                                <button type="button" class="btn btn-sm btn-danger mb-2 remove-qr" data-qr="2">
                                                                    <i class="ti ti-trash me-1"></i> Remove
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-primary replace-qr" data-qr="2">
                                                                    <i class="ti ti-replace me-1"></i> Replace
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="remove_qr_2" id="remove_qr_2" value="0">
                                                    @else
                                                        <i class="ti ti-qrcode text-muted mb-2" style="font-size: 48px;"></i>
                                                        <h6>Upload QR Code</h6>
                                                        <p class="text-muted small">Click to browse or drag and drop<br>PNG or JPG, max 2MB</p>
                                                        <button type="button" class="btn btn-sm btn-primary upload-qr" data-qr="2">
                                                            <i class="ti ti-upload me-1"></i> Upload
                                                        </button>
                                                    @endif
                                                    <input type="file" name="qr_code_2" id="qr_code_2" accept=".png, .jpg, .jpeg" style="position: absolute; opacity: 0; width: 0; height: 0;" data-qr="2">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary">Save QR Codes</button>
                                            <button type="reset" class="btn btn-light">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Currency Settings Tab Content -->
                                <div class="tab-pane fade" id="currency" role="tabpanel" aria-labelledby="currency-tab">
                                    <h3 class="mb-4">Currency Settings</h3>
                                    <p class="text-muted mb-4">
                                        Configure your preferred currency and exchange rate. This will affect how prices are displayed throughout the system.
                                    </p>
                                    
                                    <form method="POST" action="{{ route('landlord.currency.update') }}" id="currencyForm">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="currency_code">Base Currency</label>
                                                    <select class="form-select" id="currency_code" name="currency_code">
                                                        @foreach($currencies as $code => $name)
                                                            <option value="{{ $code }}" {{ Auth::user()->currency_code === $code ? 'selected' : '' }}>
                                                                {{ $code }} - {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="form-text text-muted">
                                                        Select the primary currency you want to use for your property management.
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exchange_rate">Exchange Rate</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">1 USD =</span>
                                                        <input type="number" class="form-control" id="exchange_rate" name="exchange_rate" 
                                                            value="{{ number_format(Auth::user()->exchange_rate, 2, '.', '') }}" step="0.01" min="0.01">
                                                        <span class="input-group-text currency-code">{{ Auth::user()->currency_code }}</span>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        Exchange rates are automatically updated when you select a currency. Your manually adjusted rates will be preserved.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary">Save Currency Settings</button>
                                            <button type="button" class="btn btn-light" onclick="window.refreshExchangeRate()">
                                                <i class="ti ti-refresh"></i> Refresh Rate
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Security Tab Content -->
                                <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                    <h3 class="mb-4">Security Settings</h3>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <h5>Two Factor Authentication</h5>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="twoFAToggle">
                                                    <label class="form-check-label" for="twoFAToggle">Enable two-factor authentication</label>
                                                </div>
                                                <p class="text-muted small mt-2">When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.</p>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <h5>Browser Sessions</h5>
                                                <p class="text-muted small">Manage and log out your active sessions on other browsers and devices.</p>
                                                <button type="button" class="btn btn-danger">Log Out Other Browser Sessions</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Notification Tab Content -->
                                <div class="tab-pane fade" id="notification" role="tabpanel" aria-labelledby="notification-tab">
                                    <h3 class="mb-4">Notification Settings</h3>
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="mb-3">Email Notifications</h5>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="emailPaymentNotifications" checked>
                                                <label class="form-check-label" for="emailPaymentNotifications">
                                                    Payment Notifications
                                                </label>
                                                <p class="text-muted small mt-1">Receive emails when a payment is made or when an invoice is due</p>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="emailContractNotifications" checked>
                                                <label class="form-check-label" for="emailContractNotifications">
                                                    Contract Notifications
                                                </label>
                                                <p class="text-muted small mt-1">Receive emails about contract expirations and renewals</p>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="emailSystemNotifications" checked>
                                                <label class="form-check-label" for="emailSystemNotifications">
                                                    System Notifications
                                                </label>
                                                <p class="text-muted small mt-1">Receive emails about system updates and maintenance</p>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="emailMarketingNotifications">
                                                <label class="form-check-label" for="emailMarketingNotifications">
                                                    Marketing Emails
                                                </label>
                                                <p class="text-muted small mt-1">Receive emails about new features and promotions</p>
                                            </div>
                                            
                                            <h5 class="mb-3 mt-4">SMS Notifications</h5>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="smsPaymentNotifications">
                                                <label class="form-check-label" for="smsPaymentNotifications">
                                                    Payment Notifications
                                                </label>
                                                <p class="text-muted small mt-1">Receive SMS when a payment is made or when an invoice is due</p>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="smsSystemNotifications">
                                                <label class="form-check-label" for="smsSystemNotifications">
                                                    System Notifications
                                                </label>
                                                <p class="text-muted small mt-1">Receive SMS about important system alerts</p>
                                            </div>
                                            
                                            <div class="mt-4">
                                                <button type="button" class="btn btn-primary">Save Notification Settings</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('js/currency-exchange.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Profile image preview
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        $("#imageUpload").change(function() {
            readURL(this);
        });
        
        // QR Code Upload handlers - apply to both existing and dynamically created elements
        function initQrCodeHandlers() {
            // Click handlers for upload and replace buttons
            $(document).on('click', '.upload-qr', function() {
                const qrNumber = $(this).data('qr');
                $(`#qr_code_${qrNumber}`).click();
            });
            
            $(document).on('click', '.replace-qr', function() {
                const qrNumber = $(this).data('qr');
                $(`#qr_code_${qrNumber}`).click();
            });
            
            // Remove handler
            $(document).on('click', '.remove-qr', function() {
                const qrNumber = $(this).data('qr');
                
                // Show confirmation dialog
                Swal.fire({
                    title: 'Remove QR Code?',
                    text: "This will permanently remove this QR code from your profile.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#remove_qr_${qrNumber}`).val('1');
                        
                        // Show loading state
                        Swal.fire({
                            title: 'Removing QR Code...',
                            html: 'Please wait...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Submit the form immediately to perform the server-side removal
                        $('#qrcodesForm').submit();
                    }
                });
            });
            
            // Change handler for file inputs
            $(document).on('change', 'input[name^="qr_code_"]', function() {
                const qrNumber = $(this).data('qr');
                const file = this.files[0];
                
                if (file) {
                    console.log(`QR Code ${qrNumber} file selected:`, file);
                    const reader = new FileReader();
                    const qrUploadDiv = $(this).closest('.qr-code-upload');
                    
                    reader.onload = function(e) {
                        // Remove existing content but keep the file input
                        qrUploadDiv.find('*:not(input[type="file"])').remove();
                        
                        // Add new preview elements
                        qrUploadDiv.prepend(`
                            <img src="${e.target.result}" alt="QR Code ${qrNumber}" class="qr-code-preview" onerror="this.onerror=null; this.src='{{ asset('assets/images/qr-placeholder.png') }}';">
                            <div class="overlay">
                                <div class="overlay-content">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-qr" data-qr="${qrNumber}" style="margin: 0;">
                                            <i class="ti ti-trash me-1"></i> Remove
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary replace-qr" data-qr="${qrNumber}" style="margin: 0;">
                                            <i class="ti ti-replace me-1"></i> Replace
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="remove_qr_${qrNumber}" id="remove_qr_${qrNumber}" value="0">
                        `);
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
            
            // Add a submit handler to verify the form before submission
            $('#qrcodesForm').on('submit', function(e) {
                // Log what's being submitted
                console.log('Submitting form with files:', {
                    'qr_code_1': $('#qr_code_1')[0].files[0],
                    'qr_code_2': $('#qr_code_2')[0].files[0],
                    'remove_qr_1': $('#remove_qr_1').val(),
                    'remove_qr_2': $('#remove_qr_2').val()
                });
            });
        }
        
        // Initialize QR code handlers
        initQrCodeHandlers();
        
        // Update currency code in exchange rate display
        document.getElementById('currency_code').addEventListener('change', function() {
            const currencySpan = document.querySelector('.input-group-text:last-child');
            if (currencySpan) {
                currencySpan.textContent = this.value;
            }
        });
    });
</script>
@endpush
