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
        Pengguna::updateOrCreate(['email' => 'sucipto@bps.go.id'], [
            'name' => 'Dr. Sucipto',
            'nip' => '197001011995031001',
            'password' => bcrypt('password'),
            'jabatan' => 'Kepala BPS',
            'bidang' => null,
            'role_id' => 1, // Kepala BPS
        ]);

        Pengguna::updateOrCreate(['email' => 'ahmad.dahlan@bps.go.id'], [
            'name' => 'Ahmad Dahlan',
            'nip' => '197502021998031002',
            'password' => bcrypt('password'),
            'jabatan' => 'Kasubag Umum',
            'bidang' => 'Bagian Umum',
            'role_id' => 2, // Kasubag Umum
        ]);

        // Ketua & Anggota Tim - Statistik Sosial
        Pengguna::updateOrCreate(['email' => 'budi.santoso@bps.go.id'], [
            'name' => 'Budi Santoso',
            'nip' => '198003032005011001',
            'password' => bcrypt('password'),
            'jabatan' => 'Ketua Tim',
            'bidang' => 'Tim Statistik Sosial',
            'role_id' => 3, // Ketua Tim
        ]);
        Pengguna::updateOrCreate(['email' => 'siti.aminah@bps.go.id'], [
            'name' => 'Siti Aminah',
            'nip' => '198204042006022001',
            'password' => bcrypt('password'),
            'jabatan' => 'Anggota Tim',
            'bidang' => 'Tim Statistik Sosial',
            'role_id' => 4, // Anggota Tim
        ]);

        // Ketua & Anggota Tim - Statistik Produksi
        Pengguna::updateOrCreate(['email' => 'cahyo.widodo@bps.go.id'], [
            'name' => 'Cahyo Widodo',
            'nip' => '198105052005011002',
            'password' => bcrypt('password'),
            'jabatan' => 'Ketua Tim',
            'bidang' => 'Tim Statistik Produksi',
            'role_id' => 3, // Ketua Tim
        ]);
        Pengguna::updateOrCreate(['email' => 'dewi.lestari@bps.go.id'], [
            'name' => 'Dewi Lestari',
            'nip' => '198306062007022002',
            'password' => bcrypt('password'),
            'jabatan' => 'Anggota Tim',
            'bidang' => 'Tim Statistik Produksi',
            'role_id' => 4, // Anggota Tim
        ]);

        // Ketua & Anggota Tim - Statistik Distribusi
        Pengguna::updateOrCreate(['email' => 'eko.prasetyo@bps.go.id'], [
            'name' => 'Eko Prasetyo',
            'nip' => '198207072006011003',
            'password' => bcrypt('password'),
            'jabatan' => 'Ketua Tim',
            'bidang' => 'Tim Statistik Distribusi, KTIP, dan Harga',
            'role_id' => 3, // Ketua Tim
        ]);
        Pengguna::updateOrCreate(['email' => 'fitriani@bps.go.id'], [
            'name' => 'Fitriani',
            'nip' => '198408082008022003',
            'password' => bcrypt('password'),
            'jabatan' => 'Anggota Tim',
            'bidang' => 'Tim Statistik Distribusi, KTIP, dan Harga',
            'role_id' => 4, // Anggota Tim
        ]);

        // Ketua & Anggota Tim - Neraca Wilayah
        Pengguna::updateOrCreate(['email' => 'gatot.subroto@bps.go.id'], [
            'name' => 'Gatot Subroto',
            'nip' => '198309092007011004',
            'password' => bcrypt('password'),
            'jabatan' => 'Ketua Tim',
            'bidang' => 'Tim Statistik Sosial',
            'role_id' => 3, // Ketua Tim
        ]);
        Pengguna::updateOrCreate(['email' => 'hasanah@bps.go.id'], [
            'name' => 'Hasanah',
            'nip' => '198510102009022004',
            'password' => bcrypt('password'),
            'jabatan' => 'Anggota Tim',
            'bidang' => 'Tim Statistik Sosial',
            'role_id' => 4, // Anggota Tim
        ]);
    }
}
