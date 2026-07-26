<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_company(): void
    {
        $company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'domain' => 'seuracha.com',
            'email' => 'contato@seuracha.com',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'slug' => 'seuracha-store',
        ]);
    }

    public function test_can_read_company(): void
    {
        $company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
        ]);

        $found = Company::find($company->id);

        $this->assertNotNull($found);
        $this->assertSame('Seuracha Store', $found->name);
        $this->assertSame('seuracha-store', $found->slug);
    }

    public function test_can_update_company(): void
    {
        $company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
        ]);

        $company->update(['status' => 'inactive']);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'status' => 'inactive',
        ]);
    }

    public function test_can_delete_company(): void
    {
        $company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
        ]);

        $company->delete();

        $this->assertSoftDeleted('companies', [
            'id' => $company->id,
        ]);
        $this->assertNull(Company::find($company->id));
    }

    public function test_slug_must_be_unique(): void
    {
        Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Company::create([
            'name' => 'Outra Empresa',
            'slug' => 'seuracha-store',
            'status' => 'active',
        ]);
    }

    public function test_settings_is_cast_to_array(): void
    {
        $company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
            'settings' => ['theme' => 'dark', 'locale' => 'pt_BR'],
        ]);

        $company->refresh();

        $this->assertIsArray($company->settings);
        $this->assertSame('dark', $company->settings['theme']);
    }

    public function test_trial_ends_at_is_cast_to_datetime(): void
    {
        $company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
            'trial_ends_at' => '2026-08-01 00:00:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $company->trial_ends_at);
    }

    public function test_company_has_many_users(): void
    {
        $company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@seuracha.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $company->id,
        ]);

        $this->assertTrue($company->users()->whereKey($user->id)->exists());
    }

    public function test_company_has_many_roles(): void
    {
        $company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
        ]);

        $role = Role::create([
            'name' => 'Admin',
            'company_id' => $company->id,
        ]);

        $this->assertTrue($company->roles()->whereKey($role->id)->exists());
    }

    public function test_company_has_many_permissions(): void
    {
        $company = Company::create([
            'name' => 'Seuracha Store',
            'slug' => 'seuracha-store',
            'status' => 'active',
        ]);

        $permission = Permission::create([
            'name' => 'Ver Usuários',
            'code' => 'user.view',
            'group' => 'Usuários',
            'company_id' => $company->id,
        ]);

        $this->assertTrue($company->permissions()->whereKey($permission->id)->exists());
    }
}
