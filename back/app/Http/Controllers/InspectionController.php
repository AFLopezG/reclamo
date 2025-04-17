<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Propietario;
use App\Models\Vehicle;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionRequest;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function listInsp($fecha){
        return Inspection::with('propietario')->with('vehicle')->with('user')->whereDate('fecha',$fecha)->get();
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
    public function store(StoreInspectionRequest $request)
    {
        //
        $propietario= Propietario::where('cedula',strtoupper($request->propietario['cedula']))->first();
        if(!isset($propietario))
        {
            $propietario = new Propietario();
            $propietario->cedula = strtoupper($request->propietario['cedula']);
            $propietario->nombre = strtoupper($request->propietario['nombre']);
            $propietario->categoria = strtoupper($request->propietario['categoria']==null?$request->propietario['categoria']:'');
            $propietario->seguro = strtoupper($request->propietario['seguro']);
            $propietario->save();
        }
        else
        {
            $propietario->nombre = strtoupper($request->propietario['nombre']);
            $propietario->seguro = strtoupper($request->propietario['seguro']);
            $propietario->categoria = strtoupper($request->propietario['categoria']==null?$request->propietario['categoria']:'');
            $propietario->save();
        }

        $vehicle = Vehicle::where('placa', strtoupper($request->vehicle['placa']))->first();

        if(!isset($vehicle))
        {
            $vehicle = new Vehicle();
            $vehicle->placa = strtoupper($request->vehicle['placa']);
            $vehicle->tipo = strtoupper($request->vehicle['tipo']);
            $vehicle->marca = strtoupper($request->vehicle['marca']);
            $vehicle->modelo = strtoupper($request->vehicle['modelo']);
            $vehicle->linea = strtoupper($request->vehicle['linea']);
            $vehicle->save();
        }
        else
        {
            $vehicle->tipo = strtoupper($request->vehicle['tipo']);
            $vehicle->marca = strtoupper($request->vehicle['marca']);
            $vehicle->modelo = strtoupper($request->vehicle['modelo']);
            $vehicle->linea = strtoupper($request->vehicle['linea']);
            $vehicle->save();
        }

        $inspeccion = new Inspection();
        $inspeccion->fecha = date('Y-m-d H:i:s');
        $inspeccion->ventanas=$request->ventanas;
        $inspeccion->puertas=$request->puertas;
        $inspeccion->ventilacion=$request->ventilacion;
        $inspeccion->luz=$request->luz;
        $inspeccion->higiene=$request->higiene;
        $inspeccion->triangulo=$request->triangulo;
        $inspeccion->observacion=$request->observacion;
        $inspeccion->radicatoria=$request->radicatoria;
        $inspeccion->calificacion=$request->calificacion;
        $inspeccion->vehicle_id=$vehicle->id;
        $inspeccion->propietario_id = $propietario->id;
        $inspeccion->user_id=$request->user()->id;
        $inspeccion->save();
    

    }

    /**
     * Display the specified resource.
     */
    public function show(Inspection $inspection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inspection $inspection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function actualizar(Request $request)
    {
        //

        $propietario= Propietario::where('cedula',strtoupper($request->propietario['cedula']))->first();
        if(isset($propietario))
        {
            $propietario->nombre = strtoupper($request->propietario['nombre']);
            $propietario->save();
        }

        $inspeccion =  Inspection::find($request->id);
        $inspeccion->observacion=$request->observacion;
        $inspeccion->radicatoria=$request->radicatoria;
        $inspeccion->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inspection $inspection)
    {
        //
    }
}
