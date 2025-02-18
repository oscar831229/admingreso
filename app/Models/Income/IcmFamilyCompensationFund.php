<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmFamilyCompensationFund extends Model
{
    protected $fillable = ['code', 'document_number', 'name', 'user_created', 'user_updated'];
}
