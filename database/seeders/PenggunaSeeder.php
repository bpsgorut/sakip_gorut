<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengguna::query()->delete();

        $users = [
            [
                'name' => 'Aan',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Metodologi dan Informasi Statistik',
                'nip' => '199111202024211001',
                'email' => 'aanp-pppk@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Abdul Karim Frans',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Distribusi dan Jasa',
                'nip' => '198110232025211028',
                'email' => 'abdulfrans-pppk@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Ahdiyaty Rahmi A. Suaib',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Distribusi dan Jasa',
                'nip' => '199909032023022001',
                'email' => 'ahdiyaty.rahmi@bps.go.id',
                'role_id' => 1,
            ],
            [
                'name' => 'Ainur Rosyidah',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Sosial',
                'nip' => '199807272021042001',
                'email' => 'ainur.rosyidah@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Alfath Dias Farras',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Produksi',
                'nip' => '200104162023101001',
                'email' => 'alfath.dias@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Andrianto Saputra',
                'jabatan' => 'Ketua Tim',
                'bidang' => 'Tim Metodologi dan Informasi Statistik',
                'nip' => '199507172018021001',
                'email' => 'andrianto.saputra@bps.go.id',
                'role_id' => 3,
            ],
            [
                'name' => 'Anita Ambarsari',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Distribusi dan Jasa',
                'nip' => '200210102024122001',
                'email' => 'anita.ambarsari@bps.go.id',
                'role_id' => 1,
            ],
            [
                'name' => 'Arianti Nur Faizah',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Neraca Wilayah dan Analisis',
                'nip' => '199809202022012001',
                'email' => 'arianti.nurfaizah@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Aziz Panigoro',
                'jabatan' => 'Kasubag Umum',
                'bidang' => 'Bagian Umum',
                'nip' => '197910312011011006',
                'email' => 'aziz@bps.go.id',
                'role_id' => 1,
            ],
            [
                'name' => 'Bagas Suratno Putra',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Sosial',
                'nip' => '200110092025211008',
                'email' => 'bagasputra-pppk@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Choirul Ummah',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Sosial',
                'nip' => '199808062022012001',
                'email' => 'choirul.ummah@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Depit Rudianto',
                'jabatan' => 'Kepala BPS',
                'bidang' => null,
                'nip' => '198606302009121003',
                'email' => 'depit@bps.go.id',
                'role_id' => 1,
            ],
            [
                'name' => 'Dwi Elly Noviani',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Bagian Umum',
                'nip' => '199711162021042001',
                'email' => 'elly.noviani@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Gusvia Choiri Nisa',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Sosial',
                'nip' => '200208102024122001',
                'email' => 'gusvia.nisa@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Hansir Husa',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Bagian Umum',
                'nip' => '199805142019121001',
                'email' => 'hansir.husa@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Hary Rhomadon',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Sosial',
                'nip' => '200012132023101004',
                'email' => 'hary.rhomadon@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Herdi Tomayahu',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Produksi',
                'nip' => '199306092025211034',
                'email' => 'herditomayahu-pppk@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Insih Mohune',
                'jabatan' => 'Ketua Tim',
                'bidang' => 'Tim Statistik Sosial',
                'nip' => '198309242010032001',
                'email' => 'insih@bps.go.id',
                'role_id' => 3,
            ],
            [
                'name' => 'Intan Rosiana',
                'jabatan' => 'Ketua Tim',
                'bidang' => 'Tim Neraca Wilayah dan Analisis',
                'nip' => '199503192017012001',
                'email' => 'intan.rosiana@bps.go.id',
                'role_id' => 3,
            ],
            [
                'name' => 'Kinanthi Ilham Pradastika',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Neraca Wilayah dan Analisis',
                'nip' => '200202042024122001',
                'email' => 'kinanthi.ilham@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Muhammad Sabri Ekie',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Metodologi dan Informasi Statistik',
                'nip' => '200103102023101002',
                'email' => 'sabri.ekie@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Munif Widodo',
                'jabatan' => 'Ketua Tim',
                'bidang' => 'Tim Statistik Distribusi dan Jasa',
                'nip' => '198211092011011013',
                'email' => 'munif.widodo@bps.go.id',
                'role_id' => 3,
            ],
            [
                'name' => 'Mutiara Nur Tsani Helfiana',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Sosial',
                'nip' => '200112022026032001',
                'email' => 'mutiara.tsani@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Nabil Arbain',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Neraca Wilayah dan Analisis',
                'nip' => '200207192024121003',
                'email' => 'nabil.arbain@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Rahmat Molotolo',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Distribusi dan Jasa',
                'nip' => '198412202009011010',
                'email' => 'rahmat.molotolo@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Ria Indriani',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Produksi',
                'nip' => '200206022026032001',
                'email' => 'riaindriani@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Riswanto Desei',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Produksi',
                'nip' => '198311022009111001',
                'email' => 'riswanto.desei@bps.go.id',
                'role_id' => 4,
            ],
            [
                'name' => 'Safaruddin',
                'jabatan' => 'Ketua Tim',
                'bidang' => 'Tim Statistik Produksi',
                'nip' => '198810082014031003',
                'email' => 'safaruddin@bps.go.id',
                'role_id' => 3,
            ],
            [
                'name' => 'Seizra Aulia Salsabila',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Metodologi dan Informasi Statistik',
                'nip' => '200210072026032001',
                'email' => 'seizraaulia@bps.go.id',
                'role_id' => 1,
            ],
            [
                'name' => 'Yakob Kuslin',
                'jabatan' => 'Anggota Tim',
                'bidang' => 'Tim Statistik Produksi',
                'nip' => '198905102025211064',
                'email' => 'yakobkuslin-pppk@bps.go.id',
                'role_id' => 4,
            ],
        ];

        foreach ($users as $user) {
            Pengguna::create([
                'name' => $user['name'],
                'nip' => $user['nip'],
                'email' => $user['email'],
                'bidang' => $user['bidang'],
                'password' => Hash::make('password'),
                'jabatan' => $user['jabatan'],
                'role_id' => $user['role_id'],
            ]);
        }
    }
}
