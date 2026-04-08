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
        Schema::create('dokumen_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('file', 255);  // Perbesar panjang nama file
            $table->string('file_id', 255)->nullable();  // Tambahkan kolom untuk ID file Google Drive
            $table->string('nama_dokumen', 255);
            $table->string('webViewLink', 255)->nullable();  // Buat nullable
            $table->string('webContentLink', 255)->nullable();  // Tambahkan link download
            $table->unsignedBigInteger('renstra_id')->nullable();  // Buat nullable
            $table->unsignedBigInteger('kegiatan_id')->nullable();  // Tambahkan untuk dokumen kegiatan
            $table->timestamps();

            // Foreign key untuk renstra
            $table->foreign('renstra_id')
                  ->references('id')
                  ->on('renstra')
                  ->onDelete('cascade');

            // Foreign key untuk kegiatan (opsional)
            $table->foreign('kegiatan_id')
                  ->references('id')
                  ->on('kegiatan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_kegiatan');
    }
};