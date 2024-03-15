<?php

namespace App\Models\Amadeus;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $connection = 'ramopos';
    protected $table      = 'menus_items';
}
