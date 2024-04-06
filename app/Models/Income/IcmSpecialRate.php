<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmSpecialRate extends Model
{
    protected $fillable = ['year', 'date', 'name', 'description', 'state', 'user_created', 'user_updated'];
}
