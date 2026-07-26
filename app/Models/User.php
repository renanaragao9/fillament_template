<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Traits\HasFileUploads;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'phone', 'image_path', 'status', 'company_id', 'role_id', 'is_super_admin', 'last_login_at', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable implements FilamentUser
{
    use BelongsToTenant, HasApiTokens, HasFactory, HasFileUploads, Notifiable, SoftDeletes;

    protected $table = 'users';

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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected function fileUploadFields(): array
    {
        return ['image_path'];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === 'active';
    }
}
