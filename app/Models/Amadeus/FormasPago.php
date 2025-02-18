<?php

namespace App\Models\Amadeus;

use Illuminate\Database\Eloquent\Model;

class FormasPago extends Model
{
    protected $connection = 'ramopos';
    // protected $table      = 'harandanos.forpag';

    protected $table;


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        # Nombre de base de datos front
        $hfos_name = config('configamadeus.front_name', 'hguarigua');

        $this->table = "{$hfos_name}.forpag";
    }


}
