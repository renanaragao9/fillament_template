<?php

namespace App\Support;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class TenantContext
{
    protected bool $explicit = false;

    protected ?int $companyId = null;

    protected bool $superAdmin = false;

    public static function current(): static
    {
        return app(static::class);
    }

    public function set(?int $companyId, bool $superAdmin = false): static
    {
        $this->explicit = true;
        $this->companyId = $companyId;
        $this->superAdmin = $superAdmin;

        return $this;
    }

    public function forget(): static
    {
        $this->explicit = false;
        $this->companyId = null;
        $this->superAdmin = false;

        return $this;
    }

    public function companyId(): ?int
    {
        if ($this->explicit) {
            return $this->companyId;
        }

        return $this->user()?->company_id;
    }

    public function isSuperAdmin(): bool
    {
        if ($this->explicit) {
            return $this->superAdmin;
        }

        return (bool) $this->user()?->is_super_admin;
    }

    public function hasContext(): bool
    {
        return $this->explicit
            ? ($this->companyId !== null || $this->superAdmin)
            : $this->user() !== null;
    }

    public function shouldScope(): bool
    {
        return $this->hasContext() && ! $this->isSuperAdmin();
    }

    public function runFor(?int $companyId, Closure $callback, bool $superAdmin = false): mixed
    {
        $previous = [$this->explicit, $this->companyId, $this->superAdmin];

        $this->set($companyId, $superAdmin);

        try {
            return $callback();
        } finally {
            [$this->explicit, $this->companyId, $this->superAdmin] = $previous;
        }
    }

    public function runAsSuperAdmin(Closure $callback): mixed
    {
        return $this->runFor(null, $callback, superAdmin: true);
    }

    protected function user(): ?User
    {
        if (! Auth::hasUser()) {
            return null;
        }

        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }
}
