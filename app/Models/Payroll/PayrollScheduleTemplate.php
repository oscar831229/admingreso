<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PayrollScheduleTemplate extends Model
{
    protected $connection = 'ERP';

    protected $table = 'Payroll.ScheduleTemplate';

    protected $fillable = [''];

    public function ScheduleTemplateDetails()
    {
      return $this->hasMany(PayrollScheduleTemplateConcept::class, 'ScheduleTemplateId', 'Id');
    }

    public static function getTableColumns($filters = [])
    {
      $payrollschedule = new PayrollScheduleTemplate;
      $connectionname = $payrollschedule->getConnection()->getName();
      $columns = Schema::connection($connectionname)->getColumnListing($payrollschedule->getTable());
      foreach ($filters as $key => $filter) {
        if (!in_array($key, $columns)) {
          unset($filters[$key]);
        }
      }
      return $filters;
    }

}
