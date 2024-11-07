<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Artisan;

use App\Models\Income\IcmCoverage;

class ExecuteCoverage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $date;

     public $timeout = 0; // Establece el tiempo de ejecución a null para que sea ilimitado

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date)
    {
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        # Inactivo las cobertuas de la fecha.
        IcmCoverage::where(['coverage_date' => $this->date])->update(['is_deleted' => 1]);

        // Ejecutar el comando php artisan execute:coverage con la fecha dinámica
        Artisan::call('execute:coverage', ['liquidation_date' => $this->date]);

    }

}
