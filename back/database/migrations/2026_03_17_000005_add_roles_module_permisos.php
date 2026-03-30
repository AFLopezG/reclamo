<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $permisos = [
            ['codigo' => 'roles.editar', 'nombre' => 'Roles - Editar', 'menu' => 'Roles'],
        ];

        foreach ($permisos as $p) {
            DB::table('permisos')->updateOrInsert(
                ['codigo' => $p['codigo']],
                [
                    'nombre' => $p['nombre'],
                    'menu' => $p['menu'] ?? null,
                    'descripcion' => $p['descripcion'] ?? null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $permisoId = DB::table('permisos')->where('codigo', 'roles.editar')->value('id');
        if ($permisoId) {
            DB::table('permiso_rol')->updateOrInsert(
                ['rol' => 'ADMINISTRADOR', 'permiso_id' => $permisoId],
                ['updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    public function down(): void
    {
        // no-op
    }
};

