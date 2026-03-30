<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermisoController extends Controller
{
    public function index()
    {
        return Permiso::query()->orderBy('menu')->orderBy('codigo')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:255', Rule::unique('permisos', 'codigo')],
            'nombre' => ['required', 'string', 'max:255'],
            'menu' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['codigo'] = strtolower(trim((string) $validated['codigo']));

        return Permiso::create($validated);
    }

    public function update(Request $request, Permiso $permiso)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:255', Rule::unique('permisos', 'codigo')->ignore($permiso->id)],
            'nombre' => ['required', 'string', 'max:255'],
            'menu' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['codigo'] = strtolower(trim((string) $validated['codigo']));

        $permiso->update($validated);

        return $permiso->fresh();
    }

    public function destroy(Permiso $permiso)
    {
        $permiso->delete();

        return response()->json(['success' => true]);
    }

    public function roles()
    {
        $asignadosIds = DB::table('permiso_rol')
            ->select('rol_id', 'permiso_id')
            ->orderBy('rol_id')
            ->get()
            ->groupBy('rol_id')
            ->map(function ($rows) {
                return $rows->pluck('permiso_id')->values();
            });

        return response()->json([
            'asignados_ids' => $asignadosIds,
        ]);
    }

    public function asignar(Request $request)
    {
        $validated = $request->validate([
            'rol_id' => ['nullable', 'integer', 'exists:roles,id'],
            'rol' => ['nullable', 'string', 'max:255'],
            'permisos' => ['array'],
            'permisos.*' => [],
        ]);

        $rolId = $validated['rol_id'] ?? null;
        $rolNombre = strtoupper(trim((string) ($validated['rol'] ?? '')));

        if (!$rolId && $rolNombre === '') {
            return response()->json(['message' => 'El rol es requerido.'], 422);
        }

        if (!$rolId && $rolNombre !== '') {
            $rolId = Rol::query()->where('nombre', $rolNombre)->value('id');
            if (!$rolId) {
                $rol = Rol::create(['nombre' => $rolNombre, 'descripcion' => null]);
                $rolId = $rol->id;
            }
        }

        $permisosInput = collect($validated['permisos'] ?? [])
            ->filter(fn ($v) => $v !== null && $v !== '');

        $allNumeric = $permisosInput->every(function ($v) {
            if (is_int($v)) return true;
            if (is_string($v) && preg_match('/^\d+$/', $v)) return true;
            return false;
        });

        if ($allNumeric) {
            $permisoIds = $permisosInput
                ->map(fn ($v) => (int) $v)
                ->filter(fn ($v) => $v > 0)
                ->unique()
                ->values();

            $permisoIds = Permiso::query()
                ->whereIn('id', $permisoIds)
                ->pluck('id')
                ->values();
        } else {
            $codigos = $permisosInput
                ->map(fn ($c) => strtolower(trim((string) $c)))
                ->filter(fn ($c) => $c !== '')
                ->unique()
                ->values();

            $permisoIds = Permiso::query()
                ->whereIn('codigo', $codigos)
                ->pluck('id')
                ->values();
        }

        DB::transaction(function () use ($rolId, $permisoIds) {
            DB::table('permiso_rol')->where('rol_id', $rolId)->delete();

            $now = now();
            $rows = $permisoIds->map(fn ($id) => [
                'rol_id' => $rolId,
                'permiso_id' => $id,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if (!empty($rows)) {
                DB::table('permiso_rol')->insert($rows);
            }
        });

        return response()->json(['success' => true]);
    }
}
