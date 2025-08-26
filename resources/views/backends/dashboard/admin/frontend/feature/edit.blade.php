<div class="modal fade" id="editFeatureModal" tabindex="-1" aria-labelledby="editFeatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFeatureModalLabel">Edit Feature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFeatureForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="editImagePath" class="form-label">New Image (Optional)</label>
                        <input type="file" class="form-control @error('image_path') is-invalid @enderror" id="editImagePath" name="image_path" accept="image/*">
                        <small class="form-text text-muted">Only upload a new image if you want to replace the existing one.</small>
                        @error('image_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div>
                            <img id="current-image-preview" src="" alt="Current Feature Image" style="max-width: 100px; max-height: 100px; display: none; object-fit: cover;">
                            <span id="no-current-image" style="display: none;">No image uploaded.</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="editTitle" name="title" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="editDescription" name="description" rows="3" required></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="editLink" class="form-label">Link</label>
                        <input type="url" class="form-control @error('link') is-invalid @enderror" id="editLink" name="link" placeholder="https://example.com">
                         @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Dynamic Bullets Section --}}
                    <div class="mb-3">
                        <label class="form-label">Bullet Points</label>
                        <div id="edit-bullets-container">
                            {{-- JS will populate existing bullets here --}}
                        </div>
                        <button class="btn btn-soft-primary btn-sm mt-2 add-bullet" type="button">Add Bullet</button>
                    </div>

                    <div class="mb-3">
                        <label for="editOrder" class="form-label">Order</label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" id="editOrder" name="order">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_highlighted" id="edit_is_highlighted" value="1">
                        <label class="form-check-label" for="edit_is_highlighted">
                            Set as Highlighted Feature (This will un-highlight any other feature)
                        </label>
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