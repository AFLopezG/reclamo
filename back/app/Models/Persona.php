<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    protected $fillable = [
        'cedula',
        'comp',
        'nombre',
        'telefono',
     ];
         public function formularios()
     {
         return $this->hasMany(Formulario::class);
     }
}
