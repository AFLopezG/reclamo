<?php

namespace App\Http\Controllers;

use App\Models\Sindicato;
use Illuminate\Http\Request;

class SindicatoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Sindicato::orderBy('nombre')->get();
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $sindicato = Sindicato::create([
            'nombre' => strtoupper($validated['nombre']),
        ]);

        return $sindicato;
    }

    /**
     * Display the specified resource.
     */
    public function show(Sindicato $sindicato)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sindicato $sindicato)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sindicato $sindicato)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $sindicato->update([
            'nombre' => strtoupper($validated['nombre']),
        ]);

        return $sindicato;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sindicato $sindicato)
    {
        $sindicato->delete();

        return response()->json(['success' => true]);
    }
}
