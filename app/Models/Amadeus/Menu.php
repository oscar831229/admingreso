<?php

namespace App\Models\Amadeus;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $connection = 'ramopos';
    protected $table      = 'menus';
}
