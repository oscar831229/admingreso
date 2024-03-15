<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmMenu extends Model
{
    protected $fillable = ['name', 'request_name', 'state', 'user_created'];
}
