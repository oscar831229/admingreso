<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmResolution extends Model
{
    protected $fillable = ['icm_environment_id', 'invoice_type', 'authorization', 'authorization_from', 'authorization_to', 'prefix', 'initial_consecutive', 'final_consecutive', 'state', 'user_created', 'user_updated'];
}
