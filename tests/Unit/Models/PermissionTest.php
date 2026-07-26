<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
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

    public function test_can_create_permission(): void
    {
        $permission = Permission::create([
            'name' => 'Ver Usuários',
            'code' => 'user.view',
            'group' => 'Usuários',
            'company_id' => $this->company->id,
        ]);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'code' => 'user.view',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_can_read_permission(): void
    {
        $permission = Permission::create([
            'name' => 'Ver Usuários',
            'code' => 'user.view',
            'group' => 'Usuários',
            'company_id' => $this->company->id,
        ]);

        $found = Permission::find($permission->id);

        $this->assertNotNull($found);
        $this->assertSame('user.view', $found->code);
        $this->assertSame('Usuários', $found->group);
        $this->assertSame($this->company->id, $found->company_id);
    }

    public function test_can_update_permission(): void
    {
        $permission = Permission::create([
            'name' => 'Ver Usuários',
            'code' => 'user.view',
            'group' => 'Usuários',
            'company_id' => $this->company->id,
        ]);

        $permission->update(['name' => 'Visualizar Usuários']);

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'Visualizar Usuários',
        ]);
    }

    public function test_can_delete_permission(): void
    {
        $permission = Permission::create([
            'name' => 'Ver Usuários',
            'code' => 'user.view',
            'group' => 'Usuários',
            'company_id' => $this->company->id,
        ]);

        $permission->delete();

        $this->assertSoftDeleted('permissions', [
            'id' => $permission->id,
        ]);
        $this->assertNull(Permission::find($permission->id));
    }

    public function test_permission_belongs_to_company(): void
    {
        $permission = Permission::create([
            'name' => 'Ver Usuários',
            'code' => 'user.view',
            'group' => 'Usuários',
            'company_id' => $this->company->id,
        ]);

        $this->assertTrue($permission->company->is($this->company));
    }

    public function test_permission_can_be_global_without_company(): void
    {
        $permission = Permission::create([
            'name' => 'Ver Usuários',
            'code' => 'user.view',
            'group' => 'Usuários',
        ]);

        $this->assertNull($permission->company_id);
        $this->assertNull($permission->company);
    }
}
