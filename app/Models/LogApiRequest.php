<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogApiRequest extends Model
{
    protected $fillable = ['user_id', 'method', 'url', 'response', 'ip'];
}
