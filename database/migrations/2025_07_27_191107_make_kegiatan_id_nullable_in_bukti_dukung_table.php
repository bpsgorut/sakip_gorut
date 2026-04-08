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
        Schema::table('bukti_dukung', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['kegiatan_id']);
            
            // Modify column to be nullable
            $table->unsignedBigInteger('kegiatan_id')->nullable()->change();
            
            // Re-add foreign key constraint
            $table->foreign('kegiatan_id')->references('id')->on('kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bukti_dukung', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['kegiatan_id']);
            
            // Modify column to be not nullable
            $table->unsignedBigInteger('kegiatan_id')->nullable(false)->change();
            
            // Re-add foreign key constraint
            $table->foreign('kegiatan_id')->references('id')->on('kegiatan');
        });
    }
};
