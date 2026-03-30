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
        Schema::create('soats', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('taxi_id')->nullable();
            $table->foreign('taxi_id')->references('id')->on('taxis');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soats');
    }
};
