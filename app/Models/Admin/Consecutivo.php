<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Consecutivo extends Model
{
    protected $fillable = [
        'prefijo',
        'consecutivo_inicial',
        'consecutivo_final',
        'estado',
        'observacion',
        'usuario_crea_id',
        'fecha_inicial',
        'fecha_final'
    ];


}
