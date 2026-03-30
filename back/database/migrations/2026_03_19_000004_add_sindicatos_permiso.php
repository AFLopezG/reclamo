<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('permisos')->updateOrInsert(
            ['codigo' => 'sindicatos.editar'],
            [
                'nombre' => 'Sindicatos - Editar',
                'menu' => 'Sindicatos',
                'descripcion' => null,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $permisoId = DB::table('permisos')->where('codigo', 'sindicatos.editar')->value('id');
        if ($permisoId) {
            // Con el nuevo esquema roles↔permisos por IDs.
            $rolId = DB::table('roles')->where('nombre', 'ADMINISTRADOR')->value('id');
            if ($rolId) {
                DB::table('permiso_rol')->updateOrInsert(
                    ['rol_id' => $rolId, 'permiso_id' => $permisoId],
                    ['updated_at' => $now, 'created_at' => $now]
                );
            }
        }
    }

    public function down(): void
    {
        // no-op
    }
};

