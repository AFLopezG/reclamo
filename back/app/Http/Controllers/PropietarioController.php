<?php

namespace App\Http\Controllers;

use App\Models\Propietario;
use App\Http\Requests\StorePropietarioRequest;
use App\Http\Requests\UpdatePropietarioRequest;
use App\Models\Vehicle;
use App\Models\Inspection;

class PropietarioController extends Controller
{
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
    public function store(StorePropietarioRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($cedula)
    {
        //
        return Propietario::where('cedula', $cedula)->first();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Propietario $propietario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropietarioRequest $request, Propietario $propietario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Propietario $propietario)
    {
        //
    }
}
