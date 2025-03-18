<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $users = [
            ['full_name'=>'Asep Abdul Mugni','username'=>'asepmugni','email'=>'abankr342@gmail.com','password'=>'asepam220507@','role_id' => 1],
        ];

        collect($users)->map(function($user){
            User::create([
                'full_name'=>$user['full_name'],
                'username'=>$user['username'],
                'email' => $user['email'],
                'password'=>$user['password'],
                'role_id'=>$user['role_id']
            ]);
        });
    }
}