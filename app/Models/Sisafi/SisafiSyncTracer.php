<?php

namespace App\Models\Sisafi;

use Illuminate\Database\Eloquent\Model;

class SisafiSyncTracer extends Model
{
    protected $fillable = ['start_date', 'end_date', 'start_date_synchronization', 'end_date_synchronization', 'state', 'type_execution', 'sync_type', 'document_number', 'user_created'];
}
