<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function searchpersona(Request $request){
        if($request->comp=='' || $request->comp==null)
            $persona = Persona::where('cedula', $request->cedula)->first();
        else{
            $persona = Persona::where('cedula', $request->cedula)->where('comp',strtoupper($request->comp))->first();

            }

        if ($persona) {
            return response()->json(['success' => true, 'persona' => $persona]);
        } else {
            return response()->json(['success' => false]);
        }
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
    public function store(StorePersonaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show( $cedula)
    {
        //

        return Persona::with('formularios')->where('cedula',$cedula)->first();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Persona $persona)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        //
    }
}
