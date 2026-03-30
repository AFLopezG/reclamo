<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licencias', function (Blueprint $table) {
            if (!Schema::hasColumn('licencias', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('chofer_id')->constrained('users')->nullOnDelete();
                $table->index('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('licencias', function (Blueprint $table) {
            if (Schema::hasColumn('licencias', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};

