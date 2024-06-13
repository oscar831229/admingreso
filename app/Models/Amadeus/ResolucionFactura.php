<?php

namespace App\Models\Amadeus;

use Illuminate\Database\Eloquent\Model;

class ResolucionFactura extends Model
{
    protected $connection = 'ramopos';
    protected $table      = 'resolucion_factura';
}
