<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_licencias', function (Blueprint $table) {
            if (!Schema::hasColumn('log_licencias', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('licencia_id')->constrained('users')->nullOnDelete();
                $table->index('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('log_licencias', function (Blueprint $table) {
            if (Schema::hasColumn('log_licencias', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};

