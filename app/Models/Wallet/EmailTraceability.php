<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class EmailTraceability extends Model
{
    protected $fillable = ['process_code', 'where', 'destination', 'attachments', 'user_created', 'user_updated'];
}
