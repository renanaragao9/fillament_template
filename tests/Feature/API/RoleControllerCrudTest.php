<?php

namespace Tests\Feature\API;

use App\Models\Company;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleControllerCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Empresa Base',
            'slug' => 'empresa-base',
            'status' => 'active',
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super-admin@test.com',
            'password' => '12345678',
            'status' => 'active',
            'is_super_admin' => true,
        ]);
    }

    protected function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->superAdmin->createToken('test')->plainTextToken,
        ];
    }

    public function test_role_controller_crud_flow(): void
    {
        $permissionA = Permission::create([
            'name' => 'Permissao A',
            'code' => 'role.test.a',
            'group' => 'Teste',
        ]);

        $permissionB = Permission::create([
            'name' => 'Permissao B',
            'code' => 'role.test.b',
            'group' => 'Teste',
        ]);

        $storeResponse = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/roles', [
                'name' => 'Perfil Teste',
                'company_id' => $this->company->id,
                'permission_ids' => [$permissionA->id, $permissionB->id],
            ]);

        $storeResponse->assertOk();
        $roleId = $storeResponse->json('data.id');

        $this->assertDatabaseHas('roles', [
            'id' => $roleId,
            'name' => 'Perfil Teste',
        ]);

        $this->assertDatabaseHas('permission_role', [
            'role_id' => $roleId,
            'permission_id' => $permissionA->id,
        ]);

        $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/roles')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Perfil Teste']);

        $this->withHeaders($this->authHeaders())
            ->putJson("/api/v1/roles/{$roleId}", [
                'description' => 'Perfil atualizado',
                'permission_ids' => [$permissionB->id],
            ])
            ->assertOk();

        $this->assertDatabaseHas('roles', [
            'id' => $roleId,
            'description' => 'Perfil atualizado',
        ]);

        $this->assertDatabaseMissing('permission_role', [
            'role_id' => $roleId,
            'permission_id' => $permissionA->id,
        ]);

        $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/v1/roles/{$roleId}")
            ->assertOk();

        $this->assertSoftDeleted('roles', ['id' => $roleId]);
    }
}
