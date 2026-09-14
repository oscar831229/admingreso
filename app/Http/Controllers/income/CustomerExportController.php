<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use App\Models\Income\IcmCustomerExport;
use App\Jobs\Income\GenerateCustomerExport;
use Illuminate\Support\Facades\Storage;

class CustomerExportController extends Controller
{
    public function start()
    {
        $active=IcmCustomerExport::where('user_created',auth()->id())
            ->whereIn('status',[
                IcmCustomerExport::STATUS_PENDING,
                IcmCustomerExport::STATUS_PROCESSING
            ])
            ->orderBy('id','desc')
            ->first();

        if($active){
            return response()->json([
                'success'=>true,
                'existing'=>true,
                'export_id'=>$active->id,
                'message'=>'Ya existe una exportación en proceso.'
            ]);
        }

        $export=IcmCustomerExport::create([
            'status'=>IcmCustomerExport::STATUS_PENDING,
            'user_created'=>auth()->id()
        ]);

        GenerateCustomerExport::dispatch($export->id);

        return response()->json([
            'success'=>true,
            'existing'=>false,
            'export_id'=>$export->id,
            'message'=>'Se inició la exportación de clientes.'
        ]);
    }

    public function latest()
    {
        $export=IcmCustomerExport::where('user_created',auth()->id())
            ->orderBy('id','desc')
            ->first();

        if(!$export) return response()->json(['success'=>true,'data'=>null]);

        return response()->json([
            'success'=>true,
            'data'=>$this->data($export)
        ]);
    }

    public function status($id)
    {
        $export=IcmCustomerExport::where('id',$id)
            ->where('user_created',auth()->id())
            ->firstOrFail();

        return response()->json([
            'success'=>true,
            'data'=>$this->data($export)
        ]);
    }

    public function download($id)
    {
        $export=IcmCustomerExport::where('id',$id)
            ->where('user_created',auth()->id())
            ->firstOrFail();

        if($export->status!==IcmCustomerExport::STATUS_COMPLETED){
            abort(404,'La exportación todavía no se encuentra disponible.');
        }

        if(!$export->file_path || !Storage::disk('local')->exists($export->file_path)){
            abort(404,'El archivo de exportación no se encuentra disponible.');
        }

        return Storage::disk('local')->download(
            $export->file_path,
            $export->file_name
        );
    }

    protected function data($export)
    {
        return [
            'id'=>$export->id,
            'status'=>$export->status,
            'file_name'=>$export->file_name,
            'total_rows'=>$export->total_rows,
            'processed_rows'=>$export->processed_rows,
            'progress'=>$export->progress,
            'error_message'=>$export->error_message
        ];
    }
}
