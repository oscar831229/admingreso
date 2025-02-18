<?php
namespace App\Clases\ReportManagement\SubClass;
use App\Models\Wallet\MovementType;
use App\Models\Wallet\Store;

class CoverageReport
{
	public static $filename = 'liquidaciones_entradas.xlsx';

    public static $columns = [
        'MDEPER_COT_TIPOID',
        'MDEPER_COT_IDENTIF',
        'MDEPER_BEN_TIPOID',
        'MDEPER_BEN_IDENTIF',
        'MDEPER_PRIAPE',
        'MDEPER_SEGAPE',
        'MDEPER_PRINOM',
        'MDEPER_SEGNOM',
        'MDEPER_RAZSOC',
        'MDEPER_NACIMIENTO',
        'MDEPER_GENERO',
        'MDEPER_DIRECCION_RES',
        'MDEPER_CODDPTO_RES',
        'MDEPER_CODMUN_RES',
        'MDEPER_BARRIO_RES',
        'MDEPER_CELULAR',
        'MDEPER_TEL_FIJO_RES',
        'MDEPER_HABEAS_DATA',
        'MDEPER_TIPO_PERSONA',
        'MDEPER_CORREO',
        'MDECOB_PRODUCTO_SEAC',
        'MDECOB_PRODUCTO_ORIGEN',
        'MDECOB_INFRAESTRUCTURA',
        'MDECOB_FECHA_SERVICIO',
        'MDECOB_NITEMP',
        'MDECOB_ROL_CLIENTE',
        'MDECOB_VINCULACION',
        'MDECOB_SUBVIN',
        'MDECOB_RELACION',
        'MDECOB_CATEGORIA',
        'MDECOB_VALOR_VENTA',
        'MDECOB_TIPO_SUB',
        'MDECOB_SUBSIDIO',
        'MDECOB_USOS',
        'MDECOB_PARTICIPANTES',
        'MDECOB_POLITICA',
        'MDECOB_CAJA',
        'MDECOB_SISTEMAFUENTE',
        'MDEPRO_PROCESO',
        'MDECOB_TARIFA_PROMO',
        'MDECOB_FOLIO',
        'MDECOB_FACTURA',
        'MDECOB_CATEGORIA_SSF'
    ];

	public static $data;

    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }

	public function getData(){

        $statement = \DB::table("icm_coverage_details")->selectRaw("
            MDEPER_COT_TIPOID,
            MDEPER_COT_IDENTIF,
            MDEPER_BEN_TIPOID,
            MDEPER_BEN_IDENTIF,
            MDEPER_PRIAPE,
            MDEPER_SEGAPE,
            MDEPER_PRINOM,
            MDEPER_SEGNOM,
            MDEPER_RAZSOC,
            MDEPER_NACIMIENTO,
            MDEPER_GENERO,
            MDEPER_DIRECCION_RES,
            MDEPER_CODDPTO_RES,
            MDEPER_CODMUN_RES,
            MDEPER_BARRIO_RES,
            MDEPER_CELULAR,
            MDEPER_TEL_FIJO_RES,
            MDEPER_HABEAS_DATA,
            MDEPER_TIPO_PERSONA,
            MDEPER_CORREO,
            MDECOB_PRODUCTO_SEAC,
            MDECOB_PRODUCTO_ORIGEN,
            MDECOB_INFRAESTRUCTURA,
            MDECOB_FECHA_SERVICIO,
            MDECOB_NITEMP,
            MDECOB_ROL_CLIENTE,
            MDECOB_VINCULACION,
            MDECOB_SUBVIN,
            MDECOB_RELACION,
            MDECOB_CATEGORIA,
            MDECOB_VALOR_VENTA,
            MDECOB_TIPO_SUB,
            MDECOB_SUBSIDIO,
            MDECOB_USOS,
            MDECOB_PARTICIPANTES,
            MDECOB_POLITICA,
            MDECOB_CAJA,
            MDECOB_SISTEMAFUENTE,
            MDEPRO_PROCESO,
            MDECOB_TARIFA_PROMO,
            MDECOB_FOLIO,
            MDECOB_FACTURA,
            MDECOB_CATEGORIA_SSF
        ")
        ->orderBy('MDEPER_COT_IDENTIF', 'ASC')
        ->orderBy('MDEPER_BEN_IDENTIF', 'ASC');


        if($this->request->has('date_from') && !empty($this->request->date_from)){
            $statement->whereDate('MDECOB_FECHA_SERVICIO','>=', $this->request->date_from);
        }

        if($this->request->has('date_to') && !empty($this->request->date_to)){
            $statement->whereDate('MDECOB_FECHA_SERVICIO','<=', $this->request->date_to);
        }

        return $statement->get();

	}

    /** Retorna nombres de columnas  */
    public function getColumns(){
        return self::$columns;
    }

    /** Retorna el nombre del archivo */
    public function getFileName(){
        return self::$filename;
    }

    public function view(){

        $code = $this->request->input('report');
        return view('income.income-reports.reports.'.$code, compact('code'));
    }

    public function getEstructView(){
        return 'income.income-reports.struct.coverage-report';
    }


}
