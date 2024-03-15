<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmEnvironment extends Model
{
    protected $fillable = ['name', 'state', 'user_created', 'user_updated'];

    public static function gerIncomeServices($environment_id){
        return \DB::table('icm_environments AS ie')
            ->selectRaw("
                ieii.id,
                ieii.name,
                'ADULTOS' AS income_type
            ")
            ->join('icm_environment_income_items AS ieii', 'ieii.icm_environment_id', '=', 'ie.id')
            ->where(['icm_environment_id' => $environment_id])
            ->get();
    }
}
