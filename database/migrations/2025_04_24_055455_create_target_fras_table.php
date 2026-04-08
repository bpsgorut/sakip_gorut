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
        Schema::create('target_fra', function (Blueprint $table) {
            $table->id();
            $table->float('target_tw1')->nullable();
            $table->float('target_tw2')->nullable();
            $table->float('target_tw3')->nullable();
            $table->float('target_tw4')->nullable();
            $table->unsignedBigInteger('assign_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('matriks_fra_id');
            $table->timestamps();
            $table->foreign('matriks_fra_id')->references('id')->on('matriks_fra')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_fra');
    }
};