<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class MedicalUnit extends Model
{
    protected $fillable = ['code', 'name','user_created'];
}
