<?php 
namespace App\Clases\ReportManagement\SubClass;

use App\Models\Officials\Person;
use App\Models\Officials\SurveyForm;
use App\Models\Officials\SurveyFormDetail;
use App\Models\Officials\Company;
use App\Models\ElectronicDocument\ElectronicDocumentTraceability;


class ReportConsolidatedBudgetClosing
{
	public static $filename = 'desagregado_cierre_cuipo.xlsx';

    public static $columns = [
        'RUBRO',
        'NOMBRE RUBRO',
        'CÓDIGO FUENTE DE FINANCIAMIENTO',
        'FUENTE DE FINANCIAMIENTO',
        'VIGENCIA',
        'VIGENCIA CODE',
        'CÓDIGO DANE',
        'DESC DANE',
        'VALOR COMPROMETIDO',
        'VALOR OBLIGADO',
        'VALOR PAGADO' 
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

        $year = $this->request->input('year');
        $closing_date = $this->request->input('closing_date');

        $querySQL = "DECLARE @year VARCHAR(6) = ?,
                            @closing_date DATE = ?
        
            --- INICIO COMPROMISOS VIE19 A CORTE
            ;WITH commitment_before_modification AS (
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
                    cd.InitialValue AS commitment_detail_initial_value,
                    ct.code AS category_code,
                    ct.name AS category_name,
                    fs.code AS financial_source_code,
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
                    AND CONVERT(DATE, c.DocumentDate, 112) <= 	@closing_date
                    AND c.status = 2
            ), commitment_modification AS (
                SELECT
                    cmd.CommitmentDetailId,
                    CASE
                        WHEN cmd.Nature= 1 THEN -cmd.Value
                        ELSE cmd.value
                    END AS value_modification
                FROM vie19.budget.commitmentmodification AS cm
                INNER JOIN vie19.budget.commitmentmodificationdetail AS cmd ON cmd.commitmentmodificationid = cm.Id
                INNER JOIN commitment_before_modification AS cbm ON cbm.commitment_detail_id = cmd.CommitmentDetailId
                WHERE CONVERT(DATE, cm.DocumentDate, 112) <= @closing_date
                    AND cm.status = 2
            ), commitment_modification_consolidate AS (
                SELECT CommitmentDetailId, sum(value_modification) AS value_modification FROM commitment_modification GROUP BY CommitmentDetailId
            ), commitment AS (
            SELECT
                cbm.commitment_id,
                cbm.commitment_code,
                cbm.budgetary,
                cbm.third,
                cbm.DocumentSource,
                cbm.Document,
                cbm.DocumentDate,
                cbm.commitment_detail_id,
                (cbm.commitment_detail_initial_value + ISNULL(cmc.value_modification, 0)) AS commitment_detail_value,
                cbm.category_code,
                cbm.category_name,
                cbm.financial_source_code,
                cbm.financial_source,
                cbm.disaggregated,
                cbm.validity
            FROM commitment_before_modification AS cbm
            LEFT JOIN commitment_modification_consolidate AS cmc ON cmc.CommitmentDetailId = cbm.commitment_detail_id
            --- FIN COMPROMISOS VIE19 A CORTE
            --- INICIO OBLIGACION VIE19 A CORTE
            ),obligation_before_modification AS (
                SELECT
                    o.id AS obligation_id,
                    od.id AS obligation_detail_id,
                    od.InitialValue AS obligation_detail_initial_value,
                    od.commitmentdetailid
                FROM vie19.budget.obligation AS o
                INNER JOIN vie19.budget.obligationdetail AS od ON od.obligationid = o.id
                INNER JOIN vie19.budget.BudgetaryValidity AS bv ON bv.id = o.BudgetaryValidityId
                WHERE bv.year = @year
                    AND CONVERT(DATE, o.DocumentDate, 112) <= 	@closing_date
                    AND o.status = 2
            ), obligation_modification AS (
                SELECT
                    omd.ObligationDetailId,
                    CASE
                        WHEN omd.Nature= 1 THEN -omd.Value
                        ELSE omd.value
                    END AS value_modification
                FROM vie19.budget.obligationmodification AS om
                INNER JOIN vie19.budget.obligationmodificationdetail AS omd ON omd.obligationmodificationid = om.Id
                INNER JOIN obligation_before_modification AS obm ON obm.obligation_detail_id = omd.obligationDetailId
                WHERE CONVERT(DATE, om.DocumentDate, 112) <= @closing_date
                    AND om.status = 2
            ), obligation_modification_consolidate AS (
                SELECT ObligationDetailId, sum(value_modification) AS value_modification FROM obligation_modification GROUP BY ObligationDetailId
            ), obligation AS (
            SELECT
                obm.commitmentdetailid,
                (obm.obligation_detail_initial_value + ISNULL(omc.value_modification, 0)) AS obligation_detail_value
            FROM obligation_before_modification AS obm
            LEFT JOIN obligation_modification_consolidate AS omc ON omc.ObligationDetailId = obm.obligation_detail_id
            ), obligation_consolidado as (
                SELECT commitmentdetailid, SUM(obligation_detail_value) AS obligation_detail_value FROM obligation GROUP BY commitmentdetailid
            ),
            --- FIN OBLIGACIONES VIE19 A CORTE
            --- INICIO DE ORDENES DE COMPRA VIE19 A CORTE
            paymentorder_before_modification AS (
                SELECT
                    po.id AS paymentorder_id,
                    pod.id AS paymentorder_detail_id,
                    pod.InitialValue AS paymentorder_detail_initial_value,
                    od.commitmentdetailid
                FROM vie19.budget.paymentorder AS po
                INNER JOIN vie19.budget.paymentorderdetail AS pod ON pod.paymentorderid = po.id
                INNER JOIN vie19.budget.obligationdetail AS od on od.id = pod.obligationDetailId
                INNER JOIN vie19.budget.BudgetaryValidity AS bv ON bv.id = po.BudgetaryValidityId
                WHERE bv.year = @year
                    AND CONVERT(DATE, po.DocumentDate, 112) <= 	@closing_date
                    AND po.status = 2
            ), paymentorder_modification AS (
                SELECT
                    rrd.paymentorderdetailId,
                    -rrd.Value AS value_modification
                FROM vie19.budget.ReimbursementResource AS rr
                INNER JOIN vie19.budget.ReimbursementResourceDetaill AS rrd ON rrd.ReimbursementResourceId = rr.Id
                INNER JOIN paymentorder_before_modification AS pobm ON pobm.paymentorder_detail_id = rrd.paymentorderdetailId
                WHERE CONVERT(DATE, rr.DocumentDate, 112) <= @closing_date
                    AND rr.status = 2
            ), paymentorder_desagregado_modification_consolidate AS (
                SELECT paymentorderdetailId, sum(value_modification) AS value_modification FROM paymentorder_modification GROUP BY paymentorderdetailId
            ), paymentorder AS (
            SELECT
                pobm.commitmentdetailid,
                (pobm.paymentorder_detail_initial_value + ISNULL(pomc.value_modification, 0)) AS paymentorder_detail_value
            FROM paymentorder_before_modification AS pobm
            LEFT JOIN paymentorder_desagregado_modification_consolidate AS pomc ON pomc.PaymentOrderDetailId = pobm.paymentorder_detail_id
            ), paymentorder_consolidado as (
                SELECT commitmentdetailid, SUM(paymentorder_detail_value) AS paymentorder_detail_value FROM paymentorder GROUP BY commitmentdetailid
            )
            --- FIN ORDENES DE COMPRA VIE19 A CORTE 
            , obligation_desagregado_before_modification AS (
                SELECT 
                    odd.* 
                FROM budget.obligation_extensions AS  oe
                INNER JOIN budget.obligation_detail_danes AS odd ON odd.obligation_extension_id = oe.id
                INNER JOIN vie19.budget.obligation AS o ON o.Id = oe.obligation_id
                INNER JOIN vie19.budget.BudgetaryValidity AS bv ON bv.id = o.BudgetaryValidityId
                WHERE bv.year = @year
                    AND CONVERT(DATE, o.DocumentDate, 112) <= @closing_date
                    AND o.status = 2
            ), obligation_modification_detail AS (
                SELECT
                    avd.entity_id AS obligation_detail_danes_id,
                    CASE 
                        WHEN avd.movement = 'D' THEN -avd.value
                        ELSE avd.value
                    END AS value_modification
                FROM budget.adjustment_vouchers AS av
                INNER JOIN budget.adjustment_voucher_details AS avd ON avd.adjustment_voucher_id = av.id
                WHERE av.entity_name = 'Obligation'
                    AND CAST(ISNULL(av.movement_date, CAST(av.created_at AS DATE )) AS DATE) < = @closing_date
                    AND av.state = 'A'
            ), obligation_desagregado_modification_consolidate AS (
                SELECT obligation_detail_danes_id, sum(value_modification) AS value_modification FROM obligation_modification_detail GROUP BY obligation_detail_danes_id
            ), obligation_desagregado AS (
                SELECT
                    obm.id,
                    obm.commitment_detail_id,
                    obm.obligation_detail_id,
                    obm.dane_code_id,
                    obm.obligation_extension_id,
                    (obm.initial_value + ISNULL(omc.value_modification, 0)) AS current_value,
                    obm.state 
                FROM obligation_desagregado_before_modification AS obm
                LEFT JOIN obligation_desagregado_modification_consolidate AS omc ON omc.obligation_detail_danes_id = obm.id
            ), obligation_desagregado_consolidate AS (
                SELECT commitment_detail_id, dane_code_id, SUM(current_value) AS current_value FROM obligation_desagregado GROUP BY commitment_detail_id, dane_code_id
            ), paymentorder_desagregado_before_modification AS (
                SELECT 
                    podd.*,
                    od.CommitmentDetailId as commitment_detail_id
                FROM budget.paymentorder_extensions AS  poe
                INNER JOIN budget.paymentorder_detail_danes AS podd ON podd.paymentorder_extension_id = poe.id
                INNER JOIN vie19.budget.obligationdetail as od ON od.id = podd.obligation_detail_id
                INNER JOIN vie19.budget.paymentorder AS po ON po.Id = poe.paymentorder_id
                INNER JOIN vie19.budget.BudgetaryValidity AS bv ON bv.id = po.BudgetaryValidityId
                WHERE bv.year = @year
                    AND CONVERT(DATE, po.DocumentDate, 112) <= @closing_date
                    -- AND po.status = 2
            ), paymentorder_modification_detail AS (
                SELECT
                    avd.entity_id AS paymentorder_detail_danes_id,
                    CASE 
                        WHEN avd.movement = 'D' THEN -avd.value
                        ELSE avd.value
                    END AS value_modification
                FROM budget.adjustment_vouchers AS av
                INNER JOIN budget.adjustment_voucher_details AS avd ON avd.adjustment_voucher_id = av.id
                WHERE av.entity_name = 'PaymentOrder'
                    AND CAST(ISNULL(av.movement_date, CAST(av.created_at AS DATE )) AS DATE) < = @closing_date
                    AND av.state = 'A'
            ), paymentorder_modification_consolidate AS (
                SELECT paymentorder_detail_danes_id, sum(value_modification) AS value_modification FROM paymentorder_modification_detail GROUP BY paymentorder_detail_danes_id
            ), paymentorder_desagregado AS (
                SELECT
                    pobm.id,
                    pobm.paymentorder_detail_id,
                    pobm.obligation_detail_id,
                    pobm.dane_code_id,
                    pobm.paymentorder_extension_id,
                    (pobm.initial_value + ISNULL(pomc.value_modification, 0)) AS current_value,
                    pobm.state,
                    pobm.commitment_detail_id
                FROM paymentorder_desagregado_before_modification AS pobm
                LEFT JOIN paymentorder_modification_consolidate AS pomc ON pomc.paymentorder_detail_danes_id = pobm.id
            ), paymentorder_desagregado_consolidado as (
                SELECT commitment_detail_id, dane_code_id, SUM(current_value) AS current_value FROM paymentorder_desagregado GROUP BY commitment_detail_id, dane_code_id
            ),commitment_desagregate_before_modification AS (
                SELECT 
                    cdd.*,
                    c.id as commitmentid
                FROM budget.commitment_extensions AS  ce
                INNER JOIN budget.commitment_detail_danes AS cdd ON cdd.commitment_extension_id = ce.id
                INNER JOIN vie19.budget.commitment AS c ON c.Id = ce.commitment_id
                INNER JOIN vie19.budget.BudgetaryValidity AS bv ON bv.id = c.BudgetaryValidityId
                WHERE bv.year = @year
                    AND CONVERT(DATE, c.DocumentDate, 112) <= @closing_date
                    AND c.status = 2
            ), commitment_modification_detail AS (
                SELECT
                    avd.entity_id AS commitment_detail_danes_id,
                    CASE 
                        WHEN avd.movement = 'D' THEN -avd.value
                        ELSE avd.value
                    END AS value_modification
                FROM budget.adjustment_vouchers AS av
                INNER JOIN budget.adjustment_voucher_details AS avd ON avd.adjustment_voucher_id = av.id
                WHERE av.entity_name = 'Commitment'
                    AND CAST(ISNULL(av.movement_date, CAST(av.created_at AS DATE )) AS DATE) < = @closing_date
                    AND av.state = 'A'
            ), commitment_desagregation_modification_consolidate AS (
                SELECT commitment_detail_danes_id, sum(value_modification) AS value_modification FROM commitment_modification_detail GROUP BY commitment_detail_danes_id
            ), commitment_desagregado AS (
                SELECT
                    cbm.commitmentid,
                    cbm.commitment_detail_id,
                    cbm.dane_code_id,
                    cbm.commitment_extension_id,
                    (cbm.initial_value + ISNULL(cmc.value_modification, 0)) AS current_value,
                    cbm.state 
                FROM commitment_desagregate_before_modification AS cbm
                LEFT JOIN commitment_desagregation_modification_consolidate AS cmc ON cmc.commitment_detail_danes_id = cbm.id
            ), desagregado AS (
                SELECT
                    cd.commitment_id,
                    cd.commitment_code,
                    cd.budgetary,
                    cd.third,
                    cd.DocumentSource,
                    cd.Document,
                    cd.DocumentDate,
                    cd.commitment_detail_id,
                    cd.category_code,
                    cd.category_name,
                    cd.disaggregated,
                    cd.financial_source_code,
                    cd.financial_source,
                    CASE
                        WHEN cd.validity = 1 THEN 'ACTUAL'
                        ELSE 'ANTERIOR'
                    END AS validity,
                    cdd.dane_code_id,
                    dc.code AS dane_codes,
                    dc.title AS dane_code_name,
                    CASE 
                        WHEN cdd.dane_code_id IS NULL THEN cd.commitment_detail_value 
                        ELSE cdd.current_value 
                    END AS commitment_detail_dane_value,
                    CASE 
                        WHEN cdd.dane_code_id IS NULL THEN (
                            SELECT obligation_detail_value FROM obligation_consolidado WHERE commitmentdetailid = cd.commitment_detail_id
                        )
                        ELSE odd.current_value
                    END AS obligation_detail_value,
                    CASE 
                        WHEN cdd.dane_code_id IS NULL THEN (
                            SELECT paymentorder_detail_value FROM paymentorder_consolidado WHERE commitmentdetailid = cd.commitment_detail_id
                        )
                        ELSE podd.current_value
                    END AS paymentorder_detail_value
                FROM commitment AS cd
                INNER JOIN budget.commitment_extensions AS ce ON ce.commitment_id = cd.commitment_id
                LEFT JOIN commitment_desagregado AS cdd ON cdd.commitmentid = cd.commitment_id AND cdd.commitment_detail_id = cd.commitment_detail_id
                LEFT JOIN obligation_desagregado_consolidate AS odd ON odd.commitment_detail_id = cdd.commitment_detail_id AND odd.dane_code_id = cdd.dane_code_id
                LEFT JOIN paymentorder_desagregado_consolidado AS podd ON podd.commitment_detail_id = cdd.commitment_detail_id AND podd.dane_code_id = cdd.dane_code_id
                LEFT JOIN budget.dane_codes AS dc ON dc.id = cdd.dane_code_id
            ), consolidado AS (
                SELECT
                    category_code, 
                    category_name, 
                    financial_source_code,
                    financial_source, 
                    validity, 
                    dane_codes, 
                    dane_code_name,
                    SUM(ISNULL(commitment_detail_dane_value, 0)) AS commitment_valu,
                    SUM(ISNULL(obligation_detail_value, 0)) AS obligation_value,
                    SUM(ISNULL(paymentorder_detail_value, 0)) AS paymentorder_value
                FROM desagregado 
                GROUP BY category_code, 
                        category_name,
                        financial_source_code,
                        financial_source, 
                        validity, 
                        dane_codes, 
                        dane_code_name
            )
            
            SELECT 
                category_code,
                category_name,
                financial_source_code,
                financial_source,
                validity,
                CASE 
                    WHEN validity = 'ACTUAL' THEN 1
                    WHEN validity = 'ANTERIOR' THEN 3
                    ELSE 0
                END AS validity_code,
                dane_codes,
                dane_code_name,
                commitment_valu,
                obligation_value,
                paymentorder_value
            FROM consolidado ORDER BY category_code, category_name, financial_source_code, financial_source";

        $commitments = \DB::connection('SIGH')->select($querySQL, [$year, $closing_date]);

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
        return 'BudgetManagement.report-management.struct.report-disaggregated-budget-closing';
    }
	
	
}