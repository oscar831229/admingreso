<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursal';

    protected $primaryKey = 'id_sucursal';

    public function usuarios(){
        return $this->hasMany('App\User', 'id_sucursal', 'id_sucursal');
    }

}
