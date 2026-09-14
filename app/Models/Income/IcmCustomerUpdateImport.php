<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;

class IcmCustomerUpdateImport extends Model
{
    protected $table = 'icm_customer_update_imports';

    const STATUS_PENDING = 'PENDING';
    const STATUS_VALIDATING = 'VALIDATING';
    const STATUS_READY = 'READY';
    const STATUS_APPLY_QUEUED = 'APPLY_QUEUED';
    const STATUS_APPLYING = 'APPLYING';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_VALIDATION_FAILED = 'VALIDATION_FAILED';
    const STATUS_APPLY_FAILED = 'APPLY_FAILED';

    protected $fillable = [
        'original_name','file_path','delimiter','status','total_rows','processed_rows',
        'valid_rows','error_rows','updated_rows','progress','error_message','user_created',
        'validation_started_at','validated_at','apply_started_at','finished_at'
    ];

    public function rows()
    {
        return $this->hasMany(IcmCustomerUpdateStaging::class, 'import_id', 'id');
    }
}
