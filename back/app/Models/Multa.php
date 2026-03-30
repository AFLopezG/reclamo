<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Multa extends Model
{
    use HasFactory;

    protected $table = 'multas';

    protected $fillable = [
        'fecha_hora',
        'sancion_id',
        'taxi_id',
        'licencia_id',
        'propietario_id',
        'conductor_id',
        'user_id',
        'placa',
        'num_licencia',
        'cedula_propietario',
        'cedula_conductor',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function sancion()
    {
        return $this->belongsTo(Sancion::class);
    }

    public function taxi()
    {
        return $this->belongsTo(Taxi::class);
    }

    public function licencia()
    {
        return $this->belongsTo(Licencia::class);
    }

    public function propietario()
    {
        return $this->belongsTo(Contribuyente::class, 'propietario_id');
    }

    public function conductor()
    {
        return $this->belongsTo(Contribuyente::class, 'conductor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

