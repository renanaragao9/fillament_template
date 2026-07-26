<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TenantScopeTest extends TestCase
{
    use RefreshDatabase;

    protected Company $companyA;

    protected Company $companyB;

    protected User $userA;

    protected User $userB;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyA = Company::create(['name' => 'Empresa A', 'slug' => 'empresa-a', 'status' => 'active']);
        $this->companyB = Company::create(['name' => 'Empresa B', 'slug' => 'empresa-b', 'status' => 'active']);

        $roleA = Role::create(['name' => 'Admin', 'company_id' => $this->companyA->id]);
        $roleB = Role::create(['name' => 'Admin', 'company_id' => $this->companyB->id]);

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

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => '12345678',
            'status' => 'active',
            'is_super_admin' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Auth::logout();

        parent::tearDown();
    }

    public function test_unauthenticated_context_is_unscoped(): void
    {
        $this->assertSame(2, Company::count());
        $this->assertSame(3, User::count());
        $this->assertSame(2, Role::count());
    }

    public function test_regular_user_only_sees_own_company_users(): void
    {
        Auth::login($this->userA);

        $users = User::all();

        $this->assertCount(1, $users);
        $this->assertTrue($users->first()->is($this->userA));
    }

    public function test_regular_user_only_sees_own_company_roles(): void
    {
        Auth::login($this->userA);

        $this->assertSame(1, Role::count());
    }

    public function test_regular_user_only_sees_own_company(): void
    {
        Auth::login($this->userA);

        $companies = Company::all();

        $this->assertCount(1, $companies);
        $this->assertTrue($companies->first()->is($this->companyA));
    }

    public function test_regular_user_cannot_find_other_company_user_by_id(): void
    {
        Auth::login($this->userA);

        $this->assertNull(User::find($this->userB->id));
    }

    public function test_regular_user_cannot_find_other_company_by_id(): void
    {
        Auth::login($this->userA);

        $this->assertNull(Company::find($this->companyB->id));
    }

    public function test_regular_user_sees_global_permissions_but_not_other_company_custom_permission(): void
    {
        Permission::create(['name' => 'Global', 'code' => 'global.view', 'group' => 'Global']);
        Permission::create(['name' => 'Custom B', 'code' => 'custom.b', 'group' => 'Custom', 'company_id' => $this->companyB->id]);
        Permission::create(['name' => 'Custom A', 'code' => 'custom.a', 'group' => 'Custom', 'company_id' => $this->companyA->id]);

        Auth::login($this->userA);

        $codes = Permission::pluck('code')->all();

        $this->assertContains('global.view', $codes);
        $this->assertContains('custom.a', $codes);
        $this->assertNotContains('custom.b', $codes);
    }

    public function test_is_super_admin_flagged_records_are_hidden_from_regular_users(): void
    {
        Role::create(['name' => 'Sistema', 'is_super_admin' => true]);

        Auth::login($this->userA);

        $this->assertSame(0, Role::where('name', 'Sistema')->count());
    }

    public function test_super_admin_sees_everything(): void
    {
        Auth::login($this->superAdmin);

        $this->assertSame(2, Company::count());
        $this->assertSame(3, User::count());
        $this->assertSame(2, Role::count());
        $this->assertNotNull(User::find($this->userB->id));
        $this->assertNotNull(Company::find($this->companyB->id));
    }

    public function test_regular_user_creating_record_forces_own_company_id(): void
    {
        Auth::login($this->userA);

        $role = Role::create(['name' => 'Gerente', 'company_id' => $this->companyB->id]);

        $this->assertSame($this->companyA->id, $role->company_id);
    }

    public function test_regular_user_creating_record_cannot_set_is_super_admin(): void
    {
        Auth::login($this->userA);

        $role = Role::create(['name' => 'Gerente 2', 'is_super_admin' => true]);

        $this->assertFalse($role->is_super_admin);
    }

    public function test_super_admin_can_create_record_for_any_company(): void
    {
        Auth::login($this->superAdmin);

        $role = Role::create(['name' => 'Gerente B', 'company_id' => $this->companyB->id]);

        $this->assertSame($this->companyB->id, $role->company_id);
    }
}
