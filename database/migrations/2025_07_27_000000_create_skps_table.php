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
        Schema::create('skps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->enum('jenis', ['bulanan', 'tahunan']);
            $table->tinyInteger('bulan')->nullable(); // 1-12 untuk SKP bulanan, null untuk tahunan
            $table->year('tahun');
            $table->string('file_id'); // ID file di Google Drive
            $table->text('webViewLink'); // Link untuk melihat file
            $table->string('nama_file'); // Nama file asli
            $table->unsignedBigInteger('uploaded_by'); // User yang mengupload
            $table->timestamp('uploaded_at');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('pengguna')->onDelete('cascade');
            $table->foreign('kegiatan_id')->references('id')->on('kegiatan')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('pengguna')->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['user_id', 'kegiatan_id', 'jenis', 'bulan', 'tahun'], 'unique_skp_per_user_period');
            
            // Index untuk performa query
            $table->index(['user_id', 'tahun']);
            $table->index(['kegiatan_id', 'tahun']);
            $table->index(['jenis', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skps');
    }
};