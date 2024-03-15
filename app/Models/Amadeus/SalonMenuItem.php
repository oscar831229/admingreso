<?php

namespace App\Models\Amadeus;

use Illuminate\Database\Eloquent\Model;

class SalonMenuItem extends Model
{
    protected $connection = 'ramopos';
    protected $table      = 'salon_menus_items';
}
