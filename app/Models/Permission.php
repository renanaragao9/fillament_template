<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends BaseModel
{
    use BelongsToTenant;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'code',
        'group',
        'is_super_admin',
        'company_id',
    ];

    protected function casts(): array
    {
        return [
            'is_super_admin' => 'boolean',
        ];
    }

    public function tenantScopeIncludesGlobalRecords(): bool
    {
        return true;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
