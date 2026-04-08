<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_pk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('matriks_fra_id');
            $table->float('target_pk')->nullable();
            $table->timestamps();
            
            $table->foreign('kegiatan_id')->references('id')->on('kegiatan')->onDelete('cascade');
            $table->foreign('matriks_fra_id')->references('id')->on('matriks_fra')->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['kegiatan_id', 'matriks_fra_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_pk');
    }
};