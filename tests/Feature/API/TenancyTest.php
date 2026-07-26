<?php

namespace Tests\Feature\API;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenancyTest extends TestCase
{
    use RefreshDatabase;

    protected Company $companyA;

    protected Company $companyB;

    protected User $userA;

    protected User $userB;

    protected User $superAdmin;

    protected User $noPermissionUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyA = Company::create(['name' => 'Empresa A', 'slug' => 'empresa-a', 'status' => 'active']);
        $this->companyB = Company::create(['name' => 'Empresa B', 'slug' => 'empresa-b', 'status' => 'active']);

        $permissions = collect(['user.view', 'user.create', 'user.update', 'user.delete', 'role.view', 'company.view'])
            ->map(fn ($code) => Permission::create([
                'name' => $code,
                'code' => $code,
                'group' => 'Teste',
            ]));

        $roleA = Role::create(['name' => 'Admin', 'company_id' => $this->companyA->id]);
        $roleA->permissions()->sync($permissions->pluck('id'));

        $roleB = Role::create(['name' => 'Admin', 'company_id' => $this->companyB->id]);
        $roleB->permissions()->sync($permissions->pluck('id'));

        $noPermRole = Role::create(['name' => 'Sem Permissao', 'company_id' => $this->companyA->id]);

        $this->userA = User::create([
            'name' => 'Admin A',
            'email' => 'admin-a@test.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->companyA->id,
            'role_id' => $roleA->id,
        ]);

        $this->userB = User::create([
            'name' => 'Admin B',
            'email' => 'admin-b@test.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->companyB->id,
            'role_id' => $roleB->id,
        ]);

        $this->noPermissionUser = User::create([
            'name' => 'Sem Permissao',
            'email' => 'sempermissao@test.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->companyA->id,
            'role_id' => $noPermRole->id,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => '12345678',
            'status' => 'active',
            'is_super_admin' => true,
        ]);
    }

    protected function tokenFor(User $user): string
    {
        return $user->createToken('test')->plainTextToken;
    }

    protected function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer '.$this->tokenFor($user)];
    }

    protected function getAs(User $user, string $uri)
    {
        return $this->withHeaders($this->authHeaders($user))->getJson($uri);
    }

    protected function postAs(User $user, string $uri, array $payload = [])
    {
        return $this->withHeaders($this->authHeaders($user))->postJson($uri, $payload);
    }

    public function test_regular_user_only_lists_own_company_users(): void
    {
        $response = $this->getAs($this->userA, '/api/v1/users');

        $response->assertOk();
        $emails = collect($response->json('data'))->pluck('email');

        $this->assertContains('admin-a@test.com', $emails);
        $this->assertNotContains('admin-b@test.com', $emails);
    }

    public function test_regular_user_only_lists_own_company(): void
    {
        $response = $this->getAs($this->userA, '/api/v1/companies');

        $response->assertOk();
        $slugs = collect($response->json('data'))->pluck('slug');

        $this->assertContains('empresa-a', $slugs);
        $this->assertNotContains('empresa-b', $slugs);
    }

    public function test_regular_user_gets_404_viewing_other_tenant_user(): void
    {
        $response = $this->getAs($this->userA, "/api/v1/users/{$this->userB->id}");

        $response->assertStatus(404);
    }

    public function test_regular_user_gets_404_viewing_other_tenant_company(): void
    {
        $response = $this->getAs($this->userA, "/api/v1/companies/{$this->companyB->id}");

        $response->assertStatus(404);
    }

    public function test_user_without_permission_gets_403_listing_users(): void
    {
        $response = $this->getAs($this->noPermissionUser, '/api/v1/users');

        $response->assertStatus(403);
    }

    public function test_user_without_permission_gets_403_viewing_own_company(): void
    {
        $response = $this->getAs($this->noPermissionUser, "/api/v1/companies/{$this->companyA->id}");

        $response->assertStatus(403);
    }

    public function test_regular_user_cannot_escalate_company_id_when_creating_user(): void
    {
        $response = $this->postAs($this->userA, '/api/v1/users', [
            'name' => 'Novo Usuário',
            'email' => 'novo@test.com',
            'password' => 'password123',
            'company_id' => $this->companyB->id,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'email' => 'novo@test.com',
            'company_id' => $this->companyA->id,
        ]);
    }

    public function test_regular_user_cannot_create_company_without_super_admin(): void
    {
        $response = $this->postAs($this->userA, '/api/v1/companies', [
            'name' => 'Nova Empresa',
            'slug' => 'nova-empresa',
        ]);

        $response->assertStatus(403);
    }

    public function test_super_admin_can_create_company(): void
    {
        $response = $this->postAs($this->superAdmin, '/api/v1/companies', [
            'name' => 'Nova Empresa',
            'slug' => 'nova-empresa',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('companies', ['slug' => 'nova-empresa']);
    }

    public function test_super_admin_sees_all_companies(): void
    {
        $response = $this->getAs($this->superAdmin, '/api/v1/companies');

        $response->assertOk();
        $slugs = collect($response->json('data'))->pluck('slug');

        $this->assertContains('empresa-a', $slugs);
        $this->assertContains('empresa-b', $slugs);
    }

    public function test_super_admin_can_view_any_tenant_user(): void
    {
        $response = $this->getAs($this->superAdmin, "/api/v1/users/{$this->userB->id}");

        $response->assertOk();
        $response->assertJsonPath('data.email', 'admin-b@test.com');
    }

    public function test_guest_cannot_access_any_tenant_endpoint(): void
    {
        $this->getJson('/api/v1/users')->assertStatus(401);
        $this->getJson('/api/v1/companies')->assertStatus(401);
        $this->getJson('/api/v1/roles')->assertStatus(401);
        $this->getJson('/api/v1/permissions')->assertStatus(401);
    }
}
