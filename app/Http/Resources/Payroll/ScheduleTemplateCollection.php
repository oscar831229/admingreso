<?php

namespace App\Http\Resources\Payroll;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ScheduleTemplateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
