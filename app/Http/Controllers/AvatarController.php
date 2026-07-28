<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AvatarController extends Controller
{
    public function __invoke(User $user): BinaryFileResponse|Response
    {
        $disk = Storage::disk(config('filament.default_filesystem_disk', 'local'));

        abort_unless($user->image_path && $disk->exists($user->image_path), 404);

        return response()->file($disk->path($user->image_path));
    }
}
