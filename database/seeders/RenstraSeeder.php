<?php

namespace Database\Seeders;

use App\Models\Renstra;
use Illuminate\Database\Seeder;

class RenstraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $renstras = [
            [
                'id' => 1,
                'nama_renstra' => 'Rencana Strategis',
                'periode_awal' => '2020-01-01',
                'periode_akhir' => '2024-12-31',
                'tanggal_mulai' => '2020-01-01',
                'tanggal_selesai' => '2024-12-31',
            ],
        ];

        foreach ($renstras as $renstra) {
            Renstra::updateOrCreate(['id' => $renstra['id']], $renstra);
        }
    }
}