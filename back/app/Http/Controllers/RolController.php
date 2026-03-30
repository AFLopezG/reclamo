<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    public function index()
    {
        return Rol::with('permisos')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('roles', 'nombre')],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['nombre'] = strtoupper(trim((string) $validated['nombre']));

        return Rol::create($validated);
    }

    public function update(Request $request, Rol $rol)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('roles', 'nombre')->ignore($rol->id)],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $nuevoNombre = strtoupper(trim((string) $validated['nombre']));
        $viejoNombre = (string) $rol->nombre;

        DB::transaction(function () use ($rol, $validated, $nuevoNombre, $viejoNombre) {
            $rol->update([
                'nombre' => $nuevoNombre,
                'descripcion' => $validated['descripcion'] ?? null,
            ]);

            if ($nuevoNombre !== $viejoNombre) {
                if (Schema::hasColumn('users', 'rol')) {
                    User::query()->where('rol', $viejoNombre)->update(['rol' => $nuevoNombre]);
                }
            }
        });

        return $rol->fresh();
    }

    public function destroy(Rol $rol)
    {
        $nombre = (string) $rol->nombre;
        $query = User::query()->where('rol_id', $rol->id);
        if (Schema::hasColumn('users', 'rol')) {
            $query->orWhere('rol', $nombre);
        }

        $count = $query->count();
        if ($count > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el rol: hay usuarios asignados.',
            ], 422);
        }

        DB::transaction(function () use ($rol) {
            DB::table('permiso_rol')->where('rol_id', $rol->id)->delete();
            $rol->delete();
        });

        return response()->json(['success' => true]);
    }
}
