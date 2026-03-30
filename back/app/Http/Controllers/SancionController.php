<?php

namespace App\Http\Controllers;

use App\Models\Sancion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SancionController extends Controller
{
    public function index()
    {
        return Sancion::query()->orderBy('tipo')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0'],
        ]);

        $sancion = Sancion::create([
            'tipo' => strtoupper(trim($validated['tipo'])),
            'descripcion' => isset($validated['descripcion']) ? trim((string) $validated['descripcion']) : null,
            'monto' => $validated['monto'],
        ]);

        return $sancion;
    }

    public function update(Request $request, Sancion $sancion)
    {
        $validated = $request->validate([
            'tipo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0'],
        ]);

        $sancion->update([
            'tipo' => strtoupper(trim($validated['tipo'])),
            'descripcion' => isset($validated['descripcion']) ? trim((string) $validated['descripcion']) : null,
            'monto' => $validated['monto'],
        ]);

        return $sancion->fresh();
    }

    public function destroy(Sancion $sancion)
    {
        $sancion->delete();
        return response()->json(['success' => true]);
    }
}

