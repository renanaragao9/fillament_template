<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\IsSystemRecord;
use App\Models\Traits\HasFileUploads;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use BelongsToTenant, HasApiTokens, HasFactory, HasFileUploads, IsSystemRecord, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image_path',
        'status',
        'company_id',
        'role_id',
        'is_super_admin',
        'last_login_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    protected function fileUploadFields(): array
    {
        return ['image_path'];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function tenantIsActive(): bool
    {
        if ($this->is_super_admin || ! $this->company_id) {
            return true;
        }

        return (bool) $this->resolveCompany()?->isActive();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isActive() && $this->tenantIsActive();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (! Storage::disk($this->fileUploadDisk())->exists($this->image_path)) {
            return null;
        }

        return route('avatars.serve', ['user' => $this->getKey()]);
    }

    protected function resolveCompany(): ?Company
    {
        if ($this->relationLoaded('company')) {
            return $this->company;
        }

        return Company::withoutGlobalScopes()->find($this->company_id);
    }
}
