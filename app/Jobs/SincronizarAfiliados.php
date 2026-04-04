<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Sisafi\SisafiSyncTracer;
use App\Models\Seac\ClientesSeac;
use App\Clases\Cajasan\Afiliacion;

class SincronizarAfiliados implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sisafi_sync_tracers;

    public $timeout = 0;

    /**
     * Lote fijo de inserción
     */
    private $batchSize = 1000;

    public function __construct(SisafiSyncTracer $sisafi_sync_tracers)
    {
        $this->sisafi_sync_tracers = $sisafi_sync_tracers;
    }

    public function handle()
    {
        $synctracer    = $this->sisafi_sync_tracers;
        $vinculaciones = Afiliacion::getVinculacion();

        try {
            $this->markAsRunning($synctracer);

            if (empty($synctracer->document_number)) {
                $this->processFullSync($synctracer, $vinculaciones);
            } else {
                $this->processSingleDocument($synctracer, $vinculaciones);
            }

            $this->markAsFinished($synctracer);

        } catch (\Throwable $th) {
            $this->markAsFailed($synctracer, $th);

            Log::error('Error general en SincronizarAfiliados', [
                'trace_id' => $synctracer->id,
                'document' => $synctracer->document_number,
                'message'  => $th->getMessage(),
                'file'     => $th->getFile(),
                'line'     => $th->getLine(),
            ]);

            throw $th;
        }
    }

    private function markAsRunning(SisafiSyncTracer $synctracer)
    {
        $synctracer->state = 'E';
        $synctracer->errors = '';
        $synctracer->total_processed = 0;
        $synctracer->update();
    }

    private function markAsFinished(SisafiSyncTracer $synctracer)
    {
        $synctracer->state = 'F';
        $synctracer->errors = '';
        $synctracer->update();
    }

    private function markAsFailed(SisafiSyncTracer $synctracer, \Throwable $th)
    {
        $message = $this->safeErrorMessage($th);

        try {
            $synctracer->state = 'B';
            $synctracer->errors = $message;
            $synctracer->update();
        } catch (\Throwable $inner) {
            Log::error('No fue posible guardar el error en sisafi_sync_tracers', [
                'trace_id'       => $synctracer->id,
                'original_error' => $th->getMessage(),
                'inner_error'    => $inner->getMessage(),
            ]);
        }
    }

    /**
     * Sincronización completa:
     * lee uno a uno con cursor
     * inserta por lotes de 1000
     * actualiza progreso por lote
     */
    private function processFullSync(SisafiSyncTracer $synctracer, array $vinculaciones)
    {
        DB::table('sisafi_seac_temporal')
            ->where('sisafi_sync_tracer_id', $synctracer->id)
            ->delete();

        $query = ClientesSeac::whereIn('vinculacion', $vinculaciones);

        $synctracer->total_records = (clone $query)->count();
        $synctracer->total_processed = 0;
        $synctracer->update();

        $buffer = [];
        $processed = 0;

        foreach ($query->cursor() as $cliente) {
            $row = $this->normalizeRow($cliente->toArray(), $synctracer->id);

            if (!$this->hasMinimumIdentity($row)) {
                $this->logSkippedRow($row, 'Registro omitido por no tener tipo_id o identificacion');
                continue;
            }

            $buffer[] = $row;

            if (count($buffer) === $this->batchSize) {
                $inserted = $this->insertTemporalBatch($buffer);
                $processed += $inserted;

                $synctracer->total_processed = $processed;
                $synctracer->update();

                $buffer = [];
            }
        }

        if (!empty($buffer)) {
            $inserted = $this->insertTemporalBatch($buffer);
            $processed += $inserted;

            $synctracer->total_processed = $processed;
            $synctracer->update();
        }

        DB::beginTransaction();

        try {
            DB::table('sisafi_seac_personas')->truncate();

            $this->copyTemporalToFinal($synctracer->id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        Log::info('Sincronización completa finalizada', [
            'trace_id'      => $synctracer->id,
            'total_records' => $synctracer->total_records,
            'processed'     => $processed,
            'batch_size'    => $this->batchSize,
        ]);
    }

    /**
     * Sincronización puntual:
     * lee uno a uno con cursor
     * inserta por lotes de 1000
     * actualiza progreso por lote
     */
    private function processSingleDocument(SisafiSyncTracer $synctracer, array $vinculaciones)
    {
        DB::table('sisafi_seac_temporal')
            ->where('sisafi_sync_tracer_id', $synctracer->id)
            ->delete();

        $query = ClientesSeac::where([
            'tipo_id'        => $synctracer->type_document,
            'identificacion' => $synctracer->document_number,
        ]);

        $synctracer->total_records = (clone $query)->count();
        $synctracer->total_processed = 0;
        $synctracer->update();

        $buffer = [];
        $processed = 0;

        foreach ($query->cursor() as $cliente) {
            $row = $this->normalizeRow($cliente->toArray(), $synctracer->id);

            if (!$this->hasMinimumIdentity($row)) {
                $this->logSkippedRow($row, 'Registro omitido por no tener tipo_id o identificacion');
                continue;
            }

            $buffer[] = $row;

            if (count($buffer) === $this->batchSize) {
                $inserted = $this->insertTemporalBatch($buffer);
                $processed += $inserted;

                $synctracer->total_processed = $processed;
                $synctracer->update();

                $buffer = [];
            }
        }

        if (!empty($buffer)) {
            $inserted = $this->insertTemporalBatch($buffer);
            $processed += $inserted;

            $synctracer->total_processed = $processed;
            $synctracer->update();
        }

        $temporales = DB::table('sisafi_seac_temporal')
            ->where('sisafi_sync_tracer_id', $synctracer->id)
            ->get();

        DB::beginTransaction();

        try {
            foreach ($temporales as $seacTemporal) {
                DB::table('sisafi_seac_personas')
                    ->where([
                        'tipo_id'        => $seacTemporal->tipo_id,
                        'identificacion' => $seacTemporal->identificacion,
                        'id_principal'   => $seacTemporal->id_principal,
                    ])
                    ->delete();

                if (in_array($seacTemporal->vinculacion, $vinculaciones)) {
                    $data = (array) $seacTemporal;
                    unset($data['id']);

                    DB::table('sisafi_seac_personas')->insert($data);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        Log::info('Sincronización puntual finalizada', [
            'trace_id'      => $synctracer->id,
            'document'      => $synctracer->document_number,
            'total_records' => $synctracer->total_records,
            'processed'     => $processed,
            'batch_size'    => $this->batchSize,
        ]);
    }

    /**
     * Normaliza únicamente primer_nombre
     */
    private function normalizeRow(array $row, $syncTracerId)
    {
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);

                if ($value === '?') {
                    $value = null;
                }

                $row[$key] = $value;
            }
        }

        if (!array_key_exists('primer_nombre', $row) || is_null($row['primer_nombre']) || trim((string) $row['primer_nombre']) === '') {
            $row['primer_nombre'] = '';
        }

        $row['sisafi_sync_tracer_id'] = $syncTracerId;

        unset($row['id']);

        return $row;
    }

    /**
     * Solo exige identificación mínima
     */
    private function hasMinimumIdentity(array $row)
    {
        $tipoId = isset($row['tipo_id']) ? trim((string) $row['tipo_id']) : '';
        $identificacion = isset($row['identificacion']) ? trim((string) $row['identificacion']) : '';

        return $tipoId !== '' && $identificacion !== '';
    }

    /**
     * Inserta estrictamente por lote
     */
    private function insertTemporalBatch(array $rows)
    {
        if (empty($rows)) {
            return 0;
        }

        DB::table('sisafi_seac_temporal')->insert($rows);

        return count($rows);
    }

    private function copyTemporalToFinal($syncTracerId)
    {
        $temporalColumns = Schema::getColumnListing('sisafi_seac_temporal');
        $finalColumns    = Schema::getColumnListing('sisafi_seac_personas');

        $columns = array_values(array_intersect($finalColumns, $temporalColumns));

        $columns = array_filter($columns, function ($column) {
            return $column !== 'id';
        });

        if (empty($columns)) {
            throw new \Exception('No se encontraron columnas compatibles entre temporal y final.');
        }

        $columnList = implode(', ', $columns);

        $sql = "
            INSERT INTO sisafi_seac_personas ($columnList)
            SELECT $columnList
            FROM sisafi_seac_temporal
            WHERE sisafi_sync_tracer_id = ?
        ";

        DB::statement($sql, [$syncTracerId]);
    }

    private function logSkippedRow(array $row, $message)
    {
        Log::warning('Registro omitido en sincronización', [
            'message'        => $message,
            'tipo_id'        => $row['tipo_id'] ?? null,
            'identificacion' => $row['identificacion'] ?? null,
            'id_principal'   => $row['id_principal'] ?? null,
        ]);
    }

    private function safeErrorMessage(\Throwable $th)
    {
        $message = $th->getMessage();

        if (mb_strlen($message) > 1000) {
            $message = mb_substr($message, 0, 1000) . '...';
        }

        return $message;
    }
}
