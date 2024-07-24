<?php

namespace App\Models\Seac;

use Illuminate\Database\Eloquent\Model;

class ClientesSeac extends Model
{
    protected $connection = 'oracle_second';

    protected $table = 'SEAC.V_SEAC_PERSONAS';

}
