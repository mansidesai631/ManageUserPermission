<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleIds = Role::pluck('id','name');
        
        User::create([
            'name' => 'Owner',
            'email' => 'owner@gmail.com',
            'mobile' => '1234567899',
            'password' => Hash::make('password'),
            'user_type' => $roleIds['Owner'],
        ]);
    }
}
