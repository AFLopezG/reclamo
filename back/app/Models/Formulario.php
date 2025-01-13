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
        'comp',
        'nombre',
        'telefono',
        'persona_id',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
