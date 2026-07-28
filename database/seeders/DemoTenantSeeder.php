<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoTenantSeeder extends Seeder
{
    protected const PASSWORD = '12345678';

    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        $tenantPermissions = Permission::whereNotIn('code', [
            'company.create', 'company.delete',
            'permission.create', 'permission.edit', 'permission.update', 'permission.delete',
        ])->pluck('id');

        $viewOnlyPermissions = Permission::whereIn('code', ['user.view', 'role.view'])->pluck('id');

        $alpha = $this->company('Alpha Tecnologia', 'alpha-tecnologia');
        $alphaAdmin = $this->role($alpha, 'Admin', 'Acesso total da empresa', $tenantPermissions);
        $alphaViewer = $this->role($alpha, 'Consulta', 'Somente leitura', $viewOnlyPermissions);

        $this->user('Ana Alpha (admin)', 'admin.alpha@demo.test', $alpha, $alphaAdmin);
        $this->user('Bruno Alpha (consulta)', 'consulta.alpha@demo.test', $alpha, $alphaViewer);
        $this->user('Carla Alpha (inativa)', 'inativa.alpha@demo.test', $alpha, $alphaAdmin, status: 'inactive');

        $beta = $this->company('Beta Comércio', 'beta-comercio');
        $betaAdmin = $this->role($beta, 'Admin', 'Acesso total da empresa', $tenantPermissions);

        $this->user('Diego Beta (admin)', 'admin.beta@demo.test', $beta, $betaAdmin);

        $gama = $this->company('Gama Suspensa', 'gama-suspensa', status: 'inactive');
        $gamaAdmin = $this->role($gama, 'Admin', 'Acesso total da empresa', $tenantPermissions);

        $this->user('Elis Gama (empresa suspensa)', 'admin.gama@demo.test', $gama, $gamaAdmin);

        $delta = $this->company('Delta Trial', 'delta-trial', trialEndsAt: now()->subDay());
        $deltaAdmin = $this->role($delta, 'Admin', 'Acesso total da empresa', $tenantPermissions);

        $this->user('Fabio Delta (trial expirado)', 'admin.delta@demo.test', $delta, $deltaAdmin);

        User::updateOrCreate(
            ['email' => 'super@demo.test'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt(self::PASSWORD),
                'status' => 'active',
                'is_super_admin' => true,
                'company_id' => null,
                'role_id' => null,
                'email_verified_at' => now(),
            ]
        );

        $this->summary();
    }

    protected function company(string $name, string $slug, string $status = 'active', $trialEndsAt = null): Company
    {
        return Company::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'email' => "contato@{$slug}.test",
                'status' => $status,
                'trial_ends_at' => $trialEndsAt,
            ]
        );
    }

    protected function role(Company $company, string $name, string $description, $permissionIds): Role
    {
        $role = Role::updateOrCreate(
            ['company_id' => $company->id, 'name' => $name],
            ['description' => $description, 'is_super_admin' => false]
        );

        $role->permissions()->sync($permissionIds);

        return $role;
    }

    protected function user(
        string $name,
        string $email,
        Company $company,
        Role $role,
        string $status = 'active'
    ): User {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt(self::PASSWORD),
                'status' => $status,
                'is_super_admin' => false,
                'company_id' => $company->id,
                'role_id' => $role->id,
                'email_verified_at' => now(),
            ]
        );
    }

    protected function summary(): void
    {
        $this->command?->newLine();
        $this->command?->info('Usuários de teste (senha: '.self::PASSWORD.')');
        $this->command?->table(
            ['E-mail', 'Empresa', 'Perfil', 'Esperado'],
            [
                ['admin.alpha@demo.test', 'Alpha Tecnologia', 'Admin', 'Acessa e só vê dados da Alpha'],
                ['consulta.alpha@demo.test', 'Alpha Tecnologia', 'Consulta', 'Só leitura; sem criar/editar/excluir'],
                ['inativa.alpha@demo.test', 'Alpha Tecnologia', 'Admin', 'Bloqueado: usuário inativo'],
                ['admin.beta@demo.test', 'Beta Comércio', 'Admin', 'Não enxerga nada da Alpha'],
                ['admin.gama@demo.test', 'Gama Suspensa', 'Admin', 'Bloqueado: empresa inativa'],
                ['admin.delta@demo.test', 'Delta Trial', 'Admin', 'Bloqueado: trial expirado'],
                ['super@demo.test', '—', '—', 'Enxerga todas as empresas'],
            ]
        );
    }
}
