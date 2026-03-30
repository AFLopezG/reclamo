<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sindicatos', function (Blueprint $table) {
            if (Schema::hasColumn('sindicatos', 'telefono')) {
                $table->dropColumn('telefono');
            }
            if (Schema::hasColumn('sindicatos', 'direccion')) {
                $table->dropColumn('direccion');
            }
            if (Schema::hasColumn('sindicatos', 'tipo')) {
                $table->dropColumn('tipo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sindicatos', function (Blueprint $table) {
            if (!Schema::hasColumn('sindicatos', 'telefono')) {
                $table->string('telefono')->nullable();
            }
            if (!Schema::hasColumn('sindicatos', 'direccion')) {
                $table->string('direccion')->nullable();
            }
            if (!Schema::hasColumn('sindicatos', 'tipo')) {
                $table->string('tipo')->nullable();
            }
        });
    }
};

