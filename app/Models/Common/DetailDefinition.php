<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use App\Models\Officials\Definition;

class DetailDefinition extends Model
{
    protected $fillable = ['code', 'name', 'details','definition_id', 'user_created'];

    public function definition()
    {
        return $this->belongsTo(Definition::class);
    }


}
