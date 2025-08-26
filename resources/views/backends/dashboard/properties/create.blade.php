<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Add New Property</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createPropertyForm" method="POST" action="{{ route('landlord.properties.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Property Name</label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Sunnyvale Apartments">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="property_type" class="form-label">Property Type</label>
                            <select class="form-control" id="property_type" name="property_type" required>
                                <option value="" disabled selected>Select a type...</option>
                                <option value="apartment">Apartment</option>
                                <option value="house">House</option>
                                <option value="condo">Condo</option>
                                <option value="townhouse">Townhouse</option>
                                <option value="commercial">Commercial</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter a brief description of the property"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="address_line_1" class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" id="address_line_1" name="address_line_1" required placeholder="e.g. 123 Main St">
                    </div>

                    <div class="mb-3">
                        <label for="address_line_2" class="form-label">Address Line 2 (Optional)</label>
                        <input type="text" class="form-control" id="address_line_2" name="address_line_2" placeholder="e.g. Apt #4B">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required placeholder="e.g. San Francisco">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state_province" class="form-label">State / Province</label>
                            <input type="text" class="form-control" id="state_province" name="state_province" required placeholder="e.g. CA">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" required placeholder="e.g. 94103">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select class="form-control" id="country" name="country" required>
                                <option value="" disabled selected>Select a country...</option>
                                <option value="USA">United States</option>
                                <option value="Canada">Canada</option>
                                <option value="UK">United Kingdom</option>
                                {{-- Add other countries as needed --}}
                            </select>
                        </div>
                    </div>

                     <div class="mb-3">
                        <label for="cover_image" class="form-label">Cover Image (Optional)</label>
                        <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                    </div>

                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <label for="year_built" class="form-label">Year Built</label>
                            <input type="number" class="form-control" id="year_built" name="year_built" min="1800" max="{{ date('Y') }}" placeholder="e.g. 1995">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Property</button>
                </div>
            </form>
        </div>
    </div>
</div>