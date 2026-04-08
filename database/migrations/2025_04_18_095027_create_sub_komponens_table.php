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
        Schema::create('sub_komponen', function (Blueprint $table) {
            $table->id();
            $table->string('sub_komponen', 255);
            $table->unsignedBigInteger('komponen_id');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('komponen_id')->references('id')->on('komponen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_komponen');
    }
};
