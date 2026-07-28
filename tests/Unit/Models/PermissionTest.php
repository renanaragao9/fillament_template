<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function permission(array $attributes = []): Permission
    {
        return Permission::create(array_merge([
            'name' => 'Ver Usuários',
            'code' => 'user.view',
            'group' => 'Usuários',
        ], $attributes));
    }

    public function test_can_create_permission(): void
    {
        $permission = $this->permission();

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'code' => 'user.view',
        ]);
    }

    public function test_can_read_permission(): void
    {
        $permission = $this->permission();

        $found = Permission::find($permission->id);

        $this->assertNotNull($found);
        $this->assertSame('user.view', $found->code);
        $this->assertSame('Usuários', $found->group);
    }

    public function test_can_update_permission(): void
    {
        $permission = $this->permission();

        $permission->update(['name' => 'Visualizar Usuários']);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'Visualizar Usuários',
        ]);
    }

    public function test_can_delete_permission(): void
    {
        $permission = $this->permission();

        $permission->delete();

        $this->assertSoftDeleted('permissions', ['id' => $permission->id]);
        $this->assertNull(Permission::find($permission->id));
    }

    public function test_permission_is_a_global_catalog_without_company(): void
    {
        $permission = $this->permission();

        $this->assertFalse(
            array_key_exists('company_id', $permission->getAttributes()),
            'Permissão é catálogo global e não deve ter company_id.'
        );
    }

    public function test_permission_belongs_to_many_roles(): void
    {
        $permission = $this->permission();
        $role = Role::create(['name' => 'Admin']);

        $role->permissions()->sync([$permission->id]);

        $this->assertTrue($permission->roles()->whereKey($role->id)->exists());
    }
}
