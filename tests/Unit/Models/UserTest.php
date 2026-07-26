<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
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

    public function test_can_create_user(): void
    {
        $role = Role::create(['name' => 'Admin', 'company_id' => $this->company->id]);

        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@seuracha.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->company->id,
            'role_id' => $role->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'admin@seuracha.com',
            'company_id' => $this->company->id,
        ]);
        $this->assertTrue(Hash::check('12345678', $user->password));
    }

    public function test_can_read_user(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@seuracha.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->company->id,
        ]);

        $found = User::find($user->id);

        $this->assertNotNull($found);
        $this->assertSame('admin@seuracha.com', $found->email);
        $this->assertSame($this->company->id, $found->company_id);
    }

    public function test_can_update_user(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@seuracha.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->company->id,
        ]);

        $user->update(['name' => 'Administrador Geral', 'status' => 'inactive']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Administrador Geral',
            'status' => 'inactive',
        ]);
    }

    public function test_can_delete_user(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@seuracha.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->company->id,
        ]);

        $user->delete();

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
        $this->assertNull(User::find($user->id));
    }

    public function test_user_belongs_to_role(): void
    {
        $role = Role::create(['name' => 'Admin', 'company_id' => $this->company->id]);

        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@seuracha.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->company->id,
            'role_id' => $role->id,
        ]);

        $this->assertTrue($user->role->is($role));
    }

    public function test_user_belongs_to_company(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@seuracha.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->company->id,
        ]);

        $this->assertTrue($user->company->is($this->company));
    }

    public function test_password_is_hidden_from_array(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@seuracha.com',
            'password' => '12345678',
            'status' => 'active',
            'company_id' => $this->company->id,
        ]);

        $this->assertArrayNotHasKey('password', $user->toArray());
    }
}
