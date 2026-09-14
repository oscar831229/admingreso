<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Income\CustomerUpdateImportService;
use App\Models\Income\IcmCustomerUpdateImport;

class ProcessCustomerUpdateImport extends Command
{
    protected $signature = 'customer:update-import {import_id} {action=validate}';
    protected $description = 'Valida o aplica una importación masiva de clientes';

    public function handle(CustomerUpdateImportService $service)
    {
        $id = $this->argument('import_id');
        $action = strtolower($this->argument('action'));

        if (!IcmCustomerUpdateImport::find($id)) {
            $this->error('No existe la importación '.$id);
            return 1;
        }

        if (!in_array($action, ['validate','apply','all'])) {
            $this->error('Use validate, apply o all.');
            return 1;
        }

        if ($action === 'validate') {
            $service->validateImport($id);
            $this->info('Validación terminada.');
            return 0;
        }

        if ($action === 'apply') {
            $service->applyImport($id);
            $this->info('Actualización terminada.');
            return 0;
        }

        $service->validateImport($id);
        $import = IcmCustomerUpdateImport::find($id);

        $this->info('Válidos: '.$import->valid_rows);
        $this->info('Errores: '.$import->error_rows);

        if ($import->valid_rows > 0) $service->applyImport($id);

        $this->info('Proceso terminado.');
        return 0;
    }
}
