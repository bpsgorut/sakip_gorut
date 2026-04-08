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
        Pengguna::create([
            'name' => 'Dr. Sucipto',
            'nip' => '197001011995031001',
            'email' => 'sucipto@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Kepala BPS',
            'bidang' => '',
            'role_id' => 1, // Kepala BPS
        ]);

        Pengguna::create([
            'name' => 'Ahmad Dahlan',
            'nip' => '197502021998031002',
            'email' => 'ahmad.dahlan@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Kepala Sub Bagian Umum',
            'bidang' => 'Bagian Umum',
            'role_id' => 2, // Kasubag Umum
        ]);

        // Ketua & Anggota Tim - Statistik Sosial
        Pengguna::create([
            'name' => 'Budi Santoso',
            'nip' => '198003032005011001',
            'email' => 'budi.santoso@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Ketua Tim',
            'bidang' => 'Tim Statistik Sosial',
            'role_id' => 3, // Ketua Tim
        ]);
        Pengguna::create([
            'name' => 'Siti Aminah',
            'nip' => '198204042006022001',
            'email' => 'siti.aminah@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Anggota Tim',
            'bidang' => 'Tim Statistik Sosial',
            'role_id' => 4, // Anggota Tim
        ]);

        // Ketua & Anggota Tim - Statistik Produksi
        Pengguna::create([
            'name' => 'Cahyo Widodo',
            'nip' => '198105052005011002',
            'email' => 'cahyo.widodo@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Ketua Tim',
            'bidang' => 'Tim Statistik Produksi',
            'role_id' => 3, // Ketua Tim
        ]);
        Pengguna::create([
            'name' => 'Dewi Lestari',
            'nip' => '198306062007022002',
            'email' => 'dewi.lestari@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Anggota Tim',
            'bidang' => 'Tim Statistik Produksi',
            'role_id' => 4, // Anggota Tim
        ]);

        // Ketua & Anggota Tim - Statistik Distribusi
        Pengguna::create([
            'name' => 'Eko Prasetyo',
            'nip' => '198207072006011003',
            'email' => 'eko.prasetyo@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Ketua Tim',
            'bidang' => 'Tim Statistik Distribusi, KTIP, dan Harga',
            'role_id' => 3, // Ketua Tim
        ]);
        Pengguna::create([
            'name' => 'Fitriani',
            'nip' => '198408082008022003',
            'email' => 'fitriani@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Anggota Tim',
            'bidang' => 'Tim Statistik Distribusi, KTIP, dan Harga',
            'role_id' => 4, // Anggota Tim
        ]);

        // Ketua & Anggota Tim - Neraca Wilayah
        Pengguna::create([
            'name' => 'Gatot Subroto',
            'nip' => '198309092007011004',
            'email' => 'gatot.subroto@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Ketua Tim',
            'bidang' => 'Tim Neraca Wilayah Analisis Statistik dan Penjaminan Kualitas',
            'role_id' => 3, // Ketua Tim
        ]);
        Pengguna::create([
            'name' => 'Hasanah',
            'nip' => '198510102009022004',
            'email' => 'hasanah@bps.go.id',
            'password' => bcrypt('password'),
            'jabatan' => 'Anggota Tim',
            'bidang' => 'Tim Neraca Wilayah Analisis Statistik dan Penjaminan Kualitas',
            'role_id' => 4, // Anggota Tim
        ]);
    }
}