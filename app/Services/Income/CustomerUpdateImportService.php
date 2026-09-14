<?php

namespace App\Services\Income;

use App\Models\Income\IcmCustomerUpdateImport;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use DateTime;
use Exception;

class CustomerUpdateImportService
{
    protected $stagingTable = 'icm_customer_update_staging';

    public function validateImport($importId)
    {
        $import = IcmCustomerUpdateImport::findOrFail($importId);

        try {
            $import->update([
                'status' => IcmCustomerUpdateImport::STATUS_VALIDATING,
                'progress' => 0,'total_rows' => 0,'processed_rows' => 0,'valid_rows' => 0,
                'error_rows' => 0,'updated_rows' => 0,'error_message' => null,
                'validation_started_at' => now(),'validated_at' => null
            ]);

            DB::table($this->stagingTable)->where('import_id', $import->id)->delete();

            $path = storage_path('app/' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $import->file_path));
            if (!file_exists($path)) throw new RuntimeException('No se encontró el archivo asociado a la importación.');

            $handle = fopen($path, 'r');
            if (!$handle) throw new RuntimeException('No fue posible abrir el archivo CSV.');

            $fileSize = filesize($path);
            $delimiter = $import->delimiter ?: config('customer_update.delimiter', ';');
            $enclosure = config('customer_update.enclosure', '"');
            $header = fgetcsv($handle, 0, $delimiter, $enclosure);

            if ($header === false) throw new RuntimeException('El archivo se encuentra vacío.');

            $this->validateHeader($header);

            $documentDefinitionIds = $this->getDefinitionIds('identification_document_types');
            $genderDefinitionIds = $this->getDefinitionIds('gender');

            if (empty($documentDefinitionIds)) throw new RuntimeException('No fue posible obtener la parametrización de tipos de documento.');
            if (empty($genderDefinitionIds)) throw new RuntimeException('No fue posible obtener la parametrización de género.');

            $batch = [];
            $rowNumber = 1;
            $totalRows = 0;
            $processedRows = 0;
            $insertChunk = (int) config('customer_update.insert_chunk', 2000);

            while (($row = fgetcsv($handle, 0, $delimiter, $enclosure)) !== false) {
                $rowNumber++;

                if ($this->isEmptyRow($row)) continue;

                $totalRows++;
                $processedRows++;

                $batch[] = $this->prepareRow($import->id, $rowNumber, $row, $documentDefinitionIds, $genderDefinitionIds);

                if (count($batch) >= $insertChunk) {
                    DB::table($this->stagingTable)->insert($batch);
                    $batch = [];
                    $this->updateValidationProgress($import->id, $processedRows, $totalRows, $handle, $fileSize);
                }
            }

            if (!empty($batch)) DB::table($this->stagingTable)->insert($batch);

            fclose($handle);

            $this->markDuplicatedDocuments($import->id);
            $this->associateCustomers($import->id);
            $this->markCustomersNotFound($import->id);

            $validRows = DB::table($this->stagingTable)->where('import_id', $import->id)->where('status', 'READY')->count();
            $errorRows = DB::table($this->stagingTable)->where('import_id', $import->id)->where('status', 'ERROR')->count();
            $totalRows = DB::table($this->stagingTable)->where('import_id', $import->id)->count();

            $import->update([
                'status' => IcmCustomerUpdateImport::STATUS_READY,
                'total_rows' => $totalRows,'processed_rows' => $totalRows,'valid_rows' => $validRows,
                'error_rows' => $errorRows,'progress' => 100,'validated_at' => now()
            ]);

            return true;
        } catch (Exception $e) {
            if (isset($handle) && is_resource($handle)) fclose($handle);

            DB::table($this->stagingTable)->where('import_id', $importId)->delete();

            $import->update([
                'status' => IcmCustomerUpdateImport::STATUS_VALIDATION_FAILED,
                'error_message' => $e->getMessage(),
                'finished_at' => now()
            ]);

            throw $e;
        }
    }

    protected function prepareRow($importId, $rowNumber, array $row, array $documentDefinitionIds, array $genderDefinitionIds)
    {
        $headers = config('customer_update.headers');

        if (count($row) !== count($headers)) {
            return [
                'import_id' => $importId,'row_number' => $rowNumber,
                'document_number' => isset($row[1]) ? $this->limit($this->nullIfEmpty($row[1]), 100) : null,
                'status' => 'ERROR',
                'error_message' => 'Cantidad de columnas inválida. Se esperaban '.count($headers).' y se recibieron '.count($row).'. Posible delimitador sin encapsular entre comillas.'
            ];
        }

        $data = array_combine($headers, $row);
        foreach ($data as $key => $value) $data[$key] = $this->nullIfEmpty($value);

        $errors = [];
        $documentTypeRaw = $data['document_type'] !== null ? strtoupper($data['document_type']) : null;
        $documentNumber = $data['document_number'];
        $genderRaw = $data['gender'] !== null ? strtoupper($data['gender']) : null;

        if ($documentTypeRaw === null) $errors[] = 'Tipo de documento obligatorio.';
        if ($documentNumber === null) $errors[] = 'Número de documento obligatorio.';
        if ($data['first_name'] === null) $errors[] = 'Primer nombre obligatorio.';

        if ($documentNumber !== null && mb_strlen($documentNumber) > 20) $errors[] = 'Número de documento supera 20 caracteres.';

        $this->validateLength($data['first_name'], 150, 'Primer nombre', $errors);
        $this->validateLength($data['second_name'], 150, 'Segundo nombre', $errors);
        $this->validateLength($data['first_surname'], 150, 'Primer apellido', $errors);
        $this->validateLength($data['second_surname'], 150, 'Segundo apellido', $errors);
        $this->validateLength($data['address'], 150, 'Dirección', $errors);
        $this->validateLength($data['email'], 150, 'Correo electrónico', $errors);

        $documentTypeId = null;
        if ($documentTypeRaw !== null) {
            $map = config('customer_update.document_types', []);
            if (!isset($map[$documentTypeRaw])) {
                $errors[] = 'Tipo de documento ['.$documentTypeRaw.'] no homologado.';
            } else {
                $documentTypeId = (int) $map[$documentTypeRaw];
                if (!in_array($documentTypeId, $documentDefinitionIds, true)) {
                    $errors[] = 'La homologación ['.$documentTypeRaw.'] apunta al ID ['.$documentTypeId.'] que no existe en la parametrización.';
                }
            }
        }

        $genderId = null;
        if ($genderRaw !== null) {
            $map = config('customer_update.genders', []);
            if (!isset($map[$genderRaw])) {
                $errors[] = 'Género ['.$genderRaw.'] no homologado.';
            } else {
                $genderId = (int) $map[$genderRaw];
                if (!in_array($genderId, $genderDefinitionIds, true)) {
                    $errors[] = 'La homologación de género ['.$genderRaw.'] apunta al ID ['.$genderId.'] que no existe en la parametrización.';
                }
            }
        }

        $birthdayDate = null;
        if ($data['birthday_date'] !== null) {
            $birthdayDate = $this->validateDate($data['birthday_date']);
            if ($birthdayDate === null) $errors[] = 'Fecha de nacimiento inválida ['.$data['birthday_date'].']. Formato esperado YYYY-MM-DD.';
        }

        return [
            'import_id' => $importId,'row_number' => $rowNumber,'customer_id' => null,
            'document_type_raw' => $this->limit($documentTypeRaw, 100),'document_type_id' => $documentTypeId,
            'document_number' => $this->limit($documentNumber, 100),
            'first_surname' => $this->limit($data['first_surname'], 500),
            'second_surname' => $this->limit($data['second_surname'], 500),
            'first_name' => $this->limit($data['first_name'], 500),
            'second_name' => $this->limit($data['second_name'], 500),
            'birthday_date_raw' => $this->limit($data['birthday_date'], 100),'birthday_date' => $birthdayDate,
            'gender_raw' => $this->limit($genderRaw, 100),'gender_id' => $genderId,
            'address' => $data['address'],'email' => $data['email'],
            'status' => empty($errors) ? 'READY' : 'ERROR',
            'error_message' => empty($errors) ? null : implode(' | ', array_unique($errors))
        ];
    }

    public function applyImport($importId)
    {
        $import = IcmCustomerUpdateImport::findOrFail($importId);

        if (!in_array($import->status, [
            IcmCustomerUpdateImport::STATUS_READY,
            IcmCustomerUpdateImport::STATUS_APPLY_QUEUED,
            IcmCustomerUpdateImport::STATUS_APPLY_FAILED
        ])) throw new RuntimeException('La importación todavía no se encuentra lista para aplicar.');

        try {
            $alreadyUpdated = DB::table($this->stagingTable)->where('import_id', $importId)->where('status', 'UPDATED')->count();
            $pending = DB::table($this->stagingTable)->where('import_id', $importId)->where('status', 'READY')->count();
            $total = $alreadyUpdated + $pending;
            $processed = $alreadyUpdated;

            $import->update([
                'status' => IcmCustomerUpdateImport::STATUS_APPLYING,
                'updated_rows' => $alreadyUpdated,
                'progress' => $total > 0 ? (int) floor(($alreadyUpdated * 100) / $total) : 100,
                'error_message' => null,'apply_started_at' => $import->apply_started_at ?: now()
            ]);

            $chunkSize = (int) config('customer_update.update_chunk', 10000);

            DB::table($this->stagingTable)
                ->select('id')
                ->where('import_id', $importId)
                ->where('status', 'READY')
                ->orderBy('id')
                ->chunkById($chunkSize, function ($rows) use ($import, $importId, $total, &$processed) {
                    if ($rows->isEmpty()) return;

                    $firstId = $rows->first()->id;
                    $lastId = $rows->last()->id;
                    $quantity = $rows->count();

                    DB::transaction(function () use ($import, $importId, $firstId, $lastId) {
                        DB::update("
                            UPDATE icm_customers c
                            INNER JOIN icm_customer_update_staging s ON s.customer_id = c.id
                            SET c.document_type = s.document_type_id,
                                c.first_name = s.first_name,
                                c.second_name = s.second_name,
                                c.first_surname = s.first_surname,
                                c.second_surname = s.second_surname,
                                c.birthday_date = s.birthday_date,
                                c.gender = s.gender_id,
                                c.address = s.address,
                                c.email = s.email,
                                c.user_updated = ?,
                                c.updated_at = NOW()
                            WHERE s.import_id = ?
                              AND s.status = 'READY'
                              AND s.id BETWEEN ? AND ?
                        ", [$import->user_created, $importId, $firstId, $lastId]);

                        DB::table('icm_customer_update_staging')
                            ->where('import_id', $importId)
                            ->where('status', 'READY')
                            ->whereBetween('id', [$firstId, $lastId])
                            ->update(['status' => 'UPDATED']);
                    });

                    $processed += $quantity;
                    $progress = $total > 0 ? (int) floor(($processed * 100) / $total) : 100;

                    IcmCustomerUpdateImport::where('id', $importId)->update([
                        'updated_rows' => $processed,
                        'progress' => min($progress, 99)
                    ]);
                });

            $import->update([
                'status' => IcmCustomerUpdateImport::STATUS_COMPLETED,
                'updated_rows' => $total,
                'progress' => 100,
                'finished_at' => now()
            ]);

            /*
             * Conservar staging de esta importación y eliminar
             * stagings de procesos anteriores ya terminados/cancelados.
             */
            $oldIds = IcmCustomerUpdateImport::where('id', '<>', $importId)
                ->whereIn('status', [
                    IcmCustomerUpdateImport::STATUS_COMPLETED,
                    IcmCustomerUpdateImport::STATUS_CANCELLED,
                    IcmCustomerUpdateImport::STATUS_VALIDATION_FAILED
                ])
                ->pluck('id');

            if ($oldIds->count() > 0) {
                DB::table($this->stagingTable)->whereIn('import_id', $oldIds->all())->delete();
            }

            return true;
        } catch (Exception $e) {
            $import->update([
                'status' => IcmCustomerUpdateImport::STATUS_APPLY_FAILED,
                'error_message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function validateHeader(array $header)
    {
        $header = array_map(function ($value) {
            $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);
            return strtolower(trim($value));
        }, $header);

        $expected = config('customer_update.headers');

        if ($header !== $expected) {
            throw new RuntimeException(
                'La estructura del archivo no corresponde con la estructura esperada. Encabezado requerido: '.implode(';', $expected)
            );
        }
    }

    protected function markDuplicatedDocuments($importId)
    {
        DB::update("
            UPDATE icm_customer_update_staging s
            INNER JOIN (
                SELECT document_number FROM (
                    SELECT document_number
                    FROM icm_customer_update_staging
                    WHERE import_id = ? AND document_number IS NOT NULL
                    GROUP BY document_number
                    HAVING COUNT(*) > 1
                ) x
            ) d ON d.document_number = s.document_number
            SET s.status = 'ERROR',
                s.error_message = CONCAT_WS(' | ', NULLIF(s.error_message,''), 'Número de documento duplicado dentro del archivo.')
            WHERE s.import_id = ?
        ", [$importId, $importId]);
    }

    protected function associateCustomers($importId)
    {
        DB::update("
            UPDATE icm_customer_update_staging s
            INNER JOIN icm_customers c ON c.document_number = s.document_number
            SET s.customer_id = c.id
            WHERE s.import_id = ? AND s.status = 'READY'
        ", [$importId]);
    }

    protected function markCustomersNotFound($importId)
    {
        DB::table($this->stagingTable)
            ->where('import_id', $importId)
            ->where('status', 'READY')
            ->whereNull('customer_id')
            ->update([
                'status' => 'ERROR',
                'error_message' => 'El número de documento no existe en icm_customers.'
            ]);
    }

    protected function getDefinitionIds($definition)
    {
        return collect(getDetailDefinitions($definition))
            ->keys()
            ->map(function ($id) { return (int) $id; })
            ->values()
            ->all();
    }

    protected function nullIfEmpty($value)
    {
        if ($value === null) return null;
        $value = trim($value);
        return $value === '' ? null : $value;
    }

    protected function isEmptyRow(array $row)
    {
        foreach ($row as $value) {
            if ($value !== null && trim($value) !== '') return false;
        }
        return true;
    }

    protected function validateDate($value)
    {
        $date = DateTime::createFromFormat('!Y-m-d', $value);
        $errors = DateTime::getLastErrors();

        if ($date === false) return null;

        if (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) return null;

        return $date->format('Y-m-d') === $value ? $date->format('Y-m-d') : null;
    }

    protected function validateLength($value, $maximum, $field, array &$errors)
    {
        if ($value !== null && mb_strlen($value) > $maximum) {
            $errors[] = $field.' supera la longitud máxima de '.$maximum.' caracteres.';
        }
    }

    protected function limit($value, $maximum)
    {
        return $value === null ? null : mb_substr($value, 0, $maximum);
    }

    protected function updateValidationProgress($importId, $processed, $total, $handle, $fileSize)
    {
        $position = ftell($handle);
        $progress = 0;

        if ($fileSize > 0 && $position !== false) {
            $progress = min((int) floor(($position * 100) / $fileSize), 99);
        }

        IcmCustomerUpdateImport::where('id', $importId)->update([
            'processed_rows' => $processed,
            'total_rows' => $total,
            'progress' => $progress
        ]);
    }
}
