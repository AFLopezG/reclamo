<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licencia extends Model
{
    use HasFactory;
    protected $fillable = [
        'num_licencia',
        'fecha_licencia',
        'vigencia_hasta',
        'tipo',
        'estado',
        'codigo_verificacion',
        'taxi_id',
        'sindicato_id',
        'chofer_id',
        'user_id',
    ];

    public function taxi()
    {
        return $this->belongsTo(Taxi::class);
    }
    public function sindicato()
    {
        return $this->belongsTo(Sindicato::class);
    }
    public function chofer()
    {
        return $this->belongsTo(Contribuyente::class);
    }

    public function logs()
    {
        return $this->hasMany(LogLicencia::class, 'licencia_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
