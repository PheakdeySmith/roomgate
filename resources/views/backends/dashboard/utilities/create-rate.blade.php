<div class="modal fade" id="createRateModal" tabindex="-1" aria-labelledby="createRateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRateModalLabel">Add New Utility Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('landlord.properties.rates.store', $property) }}" method="POST">
                @csrf
                <div class="modal-body">

                    {{-- This is the corrected field --}}
                    <div class="mb-3">
                        <label for="utility_type_name" class="form-label">Utility Type</label>

                        <input type="hidden" name="utility_type_id" id="create_utility_type_id">

                        <input type="text" class="form-control" id="create_utility_type_name"
                            placeholder="Select from table" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label for="rate" class="form-label">Rate (in USD - will be converted to KHR)</label>
                        <input type="number" step="0.01" class="form-control" name="rate" id="rate"
                            placeholder="e.g., 0.25 for 1,005 KHR" required>
                    </div>

                    <div class="mb-3">
                        <label for="effective_from" class="form-label">Effective From</label>
                        {{-- Use type="text" and add the "flatpickr-date" class --}}
                        <input type="text" class="form-control flatpickr-date" id="effective_from" name="effective_from"
                            placeholder="Select a date..." required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>