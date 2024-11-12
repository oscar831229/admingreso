<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Clases\Cajasan\SynchronizeAffiliates;


class SincronizarAfiliados implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $parameters;

    public function __construct($parameters = null)
    {
        $this->parameters = $parameters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $synchronizeAffiliates = new SynchronizeAffiliates();

        if(empty($this->parameters))
            $synchronizeAffiliates->automaticSynchronization();
        else
            $synchronizeAffiliates->execute($this->parameters);

    }

}
