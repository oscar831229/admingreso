<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmCustomerExport extends Model
{
    protected $table='icm_customer_exports';

    const STATUS_PENDING='PENDING';
    const STATUS_PROCESSING='PROCESSING';
    const STATUS_COMPLETED='COMPLETED';
    const STATUS_FAILED='FAILED';

    protected $fillable=[
        'file_name','file_path','status','total_rows','processed_rows','progress',
        'error_message','user_created','started_at','finished_at'
    ];
}
