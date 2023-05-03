<?php 
namespace App\Clases\ReportManagement\SubClass;

use App\Models\Officials\Person;
use App\Models\Officials\SurveyForm;
use App\Models\Officials\SurveyFormDetail;
use App\Models\Officials\Company;


class RequirementPurchaseEntrance
{
	public static $filename = 'consolidado_solicitado_comprado_entregado.xlsx';

    public static $columns = [
        'ID PRODUCTO',
        'COD PRODUCTO',
        'PRODUCTO',
        'COD GRUPO',
        'GRUPO',
        'COD SUBGRUPO',
        'SUBGRUPO',
        'CANTIDAD SOLICITADA',
        'CANTIDAD COMPRADA',
        '% SOLICI VS COMPRA',
        'ENTREGA X ENTRADA',
        'ENTRAGA X REMISION',
        'ENTRAGA TOTAL',
        '% COMPRADO VS ENTREGADO'
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

        $anio = $this->request->input('validity');

        $querySQL = "DECLARE @anio VARCHAR(4) = ?

            -- Solicitud producto con estado confirmado y detalle aprobado
            ;WITH request_products AS (
                SELECT 
                    prd.InventoryProductId AS ProductId,
                    SUM(prd.Quantity) AS Quantity
                FROM VIE19.Inventory.PurchaseRequest AS pr 
                INNER JOIN Vie19.Inventory.PurchaseRequestDetail AS prd ON prd.PurchaseRequestId = pr.Id
                WHERE pr.status = 2 AND convert(VARCHAR(4), pr.CreationDate, 112) = @anio
                GROUP BY prd.InventoryProductId
            ), purchase_order AS (
                SELECT
                    pod.ProductId,
                    SUM(pod.Quantity) AS Quantity
                FROM VIE19.Inventory.PurchaseOrder po 
                INNER JOIN VIE19.Inventory.PurchaseOrderDetail pod ON po.Id=pod.PurchaseOrderId AND po.Status=2
                WHERE CONVERT(VARCHAR(4), po.DocumentDate, 112) = @anio AND po.Status=2
                GROUP BY pod.ProductId
            ), purchase_order_devolution AS (
                SELECT
                    apod.ProductId,
                    SUM(podd.Quantity) AS Quantity
                FROM VIE19.Inventory.PurchaseOrderDevolution AS pod
                INNER JOIN VIE19.Inventory.PurchaseOrderDevolutionDetail AS podd ON podd.PurchaseOrderDevolutionId = pod.Id
                INNER JOIN VIE19.Inventory.PurchaseOrderDetail AS apod ON apod.Id = podd.PurchaseOrderDetailId
                WHERE CONVERT(VARCHAR(4), pod.DocumentDate, 112) = @anio AND pod.Status=2
                GROUP BY apod.ProductId
            ), compra_productos AS (
                SELECT 
                    po.ProductId,
                    (po.Quantity - ISNULL(pod.Quantity, 0)) AS Quantity
                FROM purchase_order po
                LEFT JOIN purchase_order_devolution pod ON pod.ProductId = po.ProductId
            ), solicitud_compra AS (
                SELECT
                    ISNULL(rp.ProductId, cp.ProductId) AS ProductId,
                    ISNULL(rp.Quantity,0) AS QuantityRequest,
                    ISNULL(cp.Quantity,0) AS QuantityPurchase
                FROM request_products AS rp
                FULL JOIN compra_productos AS cp ON cp.ProductId = rp.ProductId
            ), entrance_products AS (
                SELECT 
                    evd.ProductId,
                    SUM(evd.Quantity) AS Quantity
                FROM VIE19.Inventory.EntranceVoucher AS ev
                INNER JOIN VIE19.Inventory.EntranceVoucherDetail AS evd ON evd.EntranceVoucherId = ev.Id
                WHERE ev.status = 2 AND convert(VARCHAR(4), ev.DocumentDate, 112) = @anio AND ISNULL(evd.RemissionEntranceDetailBatchSerialId, '') = ''
                GROUP BY evd.ProductId
            ), remission_product AS (
                SELECT 
                    red.ProductId,
                    SUM(red.Quantity) AS Quantity
                FROM vie19.Inventory.RemissionEntrance AS re
                INNER JOIN vie19.Inventory.RemissionEntranceDetail AS red ON red.RemissionEntranceId = re.Id
                WHERE re.status = 2 AND convert(VARCHAR(4), re.RemissionDate, 112) = @anio
                GROUP BY red.ProductId
            ), product_consolidate AS (
                SELECT
                    rp.*,
                    ISNULL(ep.Quantity,0) AS entrance,
                    ISNULL(rep.Quantity,0) AS remission,
                    (ISNULL(ep.Quantity,0) + ISNULL(rep.Quantity,0)) AS total_entrance
                FROM solicitud_compra AS rp
                LEFT JOIN entrance_products AS ep ON ep.ProductId = rp.ProductId
                LEFT JOIN remission_product AS rep ON rep.ProductId = rp.ProductId
            ), state_final AS (
                SELECT
                    pc.ProductId,
                    ip.Code CodProducto, 
                    ip.Name NombreProducto,
                    pg.Code CodGrupo,
                    pg.Name NomGrupo,
                    ps.Code CodSubgru,
                    ps.Name NombreSubgrupo,
                    pc.QuantityRequest,
                    pc.QuantityPurchase,
                    CASE
                        WHEN pc.QuantityRequest = 0 THEN 0
                        ELSE ((pc.QuantityPurchase * 100) / pc.QuantityRequest)
                    END AS procent_compra, 
                    pc.entrance,
                    pc.remission,
                    pc.total_entrance,
                    CASE
                        WHEN pc.QuantityPurchase = 0 THEN 0
                        ELSE ((pc.total_entrance * 100) / pc.QuantityPurchase)
                    END AS procent_entrega
                    /*,
                    ISNULL(((((pc.entrance + pc.remission) * 100)) / pc.request), 0 ) AS procent*/
                FROM product_consolidate AS pc
                LEFT JOIN VIE19.Inventory.InventoryProduct ip ON ip.Id=pc.ProductId
                LEFT JOIN VIE19.Inventory.InventoryMeasurementUnit imu ON imu.Id=ip.MeasurementUnitId
                LEFT JOIN VIE19.Inventory.ProductGroup pg ON pg.Id=ip.ProductGroupId
                LEFT JOIN VIE19.Inventory.ProductSubGroup ps ON ps.Id=ip.ProductSubGroupId
                WHERE pc.ProductId IS NOT NULL 
            )
            
            SELECT * FROM state_final";

        return \DB::connection('SIGH')->select($querySQL, [$anio]);
        

	}

    /** Retorna nombres de columnas  */
    public function getColumns(){
        return self::$columns;
    }

    /** Se obtiene los filtros del request */
    public function getRequest(){
        return $this->request;
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
        return view('SupplyManagement.report-management.reports.'.$code, compact('collaborator_type', 'bonding_type', 'companies','code'));
    }

    public function getEstructView(){
        return 'SupplyManagement.report-management.struct.requirement-purchase-entrance';
    }
	
	
}