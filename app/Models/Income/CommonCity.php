<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class CommonCity extends Model
{
    protected $fillable = ['city_code', 'city_name', 'department_name', 'department_code', 'country_code', 'country_name', 'country_abbreviation', 'user_created', 'user_updated'];
}
