<?php

namespace App\Firebase;

use Kreait\Firebase\Contract\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FirebaseStorage
{
    private $storage;
    private $bucket;

    public function __construct(Storage $storage) 
    {
        $this->storage = $storage;
        $this->bucket = $this->storage->getBucket();
    }

    public function uploadFile(UploadedFile $file, string $storagePath) : string
    {
        $uploadedFileExt = $file->getClientOriginalExtension();
        $uploadedFileName = Str::random() .'.' . $uploadedFileExt;
        $file->move(public_path('tmp'), $uploadedFileName);
        $uploadedFile = fopen(public_path('tmp') . '/' . $uploadedFileName, 'r');
        $this->bucket->upload($uploadedFile, [
            'name' => $storagePath.$uploadedFileName,
        ]);
        unlink(public_path('tmp') . '/' . $uploadedFileName);

        return $uploadedFileName;
    }

    public function updateFile(UploadedFile $file, string $storagePath, string $oldFileName) : string
    {
        $fileName = $this->uploadFile($file, $storagePath);
        $this->deleteFile($oldFileName, $storagePath);

        return $fileName;
    }

    public function deleteFile(string $fileName, string $storagePath)
    {
        $this->bucket->object($storagePath.$fileName)->delete();
    }
}
