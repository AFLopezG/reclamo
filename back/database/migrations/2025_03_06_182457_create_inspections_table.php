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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->string('ventanas');
            $table->string('puertas');
            $table->string('ventilacion');
            $table->string('luz');
            $table->string('higiene');
            $table->string('triangulo');
            $table->text('observacion')->nullable();
            $table->string('radicatoria')->nullable();
            $table->string('calificacion');
            $table->unsignedBigInteger('propietario_id')->nullable();
            $table->foreign('propietario_id')->references('id')->on('propietarios');   
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id')->on('vehicles');   
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
