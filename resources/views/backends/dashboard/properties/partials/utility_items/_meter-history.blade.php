<table class="table table-sm table-striped mb-0">
    <thead>
        <tr>
            <th>Date</th>
            <th>Value</th>
            <th>Usage</th>
        </tr>
    </thead>
    <tbody class="reading-history-tbody">
        @forelse ($paginatedReadings as $reading)
            @php
                $currentIndex = $allReadings->search(fn($item) => $item->id === $reading->id);
                $previousReading = $allReadings->get($currentIndex + 1);
                $startValue = $previousReading ? $previousReading->reading_value : $meter->initial_reading;
                $consumption = $reading->reading_value - $startValue;
            @endphp
            <tr>
                <td>{{ $reading->reading_date->format('M d, Y') }}</td>
                <td>{{ number_format($reading->reading_value, 2) }}</td>
                <td>
                    @if ($consumption >= 0)
                        <span class="badge bg-success-subtle text-success">{{ number_format($consumption, 2) }}</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger">Error</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted">No history yet.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3 d-flex justify-content-center">
    {{-- This tells Laravel to use your new custom view --}}
{{ $paginatedReadings->links('vendor.pagination.custom-pagination') }}
</div>