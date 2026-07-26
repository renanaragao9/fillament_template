<?php

namespace Tests\Feature\API;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyControllerCrudTest extends TestCase
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

    public function test_company_controller_crud_flow(): void
    {
        $storeResponse = $this->withHeaders($this->authHeaders())
            ->postJson('/api/v1/companies', [
                'name' => 'Empresa Nova',
                'slug' => 'empresa-nova',
                'status' => 'active',
            ]);

        $storeResponse->assertOk();
        $companyId = $storeResponse->json('data.id');

        $this->assertDatabaseHas('companies', [
            'id' => $companyId,
            'slug' => 'empresa-nova',
        ]);

        $this->withHeaders($this->authHeaders())
            ->getJson('/api/v1/companies')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'empresa-nova']);

        $this->withHeaders($this->authHeaders())
            ->putJson("/api/v1/companies/{$companyId}", [
                'name' => 'Empresa Nova Atualizada',
            ])
            ->assertOk();

        $this->assertDatabaseHas('companies', [
            'id' => $companyId,
            'name' => 'Empresa Nova Atualizada',
        ]);

        $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/v1/companies/{$companyId}")
            ->assertOk();

        $this->assertSoftDeleted('companies', ['id' => $companyId]);
    }
}
