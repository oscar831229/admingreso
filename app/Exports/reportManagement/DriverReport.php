<?php

namespace App\Exports\reportManagement;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DriverReport implements FromView, ShouldAutoSize
{
    private $data;

    private $request;
    
    public function setData($data){
        $this->data = $data;
    }

    public function setRequest($request){
        $this->request = $request;
    }

    public function setColumns($data){
        $this->columns = $data;
    }
    
    public function setFiltro($filtros){
        $this->filtros = $filtros;
    }

    public function setReport($struct_view){
        $this->struct_view = $struct_view;
    }

    public function view(): View
    {
        // 'Officials.report-management.struct.officials-report'
        return view($this->struct_view, [
            'data' => $this->data,
            'columns' => $this->columns,
            'request' => $this->request
        ]);
    }

}
