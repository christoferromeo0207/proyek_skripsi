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
        Schema::create('commission_training', function (Blueprint $table) {
            $table->id();
            $table->json('features');   // fitur numeric array, misal [10000, 4.5, …]
            $table->string('label');    // nama kelas/level, misal "Level A"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_trainings');
    }
};
