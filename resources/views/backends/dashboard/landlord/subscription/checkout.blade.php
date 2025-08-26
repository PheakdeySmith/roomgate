@extends('backends.layouts.app')

@section('title', 'Subscription Checkout')

@push('style')
<style>
    .checkout-card {
        max-width: 1000px;
        margin: 0 auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
    }
    
    .checkout-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        background-color: var(--bs-light);
    }
    
    .checkout-header h4 {
        margin-bottom: 0;
        font-weight: 600;
    }
    
    .payment-method-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
        margin-bottom: 0;
    }
    
    .payment-method-card.selected {
        border-color: var(--bs-primary);
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .payment-method-card:hover:not(.selected) {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .payment-method-radio {
        width: 20px;
        height: 20px;
    }
    
    .payment-logo {
        width: 50px;
        height: 40px;
        object-fit: contain;
        background-color: #ffffff;
        padding: 5px;
        border-radius: 5px;
    }
    
    .form-section {
        padding: 20px;
        background-color: var(--bs-body-bg);
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .form-section-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .order-summary {
        background-color: var(--bs-light);
        border-radius: 10px;
        padding: 20px;
    }
    
    .total-row {
        font-size: 1.2rem;
        padding-top: 15px;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .checkout-btn {
        padding: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 8px;
    }
    
    .plan-name {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .plan-features {
        background-color: var(--bs-body-bg);
        border-radius: 10px;
    }
    
    .feature-item {
        padding: 8px 15px;
        display: flex;
        align-items: center;
    }
    
    .feature-item i {
        font-size: 1.2rem;
        margin-right: 10px;
    }
    
    /* Form styling */
    .form-control {
        padding: 0.65rem 1rem;
        border-radius: 8px;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 767px) {
        .checkout-divider {
            border-right: none !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
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
                        <li class="breadcrumb-item"><a href="{{ route('landlord.subscription.plans') }}">Subscription Plans</a></li>
                        <li class="breadcrumb-item active">Checkout</li>
                    </ol>
                </div>
                <h4 class="page-title">Subscription Checkout</h4>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card checkout-card">
                <div class="checkout-header">
                    <h4><i class="ti ti-shopping-cart me-2"></i>Complete Your Subscription</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-7 checkout-divider pe-lg-4" style="border-right: 1px solid rgba(0, 0, 0, 0.1);">
                            <form action="{{ route('landlord.subscription.purchase', $plan->id) }}" method="POST" id="payment-form">
                                @csrf
                                
                                <div class="form-section mb-4">
                                    <h5 class="form-section-title">Select Payment Method</h5>
                                    
                                    <div class="payment-methods">
                                        <div class="row g-3">
                                            <div class="col-md-6 col-lg-6 mb-3">
                                                <div class="card payment-method-card" data-method="credit_card">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <img src="https://cdn-icons-png.flaticon.com/512/179/179457.png" class="payment-logo" alt="Credit Card">
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0">Credit Card</h6>
                                                                <small class="text-muted">Visa, Mastercard, Amex</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input payment-method-radio" type="radio" name="payment_method" value="credit_card" id="payment-credit-card" checked>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 col-lg-6 mb-3">
                                                <div class="card payment-method-card" data-method="paypal">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <img src="https://cdn-icons-png.flaticon.com/512/196/196566.png" class="payment-logo" alt="PayPal">
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0">PayPal</h6>
                                                                <small class="text-muted">Pay with PayPal</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input payment-method-radio" type="radio" name="payment_method" value="paypal" id="payment-paypal">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 col-lg-6 mb-3">
                                                <div class="card payment-method-card" data-method="bank_transfer">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <img src="https://cdn-icons-png.flaticon.com/512/2830/2830289.png" class="payment-logo" alt="Bank Transfer">
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0">Bank Transfer</h6>
                                                                <small class="text-muted">Direct bank transfer</small>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input payment-method-radio" type="radio" name="payment_method" value="bank_transfer" id="payment-bank-transfer">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-section mb-4">
                                    <h5 class="form-section-title">Billing Information</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="billing-name" class="form-label">Name on Card</label>
                                            <input type="text" class="form-control" id="billing-name" name="billing_name" value="{{ $user->name }}" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="billing-email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="billing-email" name="billing_email" value="{{ $user->email }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="credit-card-fields" class="form-section mb-4">
                                    <h5 class="form-section-title">Card Details</h5>
                                    
                                    <div class="mb-4">
                                        <label for="card-number" class="form-label">Card Number</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="card-number" name="card_number" placeholder="4242 4242 4242 4242">
                                            <span class="input-group-text">
                                                <i class="ti ti-credit-card"></i>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="card-expiry" class="form-label">Expiry Date</label>
                                                <input type="text" class="form-control" id="card-expiry" name="card_expiry" placeholder="MM/YY">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="card-cvc" class="form-label">CVC</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="card-cvc" name="card_cvc" placeholder="123">
                                                    <span class="input-group-text" title="3-digit code on the back of your card">
                                                        <i class="ti ti-help-circle"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg checkout-btn">
                                        <i class="ti ti-check me-1"></i> Complete Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-lg-5 ps-lg-4">
                            <div class="order-summary mb-4">
                                <h5 class="mb-4 pb-2 border-bottom">Order Summary</h5>
                                
                                <div class="plan-name">{{ $plan->name }}</div>
                                
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Subscription Fee</span>
                                    <span class="fw-bold">${{ number_format($plan->price, 2) }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Duration</span>
                                    <span>{{ $plan->duration_days }} days</span>
                                </div>
                                
                                <div class="d-flex justify-content-between total-row mt-2">
                                    <span class="fw-bold">Total</span>
                                    <span class="fw-bold text-primary">${{ number_format($plan->price, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="plan-features p-4">
                                <h5 class="mb-3 pb-2 border-bottom">Plan Features</h5>
                                
                                @php 
                                    $features = json_decode($plan->features, true) ?? [];
                                @endphp
                                
                                <div class="feature-list">
                                    @foreach($features as $feature => $enabled)
                                        @if($enabled)
                                            <div class="feature-item">
                                                <i class="ti ti-check-circle text-success"></i>
                                                <span>{{ ucwords(str_replace('_', ' ', $feature)) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                
                                <div class="text-center mt-4">
                                    <a href="{{ route('landlord.subscription.plans') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-arrow-left me-1"></i> Back to Plans
                                    </a>
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
<script>
    $(document).ready(function() {
        // Initialize card form elements with better formatting
        function initCardFormatting() {
            // Credit card number formatting (add spaces every 4 digits)
            $('#card-number').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                let formattedValue = '';
                
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formattedValue += ' ';
                    }
                    formattedValue += value[i];
                }
                
                $(this).val(formattedValue);
                
                // Limit to 19 characters (16 digits + 3 spaces)
                if (value.length > 16) {
                    $(this).val(formattedValue.substring(0, 19));
                }
            });
            
            // Expiry date formatting (MM/YY)
            $('#card-expiry').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                let formattedValue = '';
                
                if (value.length > 0) {
                    formattedValue = value.substring(0, 2);
                    if (value.length > 2) {
                        formattedValue += '/' + value.substring(2, 4);
                    }
                }
                
                $(this).val(formattedValue);
            });
            
            // CVC (limit to 3 or 4 digits)
            $('#card-cvc').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length > 4) {
                    value = value.substring(0, 4);
                }
                $(this).val(value);
            });
        }
        
        // Handle payment method selection
        $('.payment-method-card').on('click', function() {
            const method = $(this).data('method');
            
            // Update UI
            $('.payment-method-card').removeClass('selected');
            $(this).addClass('selected');
            
            // Check the radio button
            $(`#payment-${method}`).prop('checked', true);
            
            // Show/hide credit card fields with animation
            if (method === 'credit_card') {
                $('#credit-card-fields').slideDown(300);
            } else {
                $('#credit-card-fields').slideUp(300);
            }
        });
        
        // Form validation
        $('#payment-form').on('submit', function(e) {
            const selectedMethod = $('input[name="payment_method"]:checked').val();
            
            if (selectedMethod === 'credit_card') {
                // Validate card fields
                const cardNumber = $('#card-number').val().replace(/\s/g, '');
                const cardExpiry = $('#card-expiry').val();
                const cardCvc = $('#card-cvc').val();
                
                if (cardNumber.length < 16) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Invalid Card Number',
                        text: 'Please enter a valid card number',
                        icon: 'error'
                    });
                    return false;
                }
                
                if (cardExpiry.length < 5) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Invalid Expiry Date',
                        text: 'Please enter a valid expiry date (MM/YY)',
                        icon: 'error'
                    });
                    return false;
                }
                
                if (cardCvc.length < 3) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Invalid CVC',
                        text: 'Please enter a valid security code',
                        icon: 'error'
                    });
                    return false;
                }
            }
            
            // Show loading state
            $(this).find('button[type="submit"]').html('<i class="ti ti-loader animate-spin me-1"></i> Processing Payment...').attr('disabled', true);
            
            // Allow form submission if validation passes
            return true;
        });
        
        // Initialize components
        initCardFormatting();
        
        // Trigger click on the initially selected payment method
        $('.payment-method-card[data-method="credit_card"]').click();
    });
</script>
@endpush
