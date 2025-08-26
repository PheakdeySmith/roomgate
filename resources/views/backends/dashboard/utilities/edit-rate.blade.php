{{-- This is the complete, corrected code for your edit-rate.blade.php --}}
<div class="modal fade" id="editRateModal" tabindex="-1" aria-labelledby="editRateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRateModalLabel">Edit Utility Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    {{-- 1. Corrected Utility Type field with a unique ID --}}
                    <div class="mb-3">
                        <label class="form-label">Utility Type</label>
                        <input type="text" class="form-control" id="edit_utility_type_name" placeholder="Loading..."
                            readonly disabled>
                    </div>

                    {{-- 2. Added the missing Rate input field --}}
                    <div class="mb-3">
                        <label for="editRate" class="form-label">Rate (in USD - will be converted to KHR)</label>
                        <input type="number" step="0.01" class="form-control" name="rate" id="editRate" required>
                    </div>

                    <div class="mb-3">
                        <label for="editEffectiveFrom" class="form-label">Effective From</label>
                        {{-- Use type="text" and add the "flatpickr-date" class --}}
                        <input type="text" class="form-control flatpickr-date" name="effective_from"
                            id="editEffectiveFrom" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>