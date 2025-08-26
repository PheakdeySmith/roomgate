{{-- This is likely in a file like 'backends.dashboard.users.edit.blade.php' --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Change id to "editUserForm" and action to "" --}}
            <form id="editUserForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="user_id"> {{-- Populated by JS --}}
                    <input type="hidden" name="existing_image_path" id="editExistingImagePath">

                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required placeholder="e.g. Jane Doe">
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required placeholder="e.g. jane.doe@example.com">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="editPassword" name="password" placeholder="Leave blank to keep current">
                            <small class="form-text text-muted">Enter a new password only if you want to change it.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editPasswordConfirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="editPasswordConfirmation" name="password_confirmation" placeholder="Re-enter new password">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="editPhone" name="phone" placeholder="e.g. (555) 123-4567">
                    </div>

                    <div class="mb-3">
                        <label for="editImage" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                        <div id="editImagePreviewContainer" class="mt-2">
                            <img id="editImagePreview" src="https://placehold.co/150x150/e9ecef/6c757d?text=Current" alt="Current Image Preview" style="max-width: 150px; max-height: 150px; border: 1px solid #dee2e6; border-radius: 0.25rem; object-fit: cover; display: none;">
                        </div>
                        <div class="mt-2 form-check">
                            <input type="checkbox" class="form-check-input" id="removeCurrentImage" name="remove_image" value="1">
                            <label class="form-check-label" for="removeCurrentImage">Remove current image (if new one isn't uploaded)</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
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