<?php

namespace Tests\Feature\API;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionControllerCrudTest extends TestCase
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

    public function test_permission_controller_crud_flow(): void
    {
        $storeResponse = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/permissions', [
                'name' => 'Permissao API',
                'code' => 'permission.api.test',
                'group' => 'API',
                'company_id' => $this->company->id,
            ]);

        $storeResponse->assertOk();
        $permissionId = $storeResponse->json('data.id');

        $this->assertDatabaseHas('permissions', [
            'id' => $permissionId,
            'code' => 'permission.api.test',
        ]);

        $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/permissions')
            ->assertOk()
            ->assertJsonFragment(['code' => 'permission.api.test']);

        $this->withHeaders($this->authHeaders())
            ->putJson("/api/v1/permissions/{$permissionId}", [
                'group' => 'API Atualizado',
            ])
            ->assertOk();

        $this->assertDatabaseHas('permissions', [
            'id' => $permissionId,
            'group' => 'API Atualizado',
        ]);

        $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/v1/permissions/{$permissionId}")
            ->assertOk();

        $this->assertSoftDeleted('permissions', ['id' => $permissionId]);
    }
}
