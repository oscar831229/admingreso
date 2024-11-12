<?php

namespace App\Clases\Cajasan;
use Illuminate\Support\Facades\Http;

use App\Models\Seac\ClientesSeac;
use App\Models\Sisafi\SisafiSyncTracer;
use App\Models\Sisafi\SisafiSeacPersonas;
use Artisan;
use Log;


class SynchronizeAffiliates
{

    public function __construct(){

    }

    public function automaticSynchronization(){

        # Validar ultima fecha sincronización
        $synchronized_date = SisafiSyncTracer::orderBy('end_date_synchronization', 'DESC')->where(['state' => 'F'])->first();

        if($synchronized_date){

            $start_date_synchronization = $synchronized_date->end_date_synchronization;

            $end_date_synchronization   = date('Y-m-d');

            $sync_type = 'I';

        }else{

            $sync_type = 'G';

            $start_date_synchronization = null;

            $end_date_synchronization   = date('Y-m-d');
            $end_date_synchronization   = '20241022';

        }

        $parameters = new Parameters;
        $parameters->start_date_synchronization = $start_date_synchronization;
        $parameters->end_date_synchronization   = $end_date_synchronization;
        $parameters->type_execution             = 'A';
        $parameters->sync_type                  = $sync_type;
        $parameters->user_id                    = 1;

        $this->execute($parameters);

    }

    public function execute(Parameters $parameters){

        $start_date = date('Y-m-d H:i:s');

        $synctracer = SisafiSyncTracer::create([
            'start_date'                 => $start_date,
            'start_date_synchronization' => $parameters->start_date_synchronization,
            'end_date_synchronization'   => $parameters->end_date_synchronization,
            'type_execution'             => $parameters->type_execution,
            'sync_type'                  => $parameters->sync_type,
            'document_number'            => $parameters->document_number,
            'user_created'               => $parameters->user_id
        ]);

        # Conexion clientes oracle
        $clientesseac = new ClientesSeac;

        if(isset($parameters->start_date_synchronization)){
            $clientesseac->whereDate('fecha_creacion', '>=', $parameters->start_date_synchronization);
        }

        if(isset($parameters->end_date_synchronization)){
            $clientesseac->whereDate('fecha_creacion', '<=', $parameters->end_date_synchronization);
        }

        if(isset($parameters->document_number)){
            $clientesseac->where('identificacion', '=', $parameters->document_number);
        }

        $clientesseac->cursor()->each(function ($cliente) use ($synctracer){

            try {

                $afiliado = SisafiSeacPersonas::where(['tipo_id' => $cliente->tipo_id, 'identificacion' => $cliente->identificacion] )->first();

                $newafiliate = array_merge($cliente->toArray(), ['sisafi_sync_tracer_id' => $synctracer->id]);

                if(!$afiliado){
                    SisafiSeacPersonas::create($newafiliate);
                }else{
                    $afiliado->update($newafiliate);
                }

            } catch (\Throwable $th) {
                Log::info($th->getMessage());
            }

        });

        # Consulta de expedientes actualizados
        if($parameters->sync_type == 'I' && !empty($parameters->start_date_synchronization) && !empty($parameters->end_date_synchronization)){

            $clientesseac = new ClientesSeac;

            $clientesseac->whereDate('fecha_actualizacion', '>=', $parameters->start_date_synchronization);
            $clientesseac->whereDate('fecha_actualizacion', '<=', $parameters->end_date_synchronization);

            $clientesseac->cursor()->each(function ($cliente) use ($synctracer){

                try {

                    $afiliado = SisafiSeacPersonas::where(['tipo_id' => $cliente->tipo_id, 'identificacion' => $cliente->identificacion] )->first();

                    $newafiliate = array_merge($cliente->toArray(), ['sisafi_sync_tracer_id' => $synctracer->id]);

                    if(!$afiliado){
                        SisafiSeacPersonas::create($newafiliate);
                    }else{
                        $afiliado->update($newafiliate);
                    }

                } catch (\Throwable $th) {
                    Log::info($th->getMessage());
                }

            });

        }

        $end_date = date('Y-m-d H:i:s');
        $synctracer->update(['end_date' => $end_date, 'state' => 'F']);

    }

}
