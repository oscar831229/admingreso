<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmTypesIncome extends Model
{
    public function icm_affiliate_categories()
    {
        return $this->belongsToMany(IcmAffiliateCategory::class);
    }
}
