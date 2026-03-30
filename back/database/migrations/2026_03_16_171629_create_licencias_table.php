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
        Schema::create('licencias', function (Blueprint $table) {
            $table->id();
            $table->string('num_licencia')->unique();
            $table->date('fecha_licencia')->nullable();
            $table->string('tipo')->nullable();
            $table->string('estado')->nullable();
            $table->unsignedBigInteger('taxi_id')->nullable();
            $table->foreign('taxi_id')->references('id')->on('taxis');
            $table->unsignedBigInteger('sindicato_id')->nullable();
            $table->foreign('sindicato_id')->references('id')->on('sindicatos');
            $table->unsignedBigInteger('chofer_id')->nullable();
            $table->foreign('chofer_id')->references('id')->on('contribuyentes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licencias');
    }
};
