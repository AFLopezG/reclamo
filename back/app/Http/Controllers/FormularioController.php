<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use App\Models\Persona;
use App\Http\Requests\StoreFormularioRequest;
use App\Http\Requests\UpdateFormularioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FormularioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    public function generatePdf($id)
    {
        $reclamo = Formulario::with('persona')->findOrFail($id);
        $qrCode = QrCode::format('png')->size(250)->generate('Nro: '.$reclamo->id.' Fecha:'.$reclamo->fecha.' Ced: '.$reclamo->cedula.'');
        $png = base64_encode($qrCode);
        $data=[
            'qrcode'=>$png,
            'reclamo'=>$reclamo
        ];
        $pdf = PDF::loadView('formulario.pdf', $data);
        
        return $pdf->stream('reporte_' . $reclamo->id . '.pdf');
    }

    public function reportFecha(Request $request){
        return Formulario::whereDate('fecha','>=',$request->ini)->whereDate('fecha','<=',$request->fin)->get();
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
    public function store(StoreFormularioRequest $request)
    {
        //
        $request->validate([
            'cedula' => 'required',
            'comp' => '',
            'nombre' => 'required',
            'telefono' => 'required',
            'direccion' => 'required',
            'descripcion' => 'required',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if($request->comp=='' || $request->comp==null)
            $persona = Persona::where('cedula', $request->cedula)->first();
        else{
            $persona = Persona::where('cedula', $request->cedula)->where('comp',$request->comp)->first();

            }

        if ($persona) {
            // Actualizar datos de la persona si ya existe
            $persona->update([
                'nombre' => strtoupper($request->nombre) ?? $persona->nombre,
                'telefono' => $request->telefono ?? $persona->telefono,
            ]);
        } else {
            // Crear nueva persona si no existe
            $persona = Persona::create([
                'cedula' => $request->cedula,
                'comp' => strtoupper($request->comp),
                'nombre' => strtoupper($request->nombre),
                'telefono' => $request->telefono,
            ]);
        }

        $formulario = $persona->formularios()->create([
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'direccion' => $request->direccion,
            'descripcion' => $request->descripcion,
            'cedula' => $persona->cedula,
            'comp' => $persona->comp,
            'nombre' => $persona->nombre,
            'telefono' => $persona->telefono,
        ]);

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            
            // Renombrar imagen con el ID del formulario y extensión original
            $nombreImagen = $formulario->id . '.' . $imagen->getClientOriginalExtension();
    
            // Redimensionar la imagen usando ImageManager de Intervention
            $image = ImageManager::imagick()->read($imagen->getRealPath());
            $image->scale(800, 800);
            $image->save(public_path('/imagenes/'.$nombreImagen));
    
            // Guardar el nombre de la imagen en la base de datos
            $formulario->update(['imagen' => $nombreImagen]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Formulario registrado con éxito',
            'data' => [
                'persona' => $persona,
                'formulario' => $formulario,
            ]
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Formulario $formulario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Formulario $formulario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormularioRequest $request, Formulario $formulario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formulario $formulario)
    {
        //
    }
}
