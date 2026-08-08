<?php

namespace App\Services;

use App\Models\PhotoFingerprint;
use Illuminate\Http\UploadedFile;

class PhotoComparisonService
{
    public function sha256(UploadedFile $photo): string
    {
        return hash_file('sha256', $photo->getRealPath());
    }

    public function findExactDuplicate(string $sha256): ?PhotoFingerprint
    {
        return PhotoFingerprint::query()
            ->where('photo_hash', $sha256)
            ->first();
    }

    public function register(
        int $userId,
        string $sha256,
        string $sourceType,
        int $sourceId
    ): PhotoFingerprint {
        return PhotoFingerprint::create([
            'user_id' => $userId,
            'photo_hash' => $sha256,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);
    }
}
