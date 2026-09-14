<?php

namespace App\Jobs\Income;

use App\Services\Income\CustomerUpdateImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApplyCustomerUpdateImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importId;
    public $timeout = 14400;
    public $tries = 1;

    public function __construct($importId)
    {
        $this->importId = $importId;
    }

    public function handle(CustomerUpdateImportService $service)
    {
        $service->applyImport($this->importId);
    }
}
