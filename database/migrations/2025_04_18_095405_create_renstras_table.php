<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renstra', function (Blueprint $table) {
            $table->id();
            $table->string('nama_renstra', 255);
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renstra');
    }
};
