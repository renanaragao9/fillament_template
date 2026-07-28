<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class TenancyHardeningTest extends TestCase
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

        Schema::create('tenant_probes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('company_id')->nullable();
        });

        $this->companyA = Company::create(['name' => 'Empresa A', 'slug' => 'empresa-a', 'status' => 'active']);
        $this->companyB = Company::create(['name' => 'Empresa B', 'slug' => 'empresa-b', 'status' => 'active']);

        $permissions = collect([
            'user.view', 'user.create', 'user.update', 'user.delete',
            'role.view', 'role.update', 'role.delete',
            'permission.view', 'permission.update', 'permission.delete',
            'company.view', 'company.update', 'company.delete',
        ])->map(fn ($code) => Permission::create([
            'name' => $code,
            'code' => $code,
            'group' => 'Teste',
        ]));

        $this->userA = $this->tenantUser('a@test.com', $this->companyA, $permissions->pluck('id'));
        $this->userB = $this->tenantUser('b@test.com', $this->companyB, $permissions->pluck('id'));

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
        Auth::forgetGuards();
        TenantContext::current()->forget();

        parent::tearDown();
    }

    protected function tenantUser(string $email, Company $company, $permissionIds): User
    {
        $role = Role::create(['name' => 'Admin '.$email, 'company_id' => $company->id]);
        $role->permissions()->sync($permissionIds);

        return User::create([
            'name' => 'Admin '.$email,
            'email' => $email,
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $company->id,
            'role_id' => $role->id,
        ]);
    }

    protected function tokenHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken];
    }

    public function test_business_model_without_super_admin_column_is_scoped(): void
    {
        TenantContext::current()->runFor($this->companyA->id, fn () => TenantProbe::create(['name' => 'da empresa A']));
        TenantContext::current()->runFor($this->companyB->id, fn () => TenantProbe::create(['name' => 'da empresa B']));

        Auth::login($this->userA);

        $probes = TenantProbe::all();

        $this->assertCount(1, $probes);
        $this->assertSame('da empresa A', $probes->first()->name);
        $this->assertSame($this->companyA->id, $probes->first()->company_id);
    }

    public function test_business_model_stamps_company_from_authenticated_user(): void
    {
        Auth::login($this->userB);

        $probe = TenantProbe::create(['name' => 'novo', 'company_id' => $this->companyA->id]);

        $this->assertSame($this->companyB->id, $probe->company_id);
    }

    public function test_tenant_context_scopes_queries_without_authenticated_user(): void
    {
        $this->assertFalse(Auth::hasUser());

        $count = TenantContext::current()->runFor(
            $this->companyA->id,
            fn () => User::count()
        );

        $this->assertSame(1, $count);
        $this->assertSame(3, User::count(), 'Fora do contexto a query volta a ser global.');
    }

    public function test_tenant_context_as_super_admin_sees_every_tenant(): void
    {
        $count = TenantContext::current()->runAsSuperAdmin(fn () => User::count());

        $this->assertSame(3, $count);
    }

    public function test_super_admin_can_create_user_for_a_chosen_company(): void
    {
        $response = $this->withHeaders($this->tokenHeaders($this->superAdmin))
            ->postJson('/api/v1/users', [
                'name' => 'Novo',
                'email' => 'novo@test.com',
                'password' => 'password123',
                'company_id' => $this->companyB->id,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', ['email' => 'novo@test.com', 'company_id' => $this->companyB->id]);
    }

    public function test_super_admin_cannot_assign_role_from_another_company(): void
    {
        $roleB = Role::withoutGlobalScopes()->where('company_id', $this->companyB->id)->firstOrFail();

        $this->withHeaders($this->tokenHeaders($this->superAdmin))
            ->postJson('/api/v1/users', [
                'name' => 'Novo',
                'email' => 'cruzado@test.com',
                'password' => 'password123',
                'company_id' => $this->companyA->id,
                'role_id' => $roleB->id,
            ])
            ->assertStatus(422);

        $this->assertDatabaseMissing('users', ['email' => 'cruzado@test.com']);
    }

    public function test_tenant_cannot_assign_role_from_another_company(): void
    {
        $roleB = Role::withoutGlobalScopes()->where('company_id', $this->companyB->id)->firstOrFail();

        $this->withHeaders($this->tokenHeaders($this->userA))
            ->postJson('/api/v1/users', [
                'name' => 'Novo',
                'email' => 'cruzado2@test.com',
                'password' => 'password123',
                'role_id' => $roleB->id,
            ])
            ->assertStatus(422);

        $this->assertDatabaseMissing('users', ['email' => 'cruzado2@test.com']);
    }

    public function test_regular_user_cannot_write_on_global_permission(): void
    {
        $permission = Permission::where('code', 'user.view')->firstOrFail();

        $this->assertTrue(Gate::forUser($this->userA)->allows('view', $permission));
        $this->assertTrue(Gate::forUser($this->userA)->denies('update', $permission));
        $this->assertTrue(Gate::forUser($this->userA)->denies('delete', $permission));
        $this->assertTrue(Gate::forUser($this->superAdmin)->allows('update', $permission));
    }

    public function test_regular_user_cannot_update_permission_through_api(): void
    {
        $permission = Permission::where('code', 'user.view')->firstOrFail();

        $this->withHeaders($this->tokenHeaders($this->userA))
            ->putJson("/api/v1/permissions/{$permission->id}", ['name' => 'Renomeada'])
            ->assertStatus(403);

        $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'name' => 'user.view']);
    }

    public function test_regular_user_cannot_delete_own_company(): void
    {
        $this->assertTrue(Gate::forUser($this->userA)->allows('update', $this->companyA));
        $this->assertTrue(Gate::forUser($this->userA)->denies('delete', $this->companyA));
        $this->assertTrue(Gate::forUser($this->superAdmin)->allows('delete', $this->companyA));
    }

    public function test_regular_user_cannot_write_on_role_of_another_company(): void
    {
        $roleB = Role::withoutGlobalScopes()->where('company_id', $this->companyB->id)->firstOrFail();

        $this->assertTrue(Gate::forUser($this->userA)->denies('update', $roleB));
        $this->assertTrue(Gate::forUser($this->userA)->denies('delete', $roleB));
    }

    public function test_user_of_suspended_company_cannot_login(): void
    {
        $this->companyA->update(['status' => 'inactive']);

        $this->postJson('/api/v1/login', ['email' => 'a@test.com', 'password' => '12345678'])
            ->assertStatus(401);
    }

    public function test_user_of_expired_trial_company_cannot_login(): void
    {
        $this->companyA->update(['trial_ends_at' => now()->subDay()]);

        $this->postJson('/api/v1/login', ['email' => 'a@test.com', 'password' => '12345678'])
            ->assertStatus(401);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->userA->update(['status' => 'inactive']);

        $this->postJson('/api/v1/login', ['email' => 'a@test.com', 'password' => '12345678'])
            ->assertStatus(401);
    }

    public function test_existing_token_stops_working_when_company_is_suspended(): void
    {
        $headers = $this->tokenHeaders($this->userA);

        $this->withHeaders($headers)->getJson('/api/v1/users')->assertOk();

        $this->companyA->update(['status' => 'inactive']);

        $this->withHeaders($headers)->getJson('/api/v1/users')->assertStatus(403);
    }

    public function test_panel_access_follows_user_and_company_status(): void
    {
        $panel = filament()->getPanel('admin');

        $this->assertTrue($this->userA->canAccessPanel($panel));

        $this->companyA->update(['status' => 'inactive']);
        $this->assertFalse($this->userA->fresh()->canAccessPanel($panel));

        $this->assertTrue($this->superAdmin->canAccessPanel($panel));
    }

    public function test_activity_log_is_stamped_with_tenant_and_causer(): void
    {
        Auth::login($this->userA);

        Role::create(['name' => 'Perfil Auditado']);

        $activity = Activity::latest('id')->firstOrFail();

        $this->assertSame($this->companyA->id, $activity->company_id);
        $this->assertSame($this->userA->id, $activity->causer_id);
    }

    public function test_avatar_route_requires_authentication(): void
    {
        $this->get(route('avatars.serve', ['user' => $this->userA->id]))
            ->assertStatus(302);
    }

    public function test_avatar_of_another_company_is_not_reachable(): void
    {
        $this->actingAs($this->userA)
            ->get(route('avatars.serve', ['user' => $this->userB->id]))
            ->assertStatus(404);
    }
}

class TenantProbe extends Model
{
    use BelongsToTenant;

    protected $table = 'tenant_probes';

    public $timestamps = false;

    protected $fillable = ['name', 'company_id'];
}
