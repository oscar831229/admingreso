<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Clases\Cajasan\Parameters;
use App\Clases\Cajasan\SynchronizeAffiliates;

class SincronizarAfiliados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sincronizar:afiliados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para la sincronización de afiliados';

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
     * @return int
     */
    public function handle()
    {

        $user_id   = 1;
        $parameter = new Parameters;
        $parameter->type_synchronization = 'T';
        $parameter->type_execution       = 'A';
        $parameter->user_id              = $user_id;

        $syncafiliados = new SynchronizeAffiliates;
        $syncafiliados->execute($parameter);

    }
}
