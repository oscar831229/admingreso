<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Income\IcmCoverage;

use App\Clases\Cajasan\Coberturas;


class ExecuteLiquidationCoverage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:coverage {liquidation_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesar las coberturas del día indicado';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        # Capturar argumento
        $liquidation_date = $this->argument('liquidation_date');
        $user_id          = 1;

        $icm_coverage = IcmCoverage::create([
            'coverage_date' => $liquidation_date,
            'user_created'  => $user_id
        ]);

        $coberturas = new Coberturas;
        $coberturas->executeProccessId($icm_coverage->id);




    }
}
