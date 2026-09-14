<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmCustomerUpdateStaging extends Model
{
    protected $table = 'icm_customer_update_staging';
    public $timestamps = false;

    protected $fillable = [
        'import_id','row_number','customer_id','document_type_raw','document_type_id',
        'document_number','first_surname','second_surname','first_name','second_name',
        'birthday_date_raw','birthday_date','gender_raw','gender_id','address','email',
        'status','error_message'
    ];
}
