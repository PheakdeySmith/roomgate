<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'file_url' => asset('storage/' . $this->file_path),
            'file_size' => $this->file_size,
            'file_size_formatted' => $this->formatFileSize($this->file_size),
            'file_type' => $this->file_type,
            'mime_type' => $this->mime_type,
            'extension' => pathinfo($this->file_path, PATHINFO_EXTENSION),
            'description' => $this->description,
            'category' => $this->category,
            'tags' => $this->tags ?? [],
            'is_public' => $this->is_public ?? false,
            'documentable_type' => $this->documentable_type ? class_basename($this->documentable_type) : null,
            'documentable_id' => $this->documentable_id,
            'entity' => $this->when($this->documentable, function () {
                return [
                    'type' => class_basename($this->documentable_type),
                    'id' => $this->documentable_id,
                    'name' => $this->getEntityName(),
                ];
            }),
            'uploaded_by' => new UserResource($this->whenLoaded('uploadedBy')),
            'shared_with' => UserResource::collection($this->whenLoaded('sharedWith')),
            'expires_at' => $this->expires_at,
            'is_expired' => $this->expires_at && now()->isAfter($this->expires_at),
            'download_count' => $this->download_count ?? 0,
            'last_accessed_at' => $this->last_accessed_at,
            'is_verified' => $this->is_verified ?? false,
            'verified_by' => new UserResource($this->whenLoaded('verifiedBy')),
            'verified_at' => $this->verified_at,
            'thumbnail_url' => $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null,
            'preview_url' => $this->getPreviewUrl(),
            'can_preview' => $this->canPreview(),
            'metadata' => $this->metadata ?? [],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Format file size in human readable format
     *
     * @param int $bytes
     * @return string
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get entity name based on documentable type
     *
     * @return string|null
     */
    private function getEntityName(): ?string
    {
        if (!$this->documentable) {
            return null;
        }

        return match(class_basename($this->documentable_type)) {
            'Contract' => "Contract #{$this->documentable->contract_number}",
            'Property' => $this->documentable->name,
            'Room' => "Room {$this->documentable->room_number}",
            'Invoice' => "Invoice #{$this->documentable->invoice_number}",
            'Payment' => "Payment #{$this->documentable->payment_number}",
            'User' => $this->documentable->name,
            default => null,
        };
    }

    /**
     * Get preview URL if available
     *
     * @return string|null
     */
    private function getPreviewUrl(): ?string
    {
        if (!$this->canPreview()) {
            return null;
        }

        // For PDFs and images, return the direct file URL
        if (in_array($this->file_type, ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])) {
            return asset('storage/' . $this->file_path);
        }

        return null;
    }

    /**
     * Check if document can be previewed
     *
     * @return bool
     */
    private function canPreview(): bool
    {
        $previewableTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/jpg',
            'image/gif',
            'image/webp',
            'text/plain',
        ];

        return in_array($this->file_type, $previewableTypes);
    }
}