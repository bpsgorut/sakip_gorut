<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_dukung', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 50);
            $table->string('nama_dokumen', 255);
            $table->string('file_id', 255);
            $table->string('webViewLink', 255);
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('renstra_id');
            $table->timestamps();
            
            $table->foreign('kegiatan_id')->references('id')->on('kegiatan');
            $table->foreign('renstra_id')->references('id')->on('renstra');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_dukung');
    }
};