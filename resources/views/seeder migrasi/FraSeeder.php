<?php

namespace Database\Seeders;

use App\Models\Fra;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fra = [
            [
                'nama_fra' => 'Form Rencana Aksi',
                'tahun_berjalan' => '2024',
                'file_template' => 'Form Rencana Aksi 2024.xlsx',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        Fra::insert($fra);
    }
}
