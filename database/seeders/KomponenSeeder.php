<?php

namespace Database\Seeders;

use App\Models\Komponen;
use Illuminate\Database\Seeder;

class KomponenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $komponens = [
            [
                'id' => 1,
                'komponen' => 'Perencanaan Kinerja',
            ],
            [
                'id' => 2,
                'komponen' => 'Pengukuran Kinerja',
            ],
            [
                'id' => 3,
                'komponen' => 'Pelaporan Kinerja',
            ],
        ];

        foreach ($komponens as $komponen) {
            Komponen::updateOrCreate(['id' => $komponen['id']], $komponen);
        }
    }
}
