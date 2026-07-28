<?php

namespace App\Models;

use App\Models\Concerns\IsSystemRecord;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends BaseModel
{
    use IsSystemRecord;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'code',
        'group',
        'description',
        'is_super_admin',
    ];

    protected function casts(): array
    {
        return [
            'is_super_admin' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
