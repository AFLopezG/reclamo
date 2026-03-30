<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $permisos = [
            ['codigo' => 'sanciones.ver', 'nombre' => 'Sanciones - Ver', 'menu' => 'Sanciones'],
            ['codigo' => 'sanciones.editar', 'nombre' => 'Sanciones - Editar', 'menu' => 'Sanciones'],
            ['codigo' => 'multas.registrar', 'nombre' => 'Multas - Registrar', 'menu' => 'Multas'],
            ['codigo' => 'multas.reporte', 'nombre' => 'Multas - Reporte', 'menu' => 'Multas'],
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

        $permisoIds = DB::table('permisos')->pluck('id', 'codigo');
        $rolIds = DB::table('roles')->pluck('id', 'nombre');

        $asignaciones = [
            'ADMINISTRADOR' => [
                'sanciones.ver',
                'sanciones.editar',
                'multas.registrar',
                'multas.reporte',
            ],
            'USUARIO' => [
                'multas.registrar',
                'multas.reporte',
            ],
        ];

        foreach ($asignaciones as $rol => $codigos) {
            $rolNombre = strtoupper(trim((string) $rol));
            $rolId = $rolIds[$rolNombre] ?? null;
            if (!$rolId) {
                continue;
            }
            foreach ($codigos as $codigo) {
                $permisoId = $permisoIds[$codigo] ?? null;
                if (!$permisoId) {
                    continue;
                }
                DB::table('permiso_rol')->updateOrInsert(
                    ['rol_id' => $rolId, 'permiso_id' => $permisoId],
                    ['updated_at' => $now, 'created_at' => $now]
                );
            }
        }
    }

    public function down(): void
    {
        // No se revierte para evitar borrar configuraciones en producción.
    }
};
