<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha',
        'ventanas',
        'puertas',
        'ventilacion',
        'luz',
        'higiene',
        'triangulo',
        'observacion',
        'radicatoria',
        'calificacion',
        'propietario_id',
        'vehicle_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function propietario()
    {
        return $this->belongsTo(Propietario::class);
    }

}
