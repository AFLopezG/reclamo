<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_licencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licencia_id')->constrained('licencias')->cascadeOnDelete();
            $table->string('tipo'); // EMISION | RENOVACION
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('estado')->nullable(); // ACTIVO | VENCIDO | ANULADO
            $table->timestamps();

            $table->index(['licencia_id', 'fecha_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_licencias');
    }
};

