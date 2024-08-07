<?php

namespace App\Models\Amadeus;

use Illuminate\Database\Eloquent\Model;

class TiposSubsidio extends Model
{
    protected $connection = 'ramopos';
    protected $table      = 'tipos_subsidio';
    public $timestamps = false;

    protected $fillable = ['codigo', 'nombre', 'estado'];
}
