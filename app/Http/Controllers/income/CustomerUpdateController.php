<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Income\IcmCustomerUpdateImport;
use App\Jobs\Income\ValidateCustomerUpdateImport;
use App\Jobs\Income\ApplyCustomerUpdateImport;

class CustomerUpdateController extends Controller
{
    public function index()
    {
        $imports = IcmCustomerUpdateImport::where('user_created', auth()->id())
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $activeImport = IcmCustomerUpdateImport::where('user_created', auth()->id())
            ->whereIn('status', [
                IcmCustomerUpdateImport::STATUS_PENDING,
                IcmCustomerUpdateImport::STATUS_VALIDATING,
                IcmCustomerUpdateImport::STATUS_READY,
                IcmCustomerUpdateImport::STATUS_APPLY_QUEUED,
                IcmCustomerUpdateImport::STATUS_APPLYING
            ])
            ->orderBy('id', 'desc')
            ->first();

        return view('income.customers.update', compact('imports', 'activeImport'));
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), ['file' => 'required|file|max:512000']);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $active = IcmCustomerUpdateImport::where('user_created', auth()->id())
            ->whereIn('status', [
                IcmCustomerUpdateImport::STATUS_PENDING,
                IcmCustomerUpdateImport::STATUS_VALIDATING,
                IcmCustomerUpdateImport::STATUS_READY,
                IcmCustomerUpdateImport::STATUS_APPLY_QUEUED,
                IcmCustomerUpdateImport::STATUS_APPLYING
            ])->exists();

        if ($active) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una importación activa. Finalice o cancele el proceso actual antes de cargar otro archivo.'
            ]);
        }

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension !== 'csv') {
            return response()->json(['success' => false, 'message' => 'El archivo debe estar en formato CSV.']);
        }

        $originalName = $file->getClientOriginalName();
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $directory = 'customer-updates/history/'.date('Y/m');
        $filename = date('Ymd_His').'_'.Str::uuid().'_'.$safeName.'.csv';

        $path = $file->storeAs($directory, $filename, 'local');

        if (!$path) {
            return response()->json(['success' => false, 'message' => 'No fue posible almacenar el archivo.']);
        }

        try {
            $import = IcmCustomerUpdateImport::create([
                'original_name' => $originalName,
                'file_path' => $path,
                'delimiter' => config('customer_update.delimiter', ';'),
                'status' => IcmCustomerUpdateImport::STATUS_PENDING,
                'user_created' => auth()->id()
            ]);

            ValidateCustomerUpdateImport::dispatch($import->id);

            return response()->json([
                'success' => true,
                'message' => 'Archivo cargado correctamente. Se inició la validación.',
                'import_id' => $import->id
            ]);
        } catch (\Exception $e) {
            Storage::disk('local')->delete($path);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function status($id)
    {
        $import = IcmCustomerUpdateImport::where('id', $id)
            ->where('user_created', auth()->id())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $import->id,
                'status' => $import->status,
                'total_rows' => $import->total_rows,
                'processed_rows' => $import->processed_rows,
                'valid_rows' => $import->valid_rows,
                'error_rows' => $import->error_rows,
                'updated_rows' => $import->updated_rows,
                'progress' => $import->progress,
                'error_message' => $import->error_message
            ]
        ]);
    }

    public function apply($id)
    {
        $import = IcmCustomerUpdateImport::where('id', $id)
            ->where('user_created', auth()->id())
            ->firstOrFail();

        if ($import->status !== IcmCustomerUpdateImport::STATUS_READY) {
            return response()->json(['success' => false, 'message' => 'La importación todavía no está lista para actualizar.']);
        }

        if ($import->valid_rows <= 0) {
            return response()->json(['success' => false, 'message' => 'La importación no contiene registros válidos.']);
        }

        $updated = IcmCustomerUpdateImport::where('id', $id)
            ->where('status', IcmCustomerUpdateImport::STATUS_READY)
            ->update([
                'status' => IcmCustomerUpdateImport::STATUS_APPLY_QUEUED,
                'progress' => 0
            ]);

        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'El proceso cambió de estado y no puede iniciarse nuevamente.']);
        }

        try {
            ApplyCustomerUpdateImport::dispatch($id);
        } catch (\Exception $e) {
            IcmCustomerUpdateImport::where('id', $id)
                ->where('status', IcmCustomerUpdateImport::STATUS_APPLY_QUEUED)
                ->update(['status' => IcmCustomerUpdateImport::STATUS_READY]);

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'message' => 'Se inició la actualización de los clientes.']);
    }

    public function cancel($id)
    {
        $import = IcmCustomerUpdateImport::where('id', $id)
            ->where('user_created', auth()->id())
            ->firstOrFail();

        if ($import->status !== IcmCustomerUpdateImport::STATUS_READY) {
            return response()->json([
                'success' => false,
                'message' => 'Solamente se puede cancelar una importación que esté lista para actualizar.'
            ]);
        }

        DB::beginTransaction();

        try {
            $updated = IcmCustomerUpdateImport::where('id', $id)
                ->where('status', IcmCustomerUpdateImport::STATUS_READY)
                ->update([
                    'status' => IcmCustomerUpdateImport::STATUS_CANCELLED,
                    'progress' => 100,
                    'finished_at' => now()
                ]);

            if (!$updated) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'El proceso cambió de estado y ya no puede cancelarse.']);
            }

            DB::table('icm_customer_update_staging')->where('import_id', $id)->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'El proceso fue cancelado correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function errors($id)
    {
        $import = IcmCustomerUpdateImport::where('id', $id)
            ->where('user_created', auth()->id())
            ->firstOrFail();

        if ($import->status !== IcmCustomerUpdateImport::STATUS_READY || $import->error_rows <= 0) {
            abort(404, 'El reporte de errores no se encuentra disponible.');
        }

        $filename = 'errores_actualizacion_clientes_'.$id.'.csv';

        return response()->streamDownload(function () use ($id) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'fila','document_type','document_number','first_surname','second_surname',
                'first_name','second_name','birthday_date','gender','address','email','error'
            ], ';');

            DB::table('icm_customer_update_staging')
                ->where('import_id', $id)
                ->where('status', 'ERROR')
                ->orderBy('id')
                ->chunkById(2000, function ($rows) use ($output) {
                    foreach ($rows as $row) {
                        fputcsv($output, [
                            $row->row_number,$row->document_type_raw,$row->document_number,
                            $row->first_surname,$row->second_surname,$row->first_name,$row->second_name,
                            $row->birthday_date_raw,$row->gender_raw,$row->address,$row->email,$row->error_message
                        ], ';');
                    }
                });

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function download($id)
    {
        $import = IcmCustomerUpdateImport::where('id', $id)
            ->where('user_created', auth()->id())
            ->firstOrFail();

        if (!$import->file_path || !Storage::disk('local')->exists($import->file_path)) {
            abort(404, 'El archivo original de esta importación no se encuentra disponible.');
        }

        return Storage::disk('local')->download(
            $import->file_path,
            'import_'.$import->id.'_'.$import->original_name
        );
    }
}
