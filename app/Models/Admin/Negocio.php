<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Negocio extends Model
{
    protected $table = 'unidades_negocio';
    
    protected $primaryKey = 'id_un';

    public function getSucursales(){
        return $this->hasMany('App\Models\Admin\Sucursal', 'id_un', 'id_un');
    }

    public function usuarios(){
        return $this->hasMany('App\User', 'id_u', 'id_un');
    }
}
