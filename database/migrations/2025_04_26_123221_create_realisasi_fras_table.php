<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisasi_fra', function (Blueprint $table) {
            $table->id();
            $table->float('realisasi');
            $table->float('capkin_kumulatif');
            $table->float('capkin_setahun');
            $table->text('kendala');
            $table->text('solusi');
            $table->text('tindak_lanjut');
            $table->date('batas_waktu_tindak_lanjut');
            $table->integer('pic_tindak_lanjut');
            $table->unsignedBigInteger('matriks_fra_id');
            $table->unsignedBigInteger('triwulan_id');
            $table->timestamps();
            
            $table->foreign('matriks_fra_id')->references('id')->on('matriks_fra');
            $table->foreign('triwulan_id')->references('id')->on('triwulan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_fra');
    }
};