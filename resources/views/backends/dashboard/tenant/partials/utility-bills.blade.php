<!-- This is a partial view that contains only the utility bill cards for infinite scrolling -->
<div class="utility-bills-container" data-has-more="{{ $utilityBills->hasMorePages() ? 'true' : 'false' }}">
@foreach ($utilityBills as $bill)
    @php
        $utilityClass = '';
        $utilityIcon = 'gauge';
        
        switch(strtolower($bill->utilityType->name)) {
            case 'electricity':
                $utilityClass = 'electricity-icon';
                $utilityIcon = 'bolt';
                break;
            case 'water':
                $utilityClass = 'water-icon';
                $utilityIcon = 'droplet';
                break;
            case 'gas':
                $utilityClass = 'gas-icon';
                $utilityIcon = 'flame';
                break;
            case 'internet':
                $utilityClass = 'internet-icon';
                $utilityIcon = 'wifi';
                break;
        }
    @endphp
    
    <div class="utility-bill-card animate-fade-in">
        <div class="d-flex">
            <div class="utility-icon {{ $utilityClass }} me-3">
                <i class="ti ti-{{ $utilityIcon }}"></i>
            </div>
            <div class="utility-details">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h4 class="mb-0">{{ $bill->utilityType->name }}</h4>
                    @php
                        $lineItem = $bill->lineItem;
                        $status = $lineItem ? $lineItem->status : 'pending';
                        $statusClass = match($status) {
                            'paid' => 'utility-status-paid',
                            'partial' => 'utility-status-partial',
                            default => 'utility-status-pending'
                        };
                        $statusText = match($status) {
                            'paid' => 'Paid',
                            'partial' => 'Partial',
                            'draft' => 'Draft',
                            'sent' => 'Sent',
                            'overdue' => 'Overdue',
                            'void' => 'Void',
                            default => 'Pending'
                        };
                    @endphp
                    <div class="utility-status {{ $statusClass }}">
                        {{ $statusText }}
                    </div>
                </div>
                <div class="text-muted small mb-2">
                    {{ Carbon\Carbon::parse($bill->billing_period_start)->format('M d') }} - 
                    {{ Carbon\Carbon::parse($bill->billing_period_end)->format('M d, Y') }}
                </div>
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <div class="text-muted small">Usage</div>
                        <div class="fw-medium">{{ number_format($bill->consumption, 2) }} {{ $bill->utilityType->unit_of_measure }}</div>
                    </div>
                    <div>
                        <div class="text-muted small">Property Rate</div>
                        <div class="fw-medium">${{ number_format($bill->rate_applied, 4) }}/{{ $bill->utilityType->unit_of_measure }}</div>
                    </div>
                    <div>
                        <div class="text-muted small">Amount</div>
                        <div class="utility-amount">${{ number_format($bill->amount, 2) }}</div>
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    <i class="ti ti-info-circle me-1"></i> Rate applies to {{ $bill->contract->room->property->name }}
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>
