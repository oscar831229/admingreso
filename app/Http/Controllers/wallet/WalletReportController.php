<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Clases\ReportManagement\MaestroReport;
use App\Exports\reportManagement\DriverReport;

use App\Models\Common\ReportManagement;

class WalletReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $module_name = 'Reportes módulo billetera electrónica';
        $reports = [];
        foreach (ReportManagement::where(['state'=>'A', 'module'=>'electronic-wallet-module'])->get() as $key => $report) {
            if(auth()->user()->can($report->code)){
                $reports[] = $report;
            }
        }

        return view('wallet.wallet-reports.index', compact('module_name','reports'));
    }

    public function show($code)
    {

        # OBTENER CLASE PRINCIPAL
        $request = request();
        $request->merge(array( 'report' => $code) );

        // RETURN VIEW FORM REPORT
        $classreport = new MaestroReport($request);
        return $classreport->view();
       
    }

    public function store(Request $request)
    {
        # OBTENER CLASE PRINCIPAL
        $classreport = new MaestroReport($request);

        # GENERAR REPORTE EXCEL
        $report = new DriverReport;
        $report->setData($classreport->getData());
        $report->setColumns($classreport->getColumns());
        $report->setReport($classreport->getEstructView());

        return \Excel::download($report, $classreport->getFileName());

    }
    
}
