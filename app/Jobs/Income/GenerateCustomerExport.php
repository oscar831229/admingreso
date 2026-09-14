<?php

namespace App\Jobs\Income;

use App\Services\Income\CustomerExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCustomerExport implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;

    protected $exportId;
    public $timeout=14400;
    public $tries=1;

    public function __construct($exportId)
    {
        $this->exportId=$exportId;
    }

    public function handle(CustomerExportService $service)
    {
        $service->generate($this->exportId);
    }
}
