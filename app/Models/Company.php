<?php

namespace App\Models;

use App\Models\Scopes\CurrentCompanyScope;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends BaseModel
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'logo_path',
        'email',
        'phone',
        'document',
        'status',
        'settings',
        'trial_ends_at',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CurrentCompanyScope);
    }

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
