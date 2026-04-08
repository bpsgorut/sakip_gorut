<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triwulan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_triwulan');
            $table->integer('nomor'); // 1, 2, 3, atau 4
            $table->foreignId('fra_id')->constrained('fra')->onDelete('cascade');
            $table->enum('status', ['Belum Mulai', 'Dalam Proses', 'Selesai', 'Terlambat'])->default('Belum Mulai');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triwulan');
    }
};