<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createUserForm" method="POST"
                action="
                        @if (Auth::check() && Auth::user()->hasRole('admin')) {{ route('admin.users.store') }}
                        @elseif(Auth::check() && Auth::user()->hasRole('landlord')) {{ route('landlord.users.store') }}
                        @else {{ route('landlord.users.store') }}
                        @endif " enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="createName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="createName" name="name" required
                            placeholder="">
                    </div>
                    <div class="mb-3">
                        <label for="createEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="createEmail" name="email" required
                            placeholder="">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="createPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="createPassword" name="password" required
                                placeholder="Must be at least 8 characters">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="createPasswordConfirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="createPasswordConfirmation"
                                name="password_confirmation" required placeholder="Re-enter password">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="createPhone" class="form-label">Phone (Optional)</label>
                        <input type="tel" class="form-control" id="createPhone" name="phone"
                            placeholder="">
                    </div>

                    <div class="mb-3">
                        <label for="createImage" class="form-label">Profile Image (Optional)</label>
                        <input type="file" class="form-control" id="createImage" name="image" accept="image/*">
                        <div id="createImagePreviewContainer" class="mt-2 d-none">
                            <img id="createImagePreview" src="https://placehold.co/150x150/e9ecef/6c757d?text=Preview"
                                alt="Image Preview"
                                style="max-width: 150px; max-height: 150px; border: 1px solid #dee2e6; border-radius: 0.25rem; object-fit: cover;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="createStatus" class="form-label">Status</label>
                            <select class="form-select" id="createStatus" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
