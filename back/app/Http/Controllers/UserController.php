<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    public function logout(Request $request)
    {
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
}
