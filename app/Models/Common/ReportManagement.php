<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class ReportManagement extends Model
{
    protected $filable = ['module', 'code', 'descripcion', 'state'];
}
