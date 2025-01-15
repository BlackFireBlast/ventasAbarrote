<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::insert([
        //     [
        //         'name'=> 'Sak Noel',
        //         'email'=> 'admin@gmail.com',
        //         'password'=> bcrypt('password')
        //     ]
        //     ]);

            //Usuario administrador
            $rol = Role::create(['name' => 'administrador']);
            $permisos = Permission::pluck('id','id')->all();
            $rol->syncPermissions($permisos);
            $user = User::find(1);
            $user->assignRole('administrador');
    }
}
