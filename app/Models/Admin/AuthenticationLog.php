<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AuthenticationLog extends Model
{
    protected $fillable = [
        'user_id',
        'ipaddress',
        'observation'
    ];
}
