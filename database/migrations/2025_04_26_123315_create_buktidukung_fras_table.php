<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buktidukung_fra', function (Blueprint $table) {
            $table->id();
            $table->string('file', 255);
            $table->string('nama_dokumen', 255);
            $table->unsignedBigInteger('realisasi_fra_id');
            $table->timestamps();
            
            $table->foreign('realisasi_fra_id')->references('id')->on('realisasi_fra');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buktidukung_fra');
    }
};