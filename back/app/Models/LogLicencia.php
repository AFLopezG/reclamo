<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogLicencia extends Model
{
    use HasFactory;

    protected $table = 'log_licencias';

    protected $fillable = [
        'licencia_id',
        'user_id',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
