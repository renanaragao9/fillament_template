<?php

namespace Tests\Feature\API;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerCrudTest extends TestCase
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

    public function test_user_controller_crud_flow(): void
    {
        $role = Role::create([
            'name' => 'Gestor',
            'company_id' => $this->company->id,
        ]);

        $storeResponse = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/users', [
                'name' => 'Novo Usuario',
                'email' => 'novo-usuario@test.com',
                'password' => 'password123',
                'status' => 'active',
                'company_id' => $this->company->id,
                'role_id' => $role->id,
            ]);

        $storeResponse->assertOk();
        $userId = $storeResponse->json('data.id');

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'email' => 'novo-usuario@test.com',
        ]);

        $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/users')
            ->assertOk()
            ->assertJsonFragment(['email' => 'novo-usuario@test.com']);

        $this->withHeaders($this->authHeaders())
            ->putJson("/api/v1/users/{$userId}", [
                'name' => 'Usuario Atualizado',
            ])
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'name' => 'Usuario Atualizado',
        ]);

        $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/v1/users/{$userId}")
            ->assertOk();

        $this->assertSoftDeleted('users', ['id' => $userId]);
    }
}
