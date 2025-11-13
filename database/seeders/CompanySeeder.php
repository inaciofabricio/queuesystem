<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{

    public function run(): void
    {
        $companies = [];

        for ($i=1; $i <= 3 ; $i++) {

            $companies[] = [
                'company_name' => 'Empresa ' . $i,
                'company_logo' => 'Empresa_0' . $i . '.png',
                'uuid' => Str::uuid(),
                'address' => 'Rua da empresa ' . $i . ', 123, Bairro Exemplo, Cidade Exemplo',
                'phone' => '987654432' . $i,
                'email' => 'empresa' . $i . '@gmail.com',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ];
        }

        DB::table('companies')->insert($companies);
        echo count($companies) . " Empresas de teste foram criadas com sucesso!";
    }
}
