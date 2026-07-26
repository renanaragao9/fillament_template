<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
        ]);
    }

    public function test_can_create_role(): void
    {
        $role = Role::create([
            'name' => 'Admin',
            'description' => 'Acesso total ao sistema',
            'company_id' => $this->company->id,
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Admin',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_read_role(): void
    {
        $role = Role::create([
            'name' => 'Admin',
            'description' => 'Acesso total ao sistema',
            'company_id' => $this->company->id,
        ]);

        $found = Role::find($role->id);

        $this->assertNotNull($found);
        $this->assertSame('Admin', $found->name);
        $this->assertSame($this->company->id, $found->company_id);
    }

    public function test_can_update_role(): void
    {
        $role = Role::create([
            'name' => 'Admin',
            'description' => 'Acesso total ao sistema',
            'company_id' => $this->company->id,
        ]);

        $role->update(['description' => 'Acesso administrativo completo']);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'description' => 'Acesso administrativo completo',
        ]);
    }

    public function test_can_delete_role(): void
    {
        $role = Role::create([
            'name' => 'Admin',
            'description' => 'Acesso total ao sistema',
            'company_id' => $this->company->id,
        ]);

        $role->delete();

        $this->assertSoftDeleted('roles', [
            'id' => $role->id,
        ]);
        $this->assertNull(Role::find($role->id));
    }

    public function test_can_sync_permissions_to_role(): void
    {
        $role = Role::create([
            'name' => 'Admin',
            'company_id' => $this->company->id,
        ]);

        $permission = Permission::create([
            'name' => 'Ver Usuários',
            'code' => 'user.view',
            'group' => 'Usuários',
            'company_id' => $this->company->id,
        ]);

        $role->permissions()->sync([$permission->id]);

        $this->assertTrue($role->permissions()->where('permissions.id', $permission->id)->exists());
    }

    public function test_role_belongs_to_company(): void
    {
        $role = Role::create([
            'name' => 'Admin',
            'company_id' => $this->company->id,
        ]);

        $this->assertTrue($role->company->is($this->company));
    }

    public function test_role_name_is_unique_per_company(): void
    {
        Role::create(['name' => 'Admin', 'company_id' => $this->company->id]);

        $otherCompany = Company::create([
            'name' => 'Outra Empresa',
            'slug' => 'outra-empresa',
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'Admin', 'company_id' => $otherCompany->id]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Admin',
            'company_id' => $otherCompany->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Role::create(['name' => 'Admin', 'company_id' => $this->company->id]);
    }
}
