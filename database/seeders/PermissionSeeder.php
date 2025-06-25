<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'user-create', 'user-edit', 'user-delete', 'user-read',
            'permission-create', 'permission-edit', 'permission-delete', 'permission-read',
        ];

        foreach($permissions as $permission) 
        {
            Permission::firstOrCreate(['name'=> $permission]);
        }
    }
}
