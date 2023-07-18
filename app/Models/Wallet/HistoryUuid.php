<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class HistoryUuid extends Model
{
    protected $fillable = ['customer_id', 'uuid', 'user_created', 'user_updated'];
}
