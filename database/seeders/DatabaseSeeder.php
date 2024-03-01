<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleTableSeeder::class,
            PermissionTableSeeder::class
        ]);
        User::create(['role_id' => 1, 'name' => 'Admin', 'email' => 'admin@mail.com', 'email_verified_at' => now(), 'password' => bcrypt('secr@te')]);
        $users = User::all();
        foreach ($users as $user) {
            $role = Role::where('id', $user['role_id'])->first();
//            dd($role);
            $user->assignRole($role);

            if ($role->id == config('const.adminRole')) {
                $permissions = Permission::where('guard_name','api')->pluck('id', 'id')->all();
                $role->syncPermissions($permissions);

            } elseif ($role->id == config('const.userRole')) {
                $permissions = Permission::where('name', 'like', 'post-' . '%')
                    ->pluck('id', 'id');
                $role->syncPermissions($permissions);
            }
        }
        Artisan::call('passport:install');
    }
}
