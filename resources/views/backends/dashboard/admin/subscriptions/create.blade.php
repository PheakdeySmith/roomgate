@extends('backends.layouts.app')

@section('title', 'Create User Subscription')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index') }}">User Subscriptions</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Create User Subscription</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Subscription Details</h5>
                    
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Warning!</strong> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('show_existing_options'))
                        <div class="card border-warning mb-4">
                            <div class="card-header bg-warning-subtle">
                                <h5 class="mb-0">Existing Active Subscription</h5>
                            </div>
                            <div class="card-body">
                                <p>This user already has an active subscription. How would you like to proceed?</p>
                                
                                <form action="{{ route('admin.subscriptions.store') }}" method="POST" id="options-form">
                                    @csrf
                                    
                                    <!-- Re-include all the previous form values -->
                                    @foreach(old() as $key => $value)
                                        @if(is_array($value))
                                            @foreach($value as $k => $v)
                                                <input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}">
                                            @endforeach
                                        @else
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endif
                                    @endforeach
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="handle_existing" id="cancel_existing" value="cancel" checked>
                                        <label class="form-check-label" for="cancel_existing">
                                            <strong>Cancel existing subscription</strong> - Cancel the current subscription and create a new one
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="handle_existing" id="keep_existing" value="keep">
                                        <label class="form-check-label" for="keep_existing">
                                            <strong>Keep both subscriptions</strong> - Create a new subscription without canceling the existing one
                                        </label>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-warning">
                                            Proceed with Selected Option
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                    <form action="{{ route('admin.subscriptions.store') }}" method="POST" id="subscription-form">
                        @csrf
                    @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Landlord <span class="text-danger">*</span></label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">Select Landlord</option>
                                        @foreach($landlords as $landlord)
                                            <option value="{{ $landlord->id }}" {{ old('user_id') == $landlord->id ? 'selected' : '' }}>
                                                {{ $landlord->name }} ({{ $landlord->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subscription_plan_id" class="form-label">Subscription Plan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('subscription_plan_id') is-invalid @enderror" id="subscription_plan_id" name="subscription_plan_id" required>
                                        <option value="">Select Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}
                                                data-price="{{ $plan->price }}" data-duration="{{ $plan->formatted_duration }}">
                                                {{ $plan->name }} - {{ $plan->formatted_price }} ({{ $plan->formatted_duration }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subscription_plan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_status" class="form-label">Payment Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                        <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="trial" {{ old('payment_status') == 'trial' ? 'selected' : '' }}>Trial</option>
                                    </select>
                                    @error('payment_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                                        <option value="">Select Payment Method</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                        <option value="stripe" {{ old('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_id" class="form-label">Transaction ID</label>
                                    <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}">
                                    @error('transaction_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount_paid" class="form-label">Amount Paid ($)</label>
                                    <input type="number" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid" value="{{ old('amount_paid') }}">
                                    <small class="text-muted">Leave empty to use plan price</small>
                                    @error('amount_paid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Subscription</button>
                        </div>
                    @if(!session('show_existing_options'))
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Update amount paid when plan changes
        $('#subscription_plan_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var price = selectedOption.data('price');
            if (price) {
                $('#amount_paid').val(price);
            }
        });
        
        // Trigger change on page load if plan is selected
        if ($('#subscription_plan_id').val()) {
            $('#subscription_plan_id').trigger('change');
        }
        
        // We don't need the proceed button handler anymore since we're using a separate form
        
        // Check for active subscription when user is selected
        $('#user_id').change(function() {
            const userId = $(this).val();
            if (userId) {
                // We'll rely on the backend check after form submission
                console.log("Selected user ID:", userId);
            }
        });
    });
</script>
@endpush
