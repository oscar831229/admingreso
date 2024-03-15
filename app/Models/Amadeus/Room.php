<?php

namespace App\Models\Amadeus;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $connection = 'ramopos';
    protected $table      = 'salon';
}
