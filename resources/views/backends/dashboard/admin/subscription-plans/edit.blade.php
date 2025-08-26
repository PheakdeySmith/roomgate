@extends('backends.layouts.app')

@section('title', 'Edit Subscription Plan')

@push('style')
<style>
    .feature-container {
        margin-bottom: 1rem;
    }
    
    .add-feature-btn {
        margin-top: 0.5rem;
    }
    
    .remove-feature-btn {
        margin-left: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Plan Details</h5>
                    
                    <form action="{{ route('admin.subscription-plans.update', $plan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                                    <div class="form-text">e.g., "Pro Monthly" or "Pro Annual"</div>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            {{-- START: NEW FIELD --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="plan_group" class="form-label">Plan Group <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('plan_group') is-invalid @enderror" id="plan_group" name="plan_group" value="{{ old('plan_group', $plan->plan_group) }}" required>
                                    <div class="form-text">e.g., "pro". Use the same group for monthly/annual versions.</div>
                                    @error('plan_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            {{-- END: NEW FIELD --}}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $plan->price) }}" required>
                                    <div class="form-text">The total price for the duration (e.g., 120 for an annual plan).</div>
                                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- START: NEW FIELD --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="base_monthly_price" class="form-label">Base Monthly Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('base_monthly_price') is-invalid @enderror" id="base_monthly_price" name="base_monthly_price" value="{{ old('base_monthly_price', $plan->base_monthly_price) }}" required>
                                    <div class="form-text">Original monthly price for calculating discounts.</div>
                                    @error('base_monthly_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            {{-- END: NEW FIELD --}}
                        </div>
                        
                        {{-- ... rest of your form fields (duration, limits, description, etc.) ... --}}
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Plan Details</h5>
                    
                    <form action="{{ route('admin.subscription-plans.update', $plan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $plan->price) }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_days" class="form-label">Duration (Days) <span class="text-danger">*</span></label>
                                    <select class="form-select @error('duration_days') is-invalid @enderror" id="duration_days" name="duration_days" required>
                                        <option value="7" {{ old('duration_days', $plan->duration_days) == 7 ? 'selected' : '' }}>1 Week (7 days)</option>
                                        <option value="30" {{ old('duration_days', $plan->duration_days) == 30 ? 'selected' : '' }}>1 Month (30 days)</option>
                                        <option value="90" {{ old('duration_days', $plan->duration_days) == 90 ? 'selected' : '' }}>3 Months (90 days)</option>
                                        <option value="180" {{ old('duration_days', $plan->duration_days) == 180 ? 'selected' : '' }}>6 Months (180 days)</option>
                                        <option value="365" {{ old('duration_days', $plan->duration_days) == 365 ? 'selected' : '' }}>1 Year (365 days)</option>
                                        <option value="0" {{ old('duration_days', $plan->duration_days) == 0 ? 'selected' : '' }}>Lifetime</option>
                                    </select>
                                    @error('duration_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="properties_limit" class="form-label">Properties Limit <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('properties_limit') is-invalid @enderror" id="properties_limit" name="properties_limit" value="{{ old('properties_limit', $plan->properties_limit) }}" required min="1">
                                    @error('properties_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rooms_limit" class="form-label">Rooms Limit <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('rooms_limit') is-invalid @enderror" id="rooms_limit" name="rooms_limit" value="{{ old('rooms_limit', $plan->rooms_limit) }}" required min="1">
                                    @error('rooms_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label d-block">Options</label>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" {{ old('is_featured', $plan->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">Featured Plan</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $plan->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Features</label>
                            <div id="features-container">
                                @if($plan->features)
                                    @foreach(json_decode($plan->features) as $feature)
                                        <div class="feature-container d-flex mb-2">
                                            <input type="text" class="form-control" name="features[]" value="{{ $feature }}">
                                            <button type="button" class="btn btn-danger remove-feature-btn">
                                                <i class="ti ti-minus"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="feature-container d-flex">
                                        <input type="text" class="form-control" name="features[]" placeholder="Enter a feature">
                                        <button type="button" class="btn btn-danger remove-feature-btn">
                                            <i class="ti ti-minus"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-info add-feature-btn">
                                <i class="ti ti-plus me-1"></i> Add Feature
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Add new feature field
        $('.add-feature-btn').click(function() {
            var featureField = `
                <div class="feature-container d-flex mt-2">
                    <input type="text" class="form-control" name="features[]" placeholder="Enter a feature">
                    <button type="button" class="btn btn-danger remove-feature-btn">
                        <i class="ti ti-minus"></i>
                    </button>
                </div>
            `;
            $('#features-container').append(featureField);
        });
        
        // Remove feature field
        $(document).on('click', '.remove-feature-btn', function() {
            $(this).closest('.feature-container').remove();
        });
    });
</script>
@endpush
