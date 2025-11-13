<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        $users = [
            [
                'email' => 'sysadmin@localhost.com',
                'password' => bcrypt('Aa12345*'),
                'id_company' => 0,
                'role' => 'sys-admin',
                'active' => true
            ],
            [
                'email' => 'clientadmin1@localhost.com',
                'password' => bcrypt('Aa12345*'),
                'id_company' => 1,
                'role' => 'client-admin',
                'active' => true
            ],
            [
                'email' => 'clientadmin2@localhost.com',
                'password' => bcrypt('Aa12345*'),
                'id_company' => 2,
                'role' => 'client-admin',
                'active' => true
            ]
        ];

        DB::table('users')->insert($users);
        echo count($users) . " Usuarios de teste foram criados com sucesso!";
    }
}
