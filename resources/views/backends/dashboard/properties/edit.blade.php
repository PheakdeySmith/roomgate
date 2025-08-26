<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Property</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Updated form ID to match JS, action is set dynamically --}}
            <form id="editPropertyForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <input type="hidden" id="editPropertyId" name="property_id">
                    <input type="hidden" name="existing_image_path" id="editExistingImagePath">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editName" class="form-label">Property Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_property_type" class="form-label">Property Type</label>
                            <select class="form-control" id="edit_property_type" name="property_type" required>
                                <option value="" disabled>Select a type...</option>
                                <option value="apartment">Apartment</option>
                                <option value="house">House</option>
                                <option value="condo">Condo</option>
                                <option value="townhouse">Townhouse</option>
                                <option value="commercial">Commercial</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editAddressLine1" class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" id="editAddressLine1" name="address_line_1" required>
                    </div>

                    <div class="mb-3">
                        <label for="editAddressLine2" class="form-label">Address Line 2 (Optional)</label>
                        <input type="text" class="form-control" id="editAddressLine2" name="address_line_2">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editCity" class="form-label">City</label>
                            <input type="text" class="form-control" id="editCity" name="city" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editStateProvince" class="form-label">State / Province</label>
                            <input type="text" class="form-control" id="editStateProvince" name="state_province" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editPostalCode" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="editPostalCode" name="postal_code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_country" class="form-label">Country</label>
                            <select class="form-control" id="edit_country" name="country" required>
                                 <option value="" disabled>Select a country...</option>
                                <option value="USA">United States</option>
                                <option value="Canada">Canada</option>
                                <option value="UK">United Kingdom</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editImage" class="form-label">Cover Image</label>
                        <input type="file" class="form-control" id="editImage" name="cover_image" accept="image/*">
                         <div id="editImagePreviewContainer" class="mt-2">
                            <img id="editImagePreview" src="" alt="Current Image Preview" style="max-width: 150px; max-height: 150px; border-radius: 0.25rem; object-fit: cover; display: none;">
                        </div>
                        <div class="mt-2 form-check">
                            <input type="checkbox" class="form-check-input" id="removeCurrentImage" name="remove_cover_image" value="1">
                            <label class="form-check-label" for="removeCurrentImage">Remove current image</label>
                        </div>
                    </div>

                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <label for="editYearBuilt" class="form-label">Year Built</label>
                            <input type="number" class="form-control" id="editYearBuilt" name="year_built" min="1800" max="{{ date('Y') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>