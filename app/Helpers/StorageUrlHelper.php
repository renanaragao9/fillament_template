<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('storage_url')) {
    function storage_url(?string $path, string $disk = 'public'): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk($disk)->url($path);
    }
}
