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
        Schema::table('buktidukung_fra', function (Blueprint $table) {
            // Drop kolom lama yang tidak diperlukan
            $table->dropColumn('file');
            
            // Tambah kolom baru sesuai dengan struktur bukti_dukung
            $table->string('file_name', 255)->after('nama_dokumen');
            $table->string('google_drive_file_id', 255)->after('file_name');
            $table->string('webViewLink', 500)->after('google_drive_file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buktidukung_fra', function (Blueprint $table) {
            // Kembalikan kolom lama
            $table->string('file', 255)->after('nama_dokumen');
            
            // Drop kolom baru
            $table->dropColumn(['file_name', 'google_drive_file_id', 'webViewLink']);
        });
    }
};