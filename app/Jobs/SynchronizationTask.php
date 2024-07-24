<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SynchronizationTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $component;
    private $document_number;

    public $timeout = null; // Establece el tiempo de ejecución a null para que sea ilimitado

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($component, $document_number = '')
    {
        $this->component       = $component;
        $this->document_number = $document_number;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        synchronizePOSSystem($this->component, $this->document_number);
    }
}
