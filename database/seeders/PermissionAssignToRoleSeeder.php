<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionAssignToRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleIds = Role::pluck('id','name');
        $perm = Permission::get();
        foreach($perm as $p){
            DB::table('role_permission')->insert([
                'role_id'=> $roleIds['Owner'],
                'permission_id'=> $p->id
            ]);
            if(in_array($p->name,['user-create','user-read','user-edit'])){
                DB::table('role_permission')->insert([
                'role_id'=> $roleIds['Admin'],
                'permission_id'=> $p->id
                ]);
            }
            if(in_array($p->name,['user-create','user-read'])){
                DB::table('role_permission')->insert([
                'role_id'=> $roleIds['Associate'],
                'permission_id'=> $p->id
                ]);
            }
        }
    }
}
