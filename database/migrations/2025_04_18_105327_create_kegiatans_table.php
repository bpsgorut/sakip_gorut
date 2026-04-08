<?php

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
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan', 255);
            $table->string('tahun_berjalan', 4);
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->unsignedBigInteger('sub_komponen_id');
            $table->unsignedBigInteger('renstra_id');
            $table->timestamps();

            $table->foreign('sub_komponen_id')->references('id')->on('sub_komponen');
            $table->foreign('renstra_id')->references('id')->on('renstra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
