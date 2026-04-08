<?php

// Membuat migrasi untuk tabel pengguna
// Nama file: database/migrations/yyyy_mm_dd_create_pengguna_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('password'); // Menambahkan kolom password
            $table->string('jabatan', 255);
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('role_id')->references('id')->on('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};