<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Clases\Cajasan\SynchronizeAffiliates;

use App\Models\Sisafi\SisafiSyncTracer;
use App\Models\Sisafi\SisafiSeacPersonas;
use App\Models\Sisafi\SisafiSeacTemporal;

use App\Models\Seac\ClientesSeac;

use App\Clases\Cajasan\Afiliacion;

use Log;



class SincronizarAfiliados implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $sisafi_sync_tracers;

    public $timeout = 0; // Establece el tiempo de ejecución a null para que sea ilimitado

    public function __construct(SisafiSyncTracer $sisafi_sync_tracers)
    {
        $this->sisafi_sync_tracers = $sisafi_sync_tracers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $synctracer = $this->sisafi_sync_tracers;

        # Vinculaciones afiliados CAJASA
        $vinculaciones = Afiliacion::getVinculacion();

        try {

            # Marcar en ejecución.
            $synctracer->state = 'E';
            $synctracer->update();

            # Iniciar transacción
            if(empty($synctracer->document_number)){

                \DB::table('sisafi_seac_temporal')->truncate();

                # Conexion clientes oracle
                ClientesSeac::whereIn('vinculacion', $vinculaciones)->cursor()->each(function ($cliente) use ($synctracer){

                    try {

                        $newafiliate = array_merge($cliente->toArray(), ['sisafi_sync_tracer_id' => $synctracer->id]);

                        SisafiSeacTemporal::create($newafiliate);

                    } catch (\Throwable $th) {
                        Log::info($th->getMessage());
                    }

                });

                \DB::beginTransaction();

                try {
                    // Truncar la tabla sisafi_seac_personas
                    \DB::table('sisafi_seac_personas')->truncate();

                    // Insertar los datos de sisafi_seac_temporal en sisafi_seac_personas
                    \DB::statement('INSERT INTO sisafi_seac_personas SELECT * FROM sisafi_seac_temporal');

                    // Confirmar la transacción si todo ha ido bien
                    \DB::commit();

                } catch (\Exception $e) {
                    \DB::rollBack();
                    Log::error($e->getMessage());
                }

            }else {

                \DB::table('sisafi_seac_temporal')->truncate();

                # Conexion clientes oracle
                ClientesSeac::where(['tipo_id' => $synctracer->type_document, 'identificacion' => $synctracer->document_number ])->cursor()->each(function ($cliente) use ($synctracer){

                    try {

                        $newafiliate = array_merge($cliente->toArray(), ['sisafi_sync_tracer_id' => $synctracer->id]);

                        SisafiSeacTemporal::create($newafiliate);

                    } catch (\Throwable $th) {
                        Log::info($th->getMessage());
                    }

                });

                # Procesar registros de identificación
                $seac_temporales = SisafiSeacTemporal::where(['sisafi_sync_tracer_id' => $synctracer->id])->get();

                foreach ($seac_temporales as $key => $seac_temporal) {

                    \DB::beginTransaction();

                    try {

                        SisafiSeacPersonas::where([
                            'tipo_id'        => $seac_temporal->tipo_id,
                            'identificacion' => $seac_temporal->identificacion,
                            'id_principal'   => $seac_temporal->id_principal
                        ])->delete();

                        if(in_array($seac_temporal->vinculacion, $vinculaciones)){
                            SisafiSeacPersonas::create($seac_temporal->toArray());
                        }

                        \DB::commit();

                    } catch (\Exception $e) {
                        \DB::rollBack();
                        Log::error($e->getMessage());
                    }
                }

            }

            # Marcar como finalizado
            $synctracer->state  = 'F';
            $synctracer->errors = '';
            $synctracer->update();

        } catch (\Throwable $th) {
            # Marcar como finalizado
            $synctracer->state  = 'B';
            $synctracer->errors = $th->getMessage();
            $synctracer->update();
        }

    }

}
