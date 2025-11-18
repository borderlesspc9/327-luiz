<?php

namespace App\Service;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Handle the file upload and create a File record.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return \App\Models\File
     */
    public function upload(UploadedFile $file, string $directory, $fileName = null): File
    {
        if(!Storage::exists($directory)){
            $this->makeDirectory($directory);
        }
        
        if($fileName == null){
            $fileName = $file->getClientOriginalName();
        }

        $fileExtension = $file->getClientOriginalExtension();
        $fileType = $this->mapFileExtensionToType($fileExtension);
        
        $filePath = $file->store($directory, 'public');
        $fileUrl = Storage::url($filePath);

        $fileRecord = File::create([
            'name' => $fileName,
            'type' => $fileType,
            'url' => $fileUrl,
        ]);

        return $fileRecord;
    }

    public function delete(int $fileId): bool
    {
        $fileRecord = File::find($fileId);

        if (!$fileRecord) {
            return false;
        }

        $filePath = str_replace(Storage::url(''), '', $fileRecord->url);

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        return $fileRecord->delete();
    }

    private function mapFileExtensionToType(string $extension): string
    {
        $typeMap = [
            'pdf' => 'pdf',
            'doc' => 'word',
            'docx' => 'word',
            'xls' => 'excel',
            'xlsx' => 'excel',
            'jpg' => 'image',
            'jpeg' => 'image',
            'png' => 'image',
            'gif' => 'image',
            'webp' => 'image',
        ];

        return $typeMap[$extension] ?? 'Unknown';
    }

    private function makeDirectory(string $directory): void
    {
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
    }
}
