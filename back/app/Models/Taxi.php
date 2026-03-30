<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxi extends Model
{
    use HasFactory;
    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'linea',
        'color',
        'anio',
        'chasis',
        'ruat',
        'soat',
        'fecha_soat',
        'propietario_id'
    ];

    public function propietario()
    {
        return $this->belongsTo(Contribuyente::class, 'propietario_id');
    }

    public function licencias()
    {
        return $this->hasMany(Licencia::class);
    }
}
