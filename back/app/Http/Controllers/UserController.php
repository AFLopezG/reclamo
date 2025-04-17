<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use ElephantIO\Client;
use Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return User::where('id','<>',1)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'nombre' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'rol' => 'required',
        ]);
        
        $validated['password']=Hash::make($validated['password']);

        $user = User::create($validated);
        return($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validated = $request->validate([
            'name' => 'required',
            'nombre' => 'required',
            'rol' => 'required',
            'email' => 'required|email',
        ]);
        
        $user = User::find($request->id);
        $user->update($validated);
        return response(['user' => $user]);
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:6',
        ]);
        $request['password']=Hash::make($request['password']);
        $user->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $user=User::find($id);
        $user->delete();
        return response(['message' => 'Usuario eliminado']);
    }

    public function login(Request $request)
    {

        $user = User::where('name',$request->name)->where('estado','ACTIVO')->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $user = User::where('name',$request->name)->first();
                $token = $user->createToken('authToken')->plainTextToken;
                return response(['user' => $user, 'token' => $token]);
            } else {
                return response(['message' => 'Contraseña incorrecta'],500);
            }
        } else {
            return response(['message' => 'Usuario no encontrado'],500);
        }
    }

    public function me(Request $request)
    {
        $user=User::where('id',$request->user()->id)
                    ->where('estado','ACTIVO')
                    ->firstOrFail();
                return $user;
    }

    public function listPosicion(){
        return  User::where('lat', '<>', '0')
        ->where('lng', '<>', '0')
        ->get();
    }


    public function logout(Request $request)
    {
        $user=User::find($request->id);
        if ($user && $user->id != null) {
            // Crear un nuevo Request con los datos y llamarlo en el mismo controlador
            $request2 = new Request([
                'id' => $user->id,
                'lat' => 0,
                'lng' => 0
            ]);
    
            $this->actualizarPosicion($request2);
        }
        $request->user()->tokens()->delete();
        return response(['message' => 'Sesión cerrada']);
    }

    public function cambioEstado($id){
        $user=User::find($id);
        if($user->estado=='ACTIVO')
            $user->estado='INACTIVO';
        else
            $user->estado='ACTIVO';
        $user->save();
    }

    public function actualizarPosicion(Request $request)
    {
        // Validar datos de la solicitud
        $request->validate([
            'id' => 'required',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        // Buscar conjunto en la base de datos
        $user = User::where('id',$request->id)->where('estado','ACTIVO')->first();
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Actualizar coordenadas en la base de datos
        $user->lat = $request->lat;
        $user->lng = $request->lng;

        $user->save();

        $datos = [
            'id' => $user->id,
            'lat' => $user->lat,
            'lng' => $user->lng,
            'nombre' => $user->nombre
        ];
        //dd($datos);

        $url = env('URL_SOCKET', 'http://localhost:4000');
        error_log("🔗 Intentando conectar con: " . $url);
        
            $client = new Client(Client::engine(Client::CLIENT_4X, $url));
            $client->initialize();
            error_log("✅ Conexión establecida con el servidor Socket.IO");
        
            $client->of('/');
            error_log("📡 Enviando datos: " . json_encode($datos));
        
            $client->emit('actualizarPosicion', $datos); 
            error_log("📨 Datos enviados correctamente");
        
            $client->close();
            error_log("🔌 Conexión cerrada");
        
            return response()->json(['success' => true, 'message' => 'Posición actualizada en ' . json_encode($datos)], 200);
    }

}
