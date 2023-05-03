<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = ['nombre','unidades_negocio_id', 'sucursal_id', 'user_id','estado'];


    public function negocio(){
        return $this->belongsTo('App\Models\Admin\Negocio', 'unidades_negocio_id','id_un');
    }

    public function sucursal(){
        return $this->belongsTo('App\Models\Admin\Sucursal', 'sucursal_id','id_sucursal');
    }

    public function usuario(){
        return $this->belongsTo('App\User', 'user_id','id');
    }
}
