<?php 
namespace App\Clases\DataTable;

class tableServer 
{

    private $filtro;

    # Modelo y metodos
    private $model;
    private $method_consulta;
    private $method_cantidad;

    public function __construct($param){

        $request = request();

        # Identificar column search
        $columnsearch = [];
        foreach ($request->input('columns') as $key => $column) {
            if(!empty($column['search']['value'])){
                $columnsearch[] = [
                    'column' => $key,
                    'value' => $column['search']['value']
                ];
            }
        }

        $this->filter = array(
            'start' => !empty($request->input('start')) ? $request->input('start') : 0,
            'length' => !empty($request->input('length')) ? $request->input('length') : 10,
            'column' => !empty($request->input('order')[0]['column']) ? $request->input('order')[0]['column'] + 1  : 1,
            'dir' => !empty($request->input('order')[0]['dir']) ? $request->input('order')[0]['dir'] : 'DESC',
            'search' => !empty($request->input('search')['value']) ? $request->input('search')['value'] : '',
            'extradata' => $param['extradata']
        );

        #Asignar modelo y metodos
        $this->model = $param['model'];
        $this->method_consulta = $param['method_consulta'];
        $this->method_cantidad = $param['method_cantidad'];

    }

    public function consultar(){
        
        $this->data = $this->model->{$this->method_consulta}($this->filter);

        $this->alistarDatos();

    }
    
    public function alistarDatos(){
        
        $dato_aux = $this->data;
        $this->data = array();

        foreach ($dato_aux as $index => $entidad) {
            // Obtener solo los valores del arreglo.
            $this->data[] = is_a($entidad, 'stdClass') ? array_values(collect($entidad)->toArray()) : array_values($entidad->toArray());
        }  	
        
    }

    public function conRegistrosFiltrados(){

        $totales = $this->model->{$this->method_cantidad}($this->filter);

        return $totales;

    }

    public function getFulldata(){
        $response = [];
        if(method_exists($this->model, 'fulldata')){
            $response = $this->model->fulldata($this->filter);
        }
        return $response;
    }

    public function getDatos(){
        
        //Consultar datos
        $this->consultar();

        //Contabilizar los registros filtrados
        $count = $this->conRegistrosFiltrados();

        $fulldata = $this->getFulldata();

        $this->filter['column']=$this->filter['column']-1;

        $datos = array(
            'data' => $this->data,
            'draw' => 0,
            'recordsFiltered' => $count['canfiltered'] ,
            'recordsTotal' => $count['cantotal'],
            'request' => $this->filter,
            'fulldata' => $fulldata
        );	

        return $datos;

    }
    
}