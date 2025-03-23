<?php

namespace App\Services\Tenant;

use App\Mail\TransferDetails;
use App\Models\Account;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class MediaService
{

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function uploadDocumentWithClear(HasMedia $model, UploadedFile $file, string $collection = 'documents'): string
    {
        $model->clearMediaCollection($collection);
        return $model->addMedia($file)
            ->toMediaCollection($collection)
            ->getUrl();
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function uploadDocumentWithOutClear(HasMedia $model, UploadedFile $file, string $collection = 'documents'): string
    {
        return $model->addMedia($file)
            ->toMediaCollection($collection)
            ->getUrl();
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function addDocument(HasMedia $model, UploadedFile $file, string $collection = 'documents'): string
    {
        return $model->addMedia($file)
            ->toMediaCollection($collection)
            ->getUrl();
    }

    public function deleteDocument(HasMedia $model, string $collection = 'documents'): string
    {
        return $model->clearMediaCollection($collection);
    }




}

