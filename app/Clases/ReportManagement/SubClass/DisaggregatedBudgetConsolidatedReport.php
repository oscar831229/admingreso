<?php 
namespace App\Clases\ReportManagement\SubClass;

use App\Models\Officials\Person;
use App\Models\Officials\SurveyForm;
use App\Models\Officials\SurveyFormDetail;
use App\Models\Officials\Company;
use App\Models\ElectronicDocument\ElectronicDocumentTraceability;


class DisaggregatedBudgetConsolidatedReport
{
	public static $filename = 'desagregado_consolidado_rubodanefuente.xlsx';

    public static $columns = [
        'ENLACE',
        'RUBRO',
        'NOMBRE RUBRO',
        'COD. DANE',
        'DANE',
        'VIGENCIA',
        'COMPROMETIDO',
        'OBLIGADO',
        'PAGADO'
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

        $year = $this->request->input('year');
        $date_from = $this->request->input('date_from');
        $date_end = $this->request->input('date_end');
        $state_document = $this->request->input('state_document');

        # AJUSTAR VALORES
        $date_from = is_null($date_from) ? '' : $date_from;
        $date_end = is_null($date_end) ? '' : $date_end;
        $state_document = is_null($state_document) ? '' : ($state_document == 'C' ? 'DESAGREGADO' : 'PENDIENTE' );

        $querySQL = "DECLARE 
            @year VARCHAR(6) = ?,
            @date_to DATE = ?,
            @date_end DATE = ?,
            @state VARCHAR(25) = ?

            ;WITH commitment AS (
                SELECT
                    c.id AS commitment_id,
                    c.code AS commitment_code,
                    bv.year AS budgetary,
                    CONCAT(tp.Nit, ' ', tp.Name) AS third,
                    CASE 
                        WHEN c.DocumentSource = 1 THEN 'Otro' 
                        WHEN c.DocumentSource = 2 THEN 'Orde de trabajo' 
                        WHEN c.DocumentSource = 3 THEN 'Contrato' 
                        ELSE 'Sin definir' 
                    END AS DocumentSource,
                    c.Document,
                    c.DocumentDate,
                    cd.id AS commitment_detail_id,
                    cd.TotalCommitment AS commitment_detail_value,
                    ct.code AS category_code,
                    ct.name AS category_name,
                    fs.name AS financial_source,
                    ISNULL(ce.disaggregated, 'S') AS disaggregated,
                    ISNULL(cdv.validity, 1) AS validity
                FROM vie19.budget.commitment AS c
                INNER JOIN vie19.budget.commitmentdetail AS cd ON cd.commitmentid = c.id
                INNER JOIN vie19.budget.BudgetaryValidity AS bv ON bv.id = c.BudgetaryValidityId
                INNER JOIN vie19.Common.ThirdParty AS tp ON tp.id = c.ThirdPartyId
                INNER JOIN vie19.budget.category AS ct ON ct.id = cd.categoryid
                INNER JOIN vie19.budget.financialsource fs ON fs.id = ct.financialsourceid
                LEFT JOIN budget.category_extensions AS ce ON ce.category_id = ct.id AND ce.budgetary_validity_id = bv.id
                LEFT JOIN budget.commitment_detail_validities AS cdv ON cdv.commitment_id = cd.commitmentid AND cdv.commitment_detail_id = cd.id
                WHERE bv.year = @year
                    AND (
                        @date_to = '1900-01-01' 
                        OR convert(DATE, c.DocumentDate, 112) BETWEEN @date_to AND @date_end
                    )
            ), obligation_desagregado as (
                SELECT 
                    odd.* 
                FROM budget.obligation_extensions AS  oe
                INNER JOIN budget.obligation_detail_danes AS odd ON odd.obligation_extension_id = oe.id
                INNER JOIN vie19.budget.obligation AS o ON o.Id = oe.obligation_id
                INNER JOIN vie19.budget.BudgetaryValidity AS bv ON bv.id = o.BudgetaryValidityId
                WHERE bv.year = @year
                    AND (
                        @date_to = '1900-01-01' 
                        OR convert(DATE, o.DocumentDate, 112) BETWEEN @date_to AND @date_end
                    )
            ), paymentorder_desagregado AS (
                SELECT 
                    podd.* 
                FROM budget.paymentorder_extensions AS  poe
                INNER JOIN budget.paymentorder_detail_danes AS podd ON podd.paymentorder_extension_id = poe.id
                INNER JOIN vie19.budget.paymentorder AS po ON po.Id = poe.paymentorder_id
                INNER JOIN vie19.budget.BudgetaryValidity AS bv ON bv.id = po.BudgetaryValidityId
                WHERE bv.year = @year
                    AND (
                        @date_to = '1900-01-01' 
                        OR convert(DATE, po.DocumentDate, 112) BETWEEN @date_to AND @date_end
                    )
            ), desagregado AS (
                SELECT
                    cd.*,
                    cdd.dane_code_id,
                    dc.code AS dane_codes,
                    dc.title AS dane_code_name,
                    CASE 
                        WHEN cdd.dane_code_id IS NULL THEN cd.commitment_detail_value 
                        ELSE cdd.current_value 
                    END AS commitment_detail_dane_value,
                    CASE 
                        WHEN cdd.dane_code_id IS NULL THEN (
                            SELECT 
								SUM(aod.TotalObligation) 
							FROM vie19.budget.obligationdetail AS aod 
							INNER JOIN vie19.budget.obligation AS ao ON ao.Id = aod.ObligationId
							WHERE aod.CommitmentDetailId = cd.commitment_detail_id
								AND (
			                        @date_to = '1900-01-01' 
			                        OR convert(DATE, ao.DocumentDate, 112) BETWEEN @date_to AND @date_end
			                    )
                        )
                        ELSE odd.current_value
                    END AS obligation_detail_value,
                    CASE 
                        WHEN cdd.dane_code_id IS NULL THEN (
                            SELECT 
								SUM(TotalPaymentOrder) 
							FROM vie19.budget.obligationdetail od
							INNER JOIN vie19.budget.paymentorderdetail pod ON pod.obligationdetailid = od.id
							INNER JOIN vie19.budget.paymentorder AS apo ON apo.Id = pod.paymentorderId
							WHERE od.CommitmentDetailId = cd.commitment_detail_id
								AND (
			                        @date_to = '1900-01-01' 
			                        OR convert(DATE, apo.DocumentDate, 112) BETWEEN @date_to AND @date_end
			                    )
                        )
                        ELSE podd.current_value
                    END AS paymentorder_detail_value
                FROM commitment AS cd
                INNER JOIN budget.commitment_extensions AS ce ON ce.commitment_id = cd.commitment_id
                LEFT JOIN budget.commitment_detail_danes AS cdd ON cdd.commitment_extension_id = ce.id AND cdd.commitment_detail_id = cd.commitment_detail_id
                LEFT JOIN obligation_desagregado AS odd ON odd.commitment_detail_id = cdd.commitment_detail_id AND odd.dane_code_id = cdd.dane_code_id
                LEFT JOIN paymentorder_desagregado AS podd ON podd.obligation_detail_id = odd.obligation_detail_Id AND podd.dane_code_id = odd.dane_code_id
                LEFT JOIN budget.dane_codes AS dc ON dc.id = cdd.dane_code_id
            ), desagregado_consolidado_dane AS (
                SELECT
                    commitment_id, 
                    commitment_code, 
                    budgetary, third, 
                    DocumentSource, 
                    Document, 
                    DocumentDate, 
                    commitment_detail_id, 
                    category_code, 
                    category_name,
                    financial_source,
                    disaggregated,
                    dane_code_id,
                    dane_codes,
                    dane_code_name,
                    commitment_detail_dane_value AS commitment_detail_dane_value,
                    sum(obligation_detail_value) AS obligation_detail_value,
                    sum(paymentorder_detail_value) AS paymentorder_detail_value,
                    CASE
                        WHEN validity = 1 THEN 'Vigencia actual'
                        ELSE 'Vigencia anterior' 
                    END AS validity
                FROM desagregado
                GROUP BY commitment_id, 
                    commitment_code, 
                    budgetary, 
                    third, 
                    DocumentSource, 
                    Document, 
                    DocumentDate, 
                    commitment_detail_id, 
                    category_code, 
                    category_name,
                    financial_source,
                    disaggregated,
                    dane_code_id,
                    dane_codes,
                    dane_code_name,
                    commitment_detail_dane_value,
                    validity		
            ), desaggregate_consolidate_rubrodane AS (
            	SELECT
                	CONCAT(category_code,'-',dane_codes) AS link, 
	            	category_code, 
	            	category_name, 
	            	dane_codes, 
	            	dane_code_name, 
	            	validity, 
	            	SUM(commitment_detail_dane_value) AS commitment_detail_dane_value, 
	            	SUM(obligation_detail_value) AS obligation_detail_value, 
	            	SUM(paymentorder_detail_value) AS paymentorder_detail_value    
	            FROM desagregado_consolidado_dane 
	            GROUP BY category_code, category_name, dane_codes, dane_code_name, validity
            )
            
            SELECT * FROM desaggregate_consolidate_rubrodane ORDER BY validity, category_code, dane_codes";

        $commitments = \DB::connection('SIGH')->select($querySQL, [$year, $date_from, $date_end, $state_document]);

        return $commitments;     
		
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
        $collaborator_type = getDetailDefinitions('TIPOCOLAB');
        $bonding_type = getDetailDefinitions('TIPOVINCU');
        $companies = auth()->user()->companies->pluck('name', 'id');
        return view('BudgetManagement.report-management.reports.'.$code, compact('collaborator_type', 'bonding_type', 'companies','code'));
    }

    public function getEstructView(){
        return 'BudgetManagement.report-management.struct.disaggregated-budget-consolidated-report';
    }
	
	
}