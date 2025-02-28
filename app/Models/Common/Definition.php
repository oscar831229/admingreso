<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use App\Models\Common\DetailDefinition;

class Definition extends Model
{
    protected $fillable = ['code', 'name', 'details', 'user_created'];


    public function detaildefinitions(){
        return $this->hasMany(DetailDefinition::class);
    }


    public function getDetailDefinitions($code){
        $detail = Definition::where(['code' => $code])->first();

        if(!$detail){
            return [];
        }else{
            return $detail->detaildefinitions->pluck('name','id');
        }

    }


    public function getDetailHomologationDefinitions($code){
        $detail = Definition::where(['code' => $code])->first();

        if(!$detail){
            return [];
        }else{
            return $detail->detaildefinitions->pluck('id','code');
        }

    }

    public function getDetailHomologationAlternativeDefinitions($code){
        $detail = Definition::where(['code' => $code])->first();

        if(!$detail){
            return [];
        }else{
            return $detail->detaildefinitions->pluck('id','alternative_code');
        }

    }

    public function getDetailHomologationAlternativeRevertDefinitions($code){
        $detail = Definition::where(['code' => $code])->first();

        if(!$detail){
            return [];
        }else{
            return $detail->detaildefinitions->pluck('alternative_code', 'id');
        }

    }









}
