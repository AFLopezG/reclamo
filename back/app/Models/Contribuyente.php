<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribuyente extends Model
{
    use HasFactory;

    protected $appends = [
        'foto_url',
    ];

    protected $fillable = [
        'cedula',
        'comp',
        'nombre',
        'apellido',
        'telefono',
        'direccion',
        'num_licencia',
        'fecha_licencia',
        'categoria',
        'foto',
        'fecha_nacimiento'
    ];

    public function getFotoUrlAttribute(): ?string
    {
        if (empty($this->foto) || !is_string($this->foto)) {
            return null;
        }

        return url('storage/' . ltrim($this->foto, '/'));
    }

    public function licenciasComoChofer()
    {
        return $this->hasMany(Licencia::class, 'chofer_id');
    }
}
