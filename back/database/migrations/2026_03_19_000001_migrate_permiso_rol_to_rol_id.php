<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permiso_rol')) {
            return;
        }

        if (!Schema::hasColumn('permiso_rol', 'rol_id')) {
            Schema::table('permiso_rol', function (Blueprint $table) {
                $table->foreignId('rol_id')->nullable()->after('id')->constrained('roles')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('permiso_rol', 'rol')) {
            $now = now();

            $rolNombres = DB::table('permiso_rol')
                ->select('rol')
                ->whereNotNull('rol')
                ->where('rol', '!=', '')
                ->distinct()
                ->pluck('rol')
                ->map(fn ($n) => strtoupper(trim((string) $n)))
                ->filter(fn ($n) => $n !== '')
                ->values();

            foreach ($rolNombres as $nombre) {
                DB::table('roles')->updateOrInsert(
                    ['nombre' => $nombre],
                    ['descripcion' => null, 'updated_at' => $now, 'created_at' => $now]
                );
            }

            $map = DB::table('roles')->pluck('id', 'nombre');
            $rows = DB::table('permiso_rol')
                ->select('id', 'rol')
                ->whereNull('rol_id')
                ->whereNotNull('rol')
                ->where('rol', '!=', '')
                ->get();

            foreach ($rows as $row) {
                $nombre = strtoupper(trim((string) $row->rol));
                $rolId = $map[$nombre] ?? null;
                if ($rolId) {
                    DB::table('permiso_rol')->where('id', $row->id)->update(['rol_id' => $rolId]);
                }
            }
        }

        // Limpia filas sin rol_id mapeado para no bloquear el cambio a solo IDs.
        DB::table('permiso_rol')->whereNull('rol_id')->delete();

        // Reemplaza índices antiguos basados en `rol` (nombres pueden variar por motor/convención).
        if (Schema::hasColumn('permiso_rol', 'rol')) {
            $indexes = DB::select('SHOW INDEX FROM permiso_rol');
            $toDrop = collect($indexes)
                ->filter(fn ($idx) => (($idx->Column_name ?? null) === 'rol') && (($idx->Key_name ?? '') !== 'PRIMARY'))
                ->pluck('Key_name')
                ->unique()
                ->values();

            foreach ($toDrop as $keyName) {
                try {
                    DB::statement('DROP INDEX `' . str_replace('`', '``', (string) $keyName) . '` ON permiso_rol');
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }

        Schema::table('permiso_rol', function (Blueprint $table) {
            if (!Schema::hasColumn('permiso_rol', 'rol_id')) {
                return;
            }
            $table->unique(['rol_id', 'permiso_id']);
        });

        if (Schema::hasColumn('permiso_rol', 'rol')) {
            Schema::table('permiso_rol', function (Blueprint $table) {
                $table->dropColumn('rol');
            });
        }
    }

    public function down(): void
    {
        // No se intenta reconstruir el esquema anterior (rol string) para evitar perder consistencia.
    }
};
