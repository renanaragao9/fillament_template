<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Users
            ['name' => 'Ver Usuários', 'code' => 'user.view', 'group' => 'Usuários'],
            ['name' => 'Criar Usuários', 'code' => 'user.create', 'group' => 'Usuários'],
            ['name' => 'Editar Usuários', 'code' => 'user.edit', 'group' => 'Usuários'],
            ['name' => 'Atualizar Usuários', 'code' => 'user.update', 'group' => 'Usuários'],
            ['name' => 'Deletar Usuários', 'code' => 'user.delete', 'group' => 'Usuários'],

            // Permissions
            ['name' => 'Ver Permissões', 'code' => 'permission.view', 'group' => 'Permissões'],
            ['name' => 'Criar Permissões', 'code' => 'permission.create', 'group' => 'Permissões'],
            ['name' => 'Editar Permissões', 'code' => 'permission.edit', 'group' => 'Permissões'],
            ['name' => 'Atualizar Permissões', 'code' => 'permission.update', 'group' => 'Permissões'],
            ['name' => 'Deletar Permissões', 'code' => 'permission.delete', 'group' => 'Permissões'],

            // Roles
            ['name' => 'Ver Perfis', 'code' => 'role.view', 'group' => 'Perfis'],
            ['name' => 'Criar Perfis', 'code' => 'role.create', 'group' => 'Perfis'],
            ['name' => 'Editar Perfis', 'code' => 'role.edit', 'group' => 'Perfis'],
            ['name' => 'Atualizar Perfis', 'code' => 'role.update', 'group' => 'Perfis'],
            ['name' => 'Deletar Perfis', 'code' => 'role.delete', 'group' => 'Perfis'],

            // Companies
            ['name' => 'Ver Empresas', 'code' => 'company.view', 'group' => 'Empresas'],
            ['name' => 'Criar Empresas', 'code' => 'company.create', 'group' => 'Empresas'],
            ['name' => 'Editar Empresas', 'code' => 'company.edit', 'group' => 'Empresas'],
            ['name' => 'Atualizar Empresas', 'code' => 'company.update', 'group' => 'Empresas'],
            ['name' => 'Deletar Empresas', 'code' => 'company.delete', 'group' => 'Empresas'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['code' => $permission['code']],
                $permission
            );
        }
    }
}
