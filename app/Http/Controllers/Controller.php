<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function uploadFile(string $folderName, UploadedFile $file, string $titleSlug): string
    {
        if (empty($folderName) || empty($titleSlug)) {
            throw new \Exception('Folder name or title slug is empty');
        }

        $slug = Str::slug($titleSlug);

        if (empty($slug)) {
            throw new \Exception('Title slug is invalid after slug conversion');
        }

        $folderPath = $folderName . '/' . $slug;
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Simpan file ke storage/app/public/...
        $path = $file->storeAs($folderPath, $fileName, 'public');

        return $path; // path relatif dari storage/app/public
    }

    public function updateFile(string $folderName, ?UploadedFile $newFile, ?string $oldFilePath, string $titleSlug): ?string
    {
        if (!$newFile) {
            return $oldFilePath;
        }

        $newFilePath = $this->uploadFile($folderName, $newFile, $titleSlug);

        // Hapus file lama jika ada
        if ($newFilePath && $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        return $newFilePath;
    }

    public function deleteFile(?string $filePath): void
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}