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
        Schema::create('matriks_fra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_fra_id')->constrained('template_fra')->onDelete('cascade');
            $table->string('tujuan');
            $table->string('sasaran');
            $table->string('indikator');
            $table->string('detail_indikator')->nullable();
            $table->string('sub_indikator')->nullable();
            $table->string('detail_sub')->nullable();
            $table->string('jenis_iku_proksi')->nullable();
            $table->string('jenis_waktu')->nullable();
            $table->string('jenis_persen')->nullable();
            $table->string('satuan', 50)->nullable();
            $table->unsignedBigInteger('parent_sub_id')->nullable();
            $table->integer('excel_row')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriks_fra');
    }
};