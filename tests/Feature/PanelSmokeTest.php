<?php

namespace Tests\Feature;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $tenantAdmin;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $company = Company::create(['name' => 'Empresa A', 'slug' => 'empresa-a', 'status' => 'active']);

        $permissions = collect(['user', 'role', 'permission', 'company'])
            ->flatMap(fn ($resource) => collect(['view', 'create', 'update', 'delete'])
                ->map(fn ($action) => Permission::create([
                    'name' => "{$resource}.{$action}",
                    'code' => "{$resource}.{$action}",
                    'group' => ucfirst($resource),
                ])));

        $role = Role::create(['name' => 'Admin', 'company_id' => $company->id]);
        $role->permissions()->sync($permissions->pluck('id'));

        $this->tenantAdmin = User::create([
            'name' => 'Admin Tenant',
            'email' => 'tenant@test.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $company->id,
            'role_id' => $role->id,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => '12345678',
            'status' => 'active',
            'is_super_admin' => true,
        ]);
    }

    public static function panelPages(): array
    {
        return [
            'usuários' => ['/admin/users'],
            'criar usuário' => ['/admin/users/create'],
            'perfis' => ['/admin/roles'],
            'criar perfil' => ['/admin/roles/create'],
            'permissões' => ['/admin/permissions'],
            'empresas' => ['/admin/companies'],
        ];
    }

    #[DataProvider('panelPages')]
    public function test_tenant_admin_can_render_panel_page(string $url): void
    {
        $this->actingAs($this->tenantAdmin)->get($url)->assertOk();
    }

    #[DataProvider('panelPages')]
    public function test_super_admin_can_render_panel_page(string $url): void
    {
        $this->actingAs($this->superAdmin)->get($url)->assertOk();
    }

    public function test_tenant_admin_creates_role_stamped_with_own_company(): void
    {
        $this->actingAs($this->tenantAdmin);

        Livewire::test(CreateRole::class)
            ->fillForm(['name' => 'Financeiro'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('roles', [
            'name' => 'Financeiro',
            'company_id' => $this->tenantAdmin->company_id,
        ]);

        Livewire::test(CreateRole::class)
            ->fillForm(['name' => 'Financeiro'])
            ->call('create')
            ->assertHasFormErrors(['name']);
    }

    public function test_super_admin_creates_user_for_a_chosen_company(): void
    {
        $company = Company::create(['name' => 'Empresa B', 'slug' => 'empresa-b', 'status' => 'active']);
        $role = Role::create(['name' => 'Gestor', 'company_id' => $company->id]);

        $this->actingAs($this->superAdmin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Usuário da B',
                'email' => 'user.b@test.com',
                'password' => 'password123',
                'status' => 'active',
                'company_id' => $company->id,
                'role_id' => $role->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'user.b@test.com',
            'company_id' => $company->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_super_admin_must_choose_a_company_when_creating_user(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Sem Empresa',
                'email' => 'sem.empresa@test.com',
                'password' => 'password123',
            ])
            ->call('create')
            ->assertHasFormErrors(['company_id']);

        $this->assertDatabaseMissing('users', ['email' => 'sem.empresa@test.com']);
    }

    public function test_super_admin_can_render_permission_create_page(): void
    {
        $this->actingAs($this->superAdmin)->get('/admin/permissions/create')->assertOk();
    }

    public function test_tenant_admin_cannot_render_permission_create_page(): void
    {
        $this->actingAs($this->tenantAdmin)->get('/admin/permissions/create')->assertForbidden();
    }
}
