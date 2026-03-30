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
        Schema::table('licencias', function (Blueprint $table) {
            if (!Schema::hasColumn('licencias', 'vigencia_hasta')) {
                $table->date('vigencia_hasta')->nullable()->after('fecha_licencia');
            }
            if (!Schema::hasColumn('licencias', 'codigo_verificacion')) {
                $table->string('codigo_verificacion', 64)->nullable()->unique()->after('estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licencias', function (Blueprint $table) {
            if (Schema::hasColumn('licencias', 'codigo_verificacion')) {
                $table->dropUnique(['codigo_verificacion']);
                $table->dropColumn('codigo_verificacion');
            }
            if (Schema::hasColumn('licencias', 'vigencia_hasta')) {
                $table->dropColumn('vigencia_hasta');
            }
        });
    }
};

