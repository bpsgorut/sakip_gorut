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
        Schema::create('template_fra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fra_id')->constrained('fra')->onDelete('cascade');
            $table->foreignId('template_jenis_id')->constrained('template_jenis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_fra');
    }
};
