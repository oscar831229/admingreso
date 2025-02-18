<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    // protected $table = 'emails';

    protected $fillable = [
        'server','encryption','puerto', 'email', 'password','user_id'
    ];


    public function getPlantillas(){
        return $this->hasMany('App\Models\Admin\PlantillasEmail', 'emails_id');
    }


}
