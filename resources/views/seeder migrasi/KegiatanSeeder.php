<?php
namespace Database\Seeders;
use App\Models\Kegiatan;
use App\Models\Renstra;
use Illuminate\Database\Seeder;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Since RenstraSeeder is run before this seeder,
        // we can simply use the ID of the first Renstra
        $renstraId = Renstra::first()->id;
        
        // If no Renstra exists for some reason, fail gracefully
        if (!$renstraId) {
            $this->command->error('No Renstra record found. Please run RenstraSeeder first.');
            return;
        }

        $kegiatans = [
            [
                'id' => 1,
                'sub_komponen_id' => 1,
                'renstra_id' => $renstraId,
                'nama_kegiatan' => 'Reviu Target Renstra Tahun',
                'tahun_berjalan' => '2023',
                'tanggal_mulai' => '2023-01-01',
                'tanggal_berakhir' => '2023-12-31',
            ],
            [
                'id' => 2,
                'sub_komponen_id' => 1,
                'renstra_id' => $renstraId,
                'nama_kegiatan' => 'Reviu Target Renstra Tahun',
                'tahun_berjalan' => '2022',
                'tanggal_mulai' => '2022-01-01',
                'tanggal_berakhir' => '2022-12-31',
            ],
            [
                'id' => 3,
                'sub_komponen_id' => 1,
                'renstra_id' => $renstraId,
                'nama_kegiatan' => 'Reviu Target Renstra Tahun',
                'tahun_berjalan' => '2021',
                'tanggal_mulai' => '2021-01-01',
                'tanggal_berakhir' => '2021-12-31',
            ],
        ];
        
        foreach ($kegiatans as $kegiatan) {
            Kegiatan::updateOrCreate(['id' => $kegiatan['id']], $kegiatan);
        }
    }
}