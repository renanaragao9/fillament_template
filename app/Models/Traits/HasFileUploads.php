<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Model
 *
 * @method static void updating(callable $callback)
 * @method static void deleted(callable $callback)
 * @method bool isDirty(array|string|null $attributes = null)
 * @method mixed getOriginal(?string $key = null, mixed $default = null)
 */
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
        return (string) config('filament.default_filesystem_disk', 'local');
    }

    abstract protected function fileUploadFields(): array;
}
