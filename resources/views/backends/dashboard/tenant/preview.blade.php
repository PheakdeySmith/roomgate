@extends('backends.layouts.app')

@section('title', 'Dashboard Redesign Preview')

@push('style')
<style>
    .preview-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }
    
    .preview-header {
        margin-bottom: 2rem;
    }
    
    .preview-card {
        border-radius: 1rem;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .preview-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: translateY(-5px);
    }
    
    .preview-card-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #4F46E5, #7C3AED);
        color: white;
    }
    
    .preview-card-body {
        padding: 1.5rem;
    }
    
    .preview-btn {
        display: inline-block;
        font-weight: 600;
        text-align: center;
        border: none;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .preview-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .preview-btn-primary {
        background-color: #4F46E5;
        color: white;
    }
    
    .preview-btn-primary:hover {
        background-color: #4338CA;
    }
    
    .preview-btn-secondary {
        background-color: #8B5CF6;
        color: white;
    }
    
    .preview-btn-secondary:hover {
        background-color: #7C3AED;
    }
    
    .preview-btn-success {
        background-color: #10B981;
        color: white;
    }
    
    .preview-btn-success:hover {
        background-color: #059669;
    }
    
    .preview-screenshot {
        width: 100%;
        border-radius: 0.5rem;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    [data-bs-theme="dark"] .preview-screenshot {
        border-color: rgba(255,255,255,0.1);
    }
    
    .feature-list {
        padding-left: 1.5rem;
    }
    
    .feature-list li {
        margin-bottom: 0.75rem;
        position: relative;
    }
    
    .feature-list li::before {
        content: "✓";
        position: absolute;
        left: -1.5rem;
        color: #10B981;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="preview-container">
    <div class="preview-header">
        <h1 class="mb-3">Tenant Dashboard Redesign Preview</h1>
        <p class="lead">Select a dashboard design to view:</p>
    </div>
    
    <div class="row">
        <!-- Original Design Card -->
        <div class="col-md-4 mb-4">
            <div class="preview-card">
                <div class="preview-card-header">
                    <h3 class="mb-0">Current Design</h3>
                </div>
                <div class="preview-card-body">
                    <p>The current tenant dashboard design.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('tenant.dashboard') }}" class="preview-btn preview-btn-primary">View Current Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- New Design Card -->
        <div class="col-md-4 mb-4">
            <div class="preview-card">
                <div class="preview-card-header">
                    <h3 class="mb-0">New Design</h3>
                </div>
                <div class="preview-card-body">
                    <p>The completely redesigned dashboard with modern UI elements.</p>
                    <div class="d-grid gap-2">
                        <button id="view-new-design" class="preview-btn preview-btn-secondary">View New Dashboard</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Simplified Design Card -->
        <div class="col-md-4 mb-4">
            <div class="preview-card">
                <div class="preview-card-header">
                    <h3 class="mb-0">Simplified Design</h3>
                </div>
                <div class="preview-card-body">
                    <p>A simplified version of the new design with fewer custom styles.</p>
                    <div class="d-grid gap-2">
                        <button id="view-simplified-design" class="preview-btn preview-btn-success">View Simplified Dashboard</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="preview-card">
                <div class="preview-card-header">
                    <h3 class="mb-0">Key Features of the New Design</h3>
                </div>
                <div class="preview-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Design Improvements</h4>
                            <ul class="feature-list">
                                <li>Modern, clean UI with improved card design</li>
                                <li>Gradient accents and subtle animations</li>
                                <li>Improved color scheme that works in both light and dark modes</li>
                                <li>Better spacing and typography for improved readability</li>
                                <li>Mobile-optimized layout with bottom navigation bar</li>
                                <li>Custom icons and visual elements for better engagement</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h4>Functional Improvements</h4>
                            <ul class="feature-list">
                                <li>User-friendly messaging for zero-value states</li>
                                <li>Enhanced charts with better visualization</li>
                                <li>Improved invoice modal with clearer information hierarchy</li>
                                <li>More intuitive navigation and content structure</li>
                                <li>Utility usage progress bars for quick comparison</li>
                                <li>Notification system with color-coded indicators</li>
                            </ul>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Handle new design button click
        document.getElementById('view-new-design').addEventListener('click', function() {
            // Create form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("tenant.dashboard") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add design parameter
            const designParam = document.createElement('input');
            designParam.type = 'hidden';
            designParam.name = 'view_design';
            designParam.value = 'new';
            form.appendChild(designParam);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        });
        
        // Handle simplified design button click
        document.getElementById('view-simplified-design').addEventListener('click', function() {
            // Create form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("tenant.dashboard") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add design parameter
            const designParam = document.createElement('input');
            designParam.type = 'hidden';
            designParam.name = 'view_design';
            designParam.value = 'simplified';
            form.appendChild(designParam);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        });
    });
</script>
@endpush
