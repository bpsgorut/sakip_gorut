<?php

namespace Database\Seeders;

use App\Models\Template_Jenis;
use Illuminate\Database\Seeder;

class TemplateJenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenis = [
            [
                'nama' => 'PK IKU',
                'wajib' => true
            ],
            [
                'nama' => 'PK Suplemen',
                'wajib' => false
            ]
        ];

        Template_Jenis::insert($jenis);
    }
}
