<?php

namespace App\Clases\Cajasan;
use Illuminate\Support\Facades\Http;

use App\Models\Seac\ClientesSeac;
use App\Models\Sisafi\SisafiSyncTracer;
use App\Models\Sisafi\SisafiSeacPersonas;
use App\Models\Sisafi\SisafiSeacTemporal;
use Artisan;
use Log;

use App\Jobs\SincronizarAfiliados;



class SynchronizeAffiliates
{

    public function __construct(){
    }

    public function automaticSynchronization(){

        $parameters = new Parameters;
        $parameters->type_execution             = 'A';
        $parameters->user_id                    =  1;

        $this->execute($parameters);

    }

    public function execute(Parameters $parameters){

        $start_date = date('Y-m-d H:i:s');

        $synctracer = SisafiSyncTracer::create([
            'type_synchronization' => $parameters->type_synchronization,
            'type_execution'       => $parameters->type_execution,
            'type_document'        => $parameters->type_document,
            'document_number'      => $parameters->document_number,
            'user_created'         => $parameters->user_id
        ]);

        SincronizarAfiliados::dispatch($synctracer);


    }

}
