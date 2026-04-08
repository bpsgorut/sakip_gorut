<?php

namespace Database\Seeders;

use App\Models\Sub_Komponen;
use Illuminate\Database\Seeder;

class SubKomponenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subKomponens = [
            [
                'id' => 1,
                'sub_komponen' => 'Manajemen Renstra',
                'komponen_id' => 1,
            ],
            [
                'id' => 2,
                'sub_komponen' => 'Manajemen RKT',
                'komponen_id' => 1,
            ],
            [
                'id' => 3,
                'sub_komponen' => 'Manajemen PK',
                'komponen_id' => 1,
            ],
            [
                'id' => 4,
                'sub_komponen' => 'SK Tim SAKIP',
                'komponen_id' => 2,
            ],
            [
                'id' => 5,
                'sub_komponen' => 'SKP (Sasaran Kinerja Pegawai)',
                'komponen_id' => 2,
            ],
            [
                'id' => 6,
                'sub_komponen' => 'Reward Punishment',
                'komponen_id' => 2,
            ],
            [
                'id' => 7,
                'sub_komponen' => 'Form Rencana Aksi',
                'komponen_id' => 2,
            ],
            [
                'id' => 8,
                'sub_komponen' => 'Lakin (Laporan Kinerja)',
                'komponen_id' => 3,
            ],
        ];

        foreach ($subKomponens as $sub_komponen) {
            Sub_Komponen::updateOrCreate(['id' => $sub_komponen['id']], $sub_komponen);
        }
    }
}
