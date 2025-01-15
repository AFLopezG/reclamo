<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha',
        'hora',
        'direccion',
        'descripcion',
        'imagen',
        'cedula',
        //'comp',
        'nombre',
        'telefono',
        'placa',
        'persona_id',
        'vehiculo_id',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
