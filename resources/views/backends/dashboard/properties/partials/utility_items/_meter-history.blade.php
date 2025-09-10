<!-- Mobile-optimized history table with responsive hiding for very small screens -->
<style>
    /* Custom CSS to handle very small screens */
    @media (max-width: 360px) {
        .hide-on-xs {
            display: none !important;
        }
        .full-width-on-xs {
            width: 50% !important;
        }
        
        /* Make everything more compact on tiny screens */
        .meter-history-table .d-flex {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
        
        /* Fix the spacing for header row */
        .meter-history-table .d-flex.bg-secondary-subtle {
            padding: 5px 10px !important;
            font-size: 10px !important;
        }
        
        /* Fix badge sizing */
        .meter-history-table .badge {
            font-size: 10px;
            padding: 3px 5px;
        }
    }
    
    /* Make pagination more compact on mobile */
    @media (max-width: 420px) {
        .pagination-mobile .page-link {
            width: 30px;
            height: 30px;
            line-height: 28px;
            padding: 0;
            text-align: center;
            font-size: 0.8125rem;
            margin: 0 2px;
        }
        
        /* Even more compact for iPhone SE and similar */
        @media (max-width: 360px) {
            .pagination-mobile .page-link {
                width: 24px;
                height: 24px;
                line-height: 22px;
                font-size: 0.7rem;
                margin: 0;
            }
            
            /* Hide some pagination items on very small screens */
            .pagination-mobile .d-none.d-sm-block {
                display: none !important;
            }
        }
    }
    
    /* Add overall scaling for tiny screens */
    @media (max-width: 320px) {
        .meter-history-table {
            font-size: 11px;
        }
    }
</style>

<div class="meter-history-table bg-light-subtle rounded-3">
    <div class="d-flex bg-secondary-subtle py-2 px-3 rounded-top-3 fw-medium text-muted fs-12">
        <!-- Hide date on very small screens (≤360px) -->
        <div class="hide-on-xs" style="width: 35%">Date</div>
        <!-- Adjust width depending on screen size -->
        <div class="full-width-on-xs text-center" style="width: 30%">Value</div>
        <div class="full-width-on-xs text-end" style="width: 35%">Usage</div>
    </div>
    
    <div class="reading-history-tbody">
        @forelse ($paginatedReadings as $reading)
            @php
                $currentIndex = $allReadings->search(fn($item) => $item->id === $reading->id);
                $previousReading = $allReadings->get($currentIndex + 1);
                $startValue = $previousReading ? $previousReading->reading_value : $meter->initial_reading;
                $consumption = $reading->reading_value - $startValue;
            @endphp
            <div class="d-flex align-items-center py-2 px-3 border-bottom border-secondary-subtle">
                <!-- Hide date on very small screens -->
                <div class="hide-on-xs fs-12" style="width: 35%">
                    {{ $reading->reading_date->format('M d, Y') }}
                </div>
                <!-- Adjust width when date is hidden -->
                <div class="full-width-on-xs text-center fw-medium" style="width: 30%">
                    {{ floor($reading->reading_value) }}
                </div>
                <div class="full-width-on-xs text-end" style="width: 35%">
                    @if ($consumption >= 0)
                        <span class="badge bg-success">{{ floor($consumption) }}</span>
                    @else
                        <span class="badge bg-danger">Error</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center p-3 text-muted">No history yet.</div>
        @endforelse
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{-- Using your custom rounded pagination style --}}
    {{ $paginatedReadings->links('vendor.pagination.custom-pagination') }}
</div>

{{-- Include the enhanced pagination script --}}
@include('backends.dashboard.properties.partials.utility_items._pagination-script')