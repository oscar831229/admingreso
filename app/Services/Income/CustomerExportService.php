<?php

namespace App\Services\Income;

use App\Models\Income\IcmCustomerExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Exception;

class CustomerExportService
{
    protected $chunkSize=5000;

    public function generate($exportId)
    {
        $export=IcmCustomerExport::findOrFail($exportId);
        $handle=null;
        $filePath=null;

        try{
            $columns=Schema::getColumnListing('icm_customers');
            if(empty($columns) || !in_array('id',$columns)) throw new RuntimeException('No fue posible obtener la estructura de icm_customers.');

            $total=DB::table('icm_customers')->count();

            $export->update([
                'status'=>IcmCustomerExport::STATUS_PROCESSING,
                'total_rows'=>$total,
                'processed_rows'=>0,
                'progress'=>0,
                'error_message'=>null,
                'started_at'=>now(),
                'finished_at'=>null
            ]);

            $directory='customer-exports/'.date('Y/m');
            Storage::disk('local')->makeDirectory($directory);

            $fileName='clientes_'.date('Ymd_His').'_export_'.$export->id.'.csv';
            $filePath=$directory.'/'.$fileName;
            $fullPath=storage_path('app/'.$filePath);

            $handle=fopen($fullPath,'w');
            if(!$handle) throw new RuntimeException('No fue posible crear el archivo CSV.');

            fwrite($handle,"\xEF\xBB\xBF");
            fputcsv($handle,$columns,';','"');

            $processed=0;
            $chunkSize=$this->chunkSize;

            DB::table('icm_customers')
                ->select($columns)
                ->chunkById($chunkSize,function($rows) use($handle,$columns,$exportId,$total,&$processed){
                    foreach($rows as $row){
                        $values=[];
                        foreach($columns as $column) $values[]=$row->{$column};
                        fputcsv($handle,$values,';','"');
                    }

                    $processed+=count($rows);
                    $progress=$total>0?(int)floor(($processed*100)/$total):100;

                    IcmCustomerExport::where('id',$exportId)->update([
                        'processed_rows'=>$processed,
                        'progress'=>min($progress,99)
                    ]);
                },'id');

            fclose($handle);
            $handle=null;

            $export->update([
                'file_name'=>$fileName,
                'file_path'=>$filePath,
                'status'=>IcmCustomerExport::STATUS_COMPLETED,
                'processed_rows'=>$processed,
                'progress'=>100,
                'finished_at'=>now()
            ]);

            $this->deletePreviousExports($export);
            return true;

        }catch(Exception $e){
            if(is_resource($handle)) fclose($handle);
            if($filePath && Storage::disk('local')->exists($filePath)) Storage::disk('local')->delete($filePath);

            $export->update([
                'status'=>IcmCustomerExport::STATUS_FAILED,
                'error_message'=>$e->getMessage(),
                'finished_at'=>now()
            ]);

            throw $e;
        }
    }

    protected function deletePreviousExports(IcmCustomerExport $current)
    {
        $previous=IcmCustomerExport::where('user_created',$current->user_created)
            ->where('id','<>',$current->id)
            ->where('status',IcmCustomerExport::STATUS_COMPLETED)
            ->get();

        foreach($previous as $export){
            if($export->file_path && Storage::disk('local')->exists($export->file_path)) Storage::disk('local')->delete($export->file_path);
            $export->delete();
        }
    }
}
