<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Storage;

trait HasFileUploads
{
    public static function bootHasFileUploads(): void
    {
        static::updating(function (self $model): void {
            foreach ($model->fileUploadFields() as $field) {
                if ($model->isDirty($field) && $model->getOriginal($field)) {
                    Storage::disk($model->fileUploadDisk())->delete($model->getOriginal($field));
                }
            }
        });

        static::deleted(function (self $model): void {
            foreach ($model->fileUploadFields() as $field) {
                if ($model->{$field}) {
                    Storage::disk($model->fileUploadDisk())->delete($model->{$field});
                }
            }
        });
    }

    protected function fileUploadDisk(): string
    {
        return 'public';
    }

    abstract protected function fileUploadFields(): array;
}
