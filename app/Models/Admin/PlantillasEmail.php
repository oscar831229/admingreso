<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PlantillasEmail extends Model
{
    protected $table = 'plantillas_email';

    protected $fillable = [
        'codigo','nombre','asunto','mensaje','emails_id'
    ];


    public function email(){
        return $this->belongsTo('App\Models\Admin\Email', 'emails_id');
    }

}
