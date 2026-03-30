<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('multas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_hora')->index();

            $table->foreignId('sancion_id')->constrained('sanciones');
            $table->foreignId('taxi_id')->nullable()->constrained('taxis')->nullOnDelete();
            $table->foreignId('licencia_id')->nullable()->constrained('licencias')->nullOnDelete();
            $table->foreignId('propietario_id')->nullable()->constrained('contribuyentes')->nullOnDelete();
            $table->foreignId('conductor_id')->nullable()->constrained('contribuyentes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Snapshots para búsquedas rápidas / reporte
            $table->string('placa')->nullable()->index();
            $table->string('num_licencia')->nullable()->index();
            $table->string('cedula_propietario')->nullable()->index();
            $table->string('cedula_conductor')->nullable()->index();

            $table->timestamps();

            $table->index(['fecha_hora', 'placa']);
            $table->index(['fecha_hora', 'cedula_conductor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multas');
    }
};

