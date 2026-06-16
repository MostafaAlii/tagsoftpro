<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Models\{Attachment,ProviderDocumentStatus};

trait UploadDocumentTrait {
    public function uploadProviderDocument($file, $identifier, $providerId, $documentId) {
        // Generate unique folder name
        $slug = explode('@', $identifier)[0];
        $slug = substr(preg_replace('/[^A-Za-z0-9_]/', '_', $slug), 0, 10); // sanitize
        $folderName = "{$slug}_{$providerId}";
        $folderPath = public_path("documents/{$folderName}");
        // Create folder if it doesn't exist
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }
        // Prepare file
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $folderPath . '/' . $fileName;
        // Save image or file
        if (str_starts_with($file->getMimeType(), 'image/')) {
            Image::make($file)->save($fullPath);
        } else {
            $file->move($folderPath, $fileName);
        }
        // Relative path to save in DB
        $relativePath = "documents/{$folderName}/{$fileName}";
        // Save in attachments table
        $attachment = Attachment::create([
            'file' => $relativePath,
            'type' => $file->getClientMimeType(),
        ]);
        // Save in provider_document_statuses
        ProviderDocumentStatus::create([
            'provider_id'   => $providerId,
            'document_id'   => $documentId,
            'attachment_id' => $attachment->id,
            'status'        => 'pending',
        ]);
        return $relativePath;
    }

    public function updateProviderDocument($file, ProviderDocumentStatus $status, $identifier) {
        $oldAttachment = $status->attachment;
        if ($oldAttachment && File::exists(public_path($oldAttachment->file))) {
            File::delete(public_path($oldAttachment->file));
        }
        $slug = explode('@', $identifier)[0];
        $slug = substr(preg_replace('/[^A-Za-z0-9_]/', '_', $slug), 0, 10);
        $folderName = "{$slug}_{$status->provider_id}";
        $folderPath = public_path("documents/{$folderName}");
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $folderPath . '/' . $fileName;

        if (str_starts_with($file->getMimeType(), 'image/')) {
            Image::make($file)->save($fullPath);
        } else {
            $file->move($folderPath, $fileName);
        }

        $relativePath = "documents/{$folderName}/{$fileName}";
        $oldAttachment->update([
            'file' => $relativePath,
            'type' => $file->getClientMimeType(),
        ]);
        $status->update([
            'status' => 'pending',
            'rejection_reason' => null,
        ]);

        return $relativePath;
    }
}
