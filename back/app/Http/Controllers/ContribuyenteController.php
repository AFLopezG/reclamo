<?php

namespace App\Http\Controllers;

use App\Models\Contribuyente;
use App\Http\Requests\StoreContribuyenteRequest;
use App\Http\Requests\UpdateContribuyenteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContribuyenteController extends Controller
{
    private function normalizarComp(?string $comp): ?string
    {
        if ($comp === null) {
            return null;
        }

        $comp = strtoupper(trim($comp));

        return $comp === '' ? null : $comp;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContribuyenteRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Contribuyente $contribuyente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contribuyente $contribuyente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContribuyenteRequest $request, Contribuyente $contribuyente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contribuyente $contribuyente)
    {
        //
    }

    public function buscar(Request $request)
    {
        $validated = $request->validate([
            'cedula' => ['required', 'string', 'max:255'],
            'comp' => ['nullable', 'string', 'max:255'],
        ]);

        $cedula = trim((string) $validated['cedula']);
        $comp = $this->normalizarComp($validated['comp'] ?? null);

        $query = Contribuyente::query()->where('cedula', $cedula);
        if ($comp !== null && $comp !== '') {
            $query->where('comp', $comp);
        }

        $contribuyente = $query->first();

        // Si mandan complemento pero no hay match exacto, devolvemos el registro por CI si existe.
        if (!$contribuyente && $comp !== null && $comp !== '') {
            $contribuyente = Contribuyente::query()->where('cedula', $cedula)->first();
        }

        return response()->json([
            'data' => $contribuyente,
        ]);
    }

    public function actualizar(Request $request)
    {
        $validated = $request->validate([
            'cedula' => ['required', 'string', 'max:255'],
            'comp' => ['nullable', 'string', 'max:255'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ]);

        $cedula = trim((string) $validated['cedula']);
        $comp = $this->normalizarComp($validated['comp'] ?? null);

        $oldFoto = Contribuyente::query()->where('cedula', $cedula)->value('foto');
        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $slug = Str::of($cedula . '_' . ($comp ?? ''))->slug('_')->toString();
            $slug = $slug !== '' ? $slug : 'contribuyente';
            $filename = $slug . '_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();

            $fotoPath = $file->storeAs('contribuyentes', $filename, 'public');
        }

        // Nota: la base actual tiene cedula como unique, por lo que el upsert se basa en cedula.
        $update = [
            'comp' => $comp,
            'nombre' => strtoupper($validated['nombre']),
            'apellido' => isset($validated['apellido']) ? strtoupper($validated['apellido']) : null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'categoria' => isset($validated['categoria']) ? strtoupper($validated['categoria']) : null,
        ];

        if (is_string($fotoPath) && $fotoPath !== '') {
            $update['foto'] = $fotoPath;
        }

        $contribuyente = Contribuyente::updateOrCreate(
            ['cedula' => $cedula],
            $update
        );

        if (is_string($fotoPath) && $fotoPath !== '' && is_string($oldFoto) && $oldFoto !== '' && $oldFoto !== $fotoPath) {
            Storage::disk('public')->delete($oldFoto);
        }

        return response()->json([
            'data' => $contribuyente,
        ]);
    }
}
