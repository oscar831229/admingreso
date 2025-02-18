<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmConsecutive extends Model
{
    protected $fillable = ['validity', 'consecutive'];

    public static function getConsecutiveValidity()
    {

        # Vigencia
        $validity = now()->year;

        // Buscar si ya existe un consecutivo para el año actual
        $consecutive = self::where('validity', $validity)->lockForUpdate()->first();

        if (!$consecutive) {
            $consecutive = self::create([
                'validity'    => $validity,
                'consecutive' => 1
            ]);
        }else{
            $consecutive->consecutive++;
            $consecutive->save();
        }

        return $consecutive;

    }

}
