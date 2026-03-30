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
        Schema::create('taxis', function (Blueprint $table) {
            $table->id();
            $table->string('placa')->unique();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('linea')->nullable();
            $table->string('color')->nullable();
            $table->string('anio')->nullable();
            $table->string('chasis')->nullable();
            $table->string('ruat')->nullable();
            $table->string('soat')->nullable();
            $table->date('fecha_soat')->nullable();
            $table->unsignedBigInteger('propietario_id')->nullable();
            $table->foreign('propietario_id')->references('id')->on('contribuyentes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxis');
    }
};
