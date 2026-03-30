<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $permisos = [
            // Menú
            ['codigo' => 'usuarios.ver', 'nombre' => 'Usuarios - Ver', 'menu' => 'Usuarios'],
            ['codigo' => 'usuarios.editar', 'nombre' => 'Usuarios - Editar', 'menu' => 'Usuarios'],

            ['codigo' => 'reporte.ver', 'nombre' => 'Reporte - Ver', 'menu' => 'Reporte'],

            ['codigo' => 'inspeccion.ver', 'nombre' => 'Inspección - Ver', 'menu' => 'Inspección'],
            ['codigo' => 'inspeccion.editar', 'nombre' => 'Inspección - Editar', 'menu' => 'Inspección'],

            ['codigo' => 'licencias.ver', 'nombre' => 'Licencias - Ver', 'menu' => 'Licencias'],
            ['codigo' => 'licencias.editar', 'nombre' => 'Licencias - Editar', 'menu' => 'Licencias'],
            ['codigo' => 'licencias.renovar', 'nombre' => 'Licencias - Renovar', 'menu' => 'Licencias'],
            ['codigo' => 'licencias.anular', 'nombre' => 'Licencias - Anular', 'menu' => 'Licencias'],
            ['codigo' => 'licencias.imprimir', 'nombre' => 'Licencias - Imprimir', 'menu' => 'Licencias'],

            ['codigo' => 'contribuyentes.editar', 'nombre' => 'Contribuyentes - Editar', 'menu' => 'Licencias'],

            ['codigo' => 'posicion.ver', 'nombre' => 'Posición - Ver', 'menu' => 'Posición'],
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

        $asignaciones = [
            'ADMINISTRADOR' => [
                'usuarios.ver',
                'usuarios.editar',
                'reporte.ver',
                'inspeccion.ver',
                'inspeccion.editar',
                'licencias.ver',
                'licencias.editar',
                'licencias.renovar',
                'licencias.anular',
                'licencias.imprimir',
                'contribuyentes.editar',
                'posicion.ver',
            ],
            // Replica el acceso actual del menú para USUARIO
            'USUARIO' => [
                'reporte.ver',
                'inspeccion.ver',
                'licencias.ver',
                'licencias.imprimir',
                'posicion.ver',
            ],
        ];

        foreach ($asignaciones as $rol => $codigos) {
            foreach ($codigos as $codigo) {
                $permisoId = $permisoIds[$codigo] ?? null;
                if (!$permisoId) {
                    continue;
                }
                DB::table('permiso_rol')->updateOrInsert(
                    ['rol' => $rol, 'permiso_id' => $permisoId],
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

