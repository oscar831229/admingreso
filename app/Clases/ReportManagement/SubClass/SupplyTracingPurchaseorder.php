<?php 
namespace App\Clases\ReportManagement\SubClass;

use App\Models\Officials\Person;
use App\Models\Officials\SurveyForm;
use App\Models\Officials\SurveyFormDetail;
use App\Models\Officials\Company;


class SupplyTracingPurchaseorder
{
	public static $filename = 'trazabilidad_ordenes_compra.xlsx';

    public static $columns = [
        'Id Orden compra',
        'Id Detalle',
        'Id Almacen',
        'Consecutivo',
        'Fecha orden',
        'Fecha entrega',
        'Id proveedor',
        'Nit',
        'Tercero',
        'Basado en contrato',
        'Número contrato',
        'Consecutivo contrato',
        'Id producto',
        'Código producto',
        'Producto',
        'Código grupo',
        'Nombre grupo',
        'Código subgrupo',
        'Nombre subgrupo',
        'Unidad',
        'Cantidad solicitada',
        'Cantidad despachada',
        'Cantidad despachada remisión manual',
        'Remisiones manuales',
        'Cantidad pendiente',
        'Valor unitario',
        'Descuento',
        'Iva',
        'Subtotal',
        'Cantidad x entrada',
        'Cantidad x remisión',
        'Cantidad devuelta',
        'Cantidad x remisión sin realación',
        'Ultima remisión',
        'Fecha última remisión',
        'Remisiones sin vincular',
        'Cantidad remisiones sin vincular',
        'Fechas remisiones sin vincular'
    ];

	public static $data;
	
    # PARAMETRO PETICION

    public function __construct($request){
        $this->request = $request;
    }
	
	public function getData(){

        $fecha_inicial_orden = $this->request->input('date_from_purchase');
        $fecha_final_orden = $this->request->input('date_end_purchase');
        $fecha_inicial_remision = $this->request->input('date_from_remission');
        $fecha_final_remision = $this->request->input('date_end_remission');

        $querySQL = "DECLARE @fecha_inicial VARCHAR(15) = ?,
                            @fecha_final VARCHAR(15) = ?,
                            @fecha_inicial_remision VARCHAR(15) = ?,
                            @fecha_final_remision VARCHAR(15) = ?
                            
                    SET @fecha_inicial_remision = CASE 
                        WHEN ISNULL(@fecha_inicial_remision, '') = '' THEN @fecha_inicial
                        ELSE @fecha_inicial_remision
                    END
    
                    SET @fecha_final_remision = CASE 
                        WHEN ISNULL(@fecha_final_remision, '') = '' THEN CAST(getdate() AS date)
                        ELSE @fecha_final_remision
                    END

                    ;WITH ordenes_compra AS (        
                        SELECT
                            po.Id AS PurchaseOrderId,
                            pod.Id AS PurchaseOrderDetailId,
                            po.WarehouseId,
                            po.Code Consecutivo,
                            po.DocumentDate FechaOrden,
                            po.DeliveredDate FechaEntrega,
                            po.SupplierId,
                            s.Code Nit,s.Name Tercero,
                            CASE WHEN po.IsBasedContract=0 THEN 'NO' ELSE 'SI' END BasadoContrato,
                            ic.ContractNumber ContratoNumero,
                            ic.Code ConsecContrato,
                            ip.Id AS ProductId,
                            ip.Code CodProducto, 
                            ip.Name NombreProducto,
                            pg.Code CodGrupo,
                            pg.Name NomGrupo,
                            ps.Code CodSubgru,
                            ps.Name NombreSubgrupo,
                            imu.Name Unidad,
                            pod.Quantity CantSolict,
                            pod.CancelledQuantity CantDespachada,
                            pod.OutstandingQuantity CantPendiente,
                            pod.Value ValorUnit,
                            pod.DiscountValue Descuento,
                            pod.IvaValue VrIVA,
                            pod.TotalValue Subtotal_OC
                        FROM VIE19.Inventory.PurchaseOrder po
                            INNER JOIN VIE19.Inventory.PurchaseOrderDetail pod ON po.Id=pod.PurchaseOrderId AND po.Status=2
                            INNER JOIN VIE19.Common.Supplier s  ON s.Id=po.SupplierId
                            left JOIN VIE19.Inventory.InventoryContract ic ON ic.Id=po.ContractId
                            LEFT JOIN VIE19.Inventory.InventoryProduct ip ON ip.Id=pod.ProductId
                            LEFT JOIN VIE19.Inventory.InventoryMeasurementUnit imu ON imu.Id=ip.MeasurementUnitId
                            LEFT JOIN VIE19.Inventory.ProductGroup pg ON pg.Id=ip.ProductGroupId
                            LEFT JOIN VIE19.Inventory.ProductSubGroup ps ON ps.Id=ip.ProductSubGroupId
                        WHERE po.DocumentDate>=@fecha_inicial  AND po.DocumentDate<=@fecha_final
                    ), remisiones AS (
                        SELECT 
                            MAX(re.Id) AS RemissionEntranceId,
                            red.PurchaseOrderDetailId, 
                            sum(red.Quantity) AS Quantity 
                        FROM vie19.Inventory.RemissionEntranceDetail AS red
                        INNER JOIN vie19.Inventory.RemissionEntrance re on re.id=red.RemissionEntranceId
                        INNER JOIN ordenes_compra AS oc ON oc.PurchaseOrderDetailId = red.PurchaseOrderDetailId
                        GROUP BY red.PurchaseOrderDetailId
                    ), entradas_almacen AS (
                        SELECT 
                            evd.PurchaseOrderDetailId, 
                            sum(evd.Quantity) AS Quantity 
                        FROM VIE19.Inventory.EntranceVoucherDetail AS evd
                            INNER JOIN VIE19.Inventory.EntranceVoucher ev ON ev.Id=evd.EntranceVoucherId AND ev.Status=2
                            INNER JOIN ordenes_compra AS oc ON oc.PurchaseOrderDetailId = evd.PurchaseOrderDetailId
                        GROUP BY evd.PurchaseOrderDetailId
                    ), devoluciones_ordenes AS (
                        SELECT 
                            podd.PurchaseOrderDetailId, 
                            sum(podd.Quantity) AS Quantity 
                        FROM VIE19.Inventory.PurchaseOrderDevolutionDetail AS podd
                            INNER JOIN ordenes_compra AS oc ON oc.PurchaseOrderDetailId = podd.PurchaseOrderDetailId
                        GROUP BY podd.PurchaseOrderDetailId
                    ), remisiones_no_vinculadas_general AS (	
                        SELECT
                            re.id,
                            re.code,
                            CAST(re.RemissionDate AS DATE) AS RemissionDate,
                            re.SupplierId,
                            re.WareHouseId,
                            red.ProductId,
                            red.Quantity
                        FROM vie19.Inventory.RemissionEntrance AS re
                        INNER JOIN vie19.Inventory.RemissionEntranceDetail AS red on red.RemissionEntranceId=re.Id
                        LEFT JOIN inventory.remission_entrance_detail_extension AS rede ON rede.remission_entrance_detail_id = red.id
                        WHERE CAST(re.RemissionDate AS DATE) BETWEEN ISNULL(@fecha_inicial_remision, @fecha_inicial) AND ISNULL(@fecha_final_remision, @fecha_final)
                            AND ISNULL(red.PurchaseOrderDetailId, '') = ''
                            AND ISNULL(rede.id, '') = ''
                    ), remision_no_vinculadas AS (
                        SELECT
                            (
                                SELECT
                                    CONCAT('| ', code)
                                FROM remisiones_no_vinculadas_general
                                WHERE SupplierId = rnvg.SupplierId AND ProductId = rnvg.ProductId
                                FOR XML PATH('')
                            ) AS remisiones,
                            (
                                SELECT
                                    CONCAT('| ', RemissionDate)
                                FROM remisiones_no_vinculadas_general
                                WHERE SupplierId = rnvg.SupplierId AND ProductId = rnvg.ProductId
                                FOR XML PATH('')
                            ) AS dates,
                            (
                                SELECT
                                    CONCAT('| ', Quantity)
                                FROM remisiones_no_vinculadas_general
                                WHERE SupplierId = rnvg.SupplierId AND ProductId = rnvg.ProductId
                                FOR XML PATH('')
                            ) AS quantities,
                            SupplierId,
                            ProductId,
                            SUM(ISNULL(Quantity, 0)) AS Quantity
                        FROM remisiones_no_vinculadas_general AS rnvg
                        GROUP BY SupplierId, ProductId
                    ), remisiones_vinculadas_manuales AS (
                        SELECT
                            rede.purchase_order_detail_id, 
                            sum(rede.Quantity) AS Quantity 
                        FROM vie19.Inventory.RemissionEntranceDetail AS red
                        INNER JOIN vie19.Inventory.RemissionEntrance re on re.id=red.RemissionEntranceId
                        INNER JOIN inventory.remission_entrance_detail_extension AS rede ON rede.remission_entrance_detail_id = red.id
                        INNER JOIN ordenes_compra AS oc ON oc.PurchaseOrderDetailId = rede.purchase_order_detail_id
                        GROUP BY rede.purchase_order_detail_id
                    )
                    
                    SELECT
                        oc.PurchaseOrderId,
                        oc.PurchaseOrderDetailId,
                        oc.WarehouseId,
                        oc.Consecutivo,
                        oc.FechaOrden,
                        oc.FechaEntrega,
                        oc.SupplierId,
                        oc.Nit,
                        oc.Tercero,
                        oc.BasadoContrato,
                        oc.ContratoNumero,
                        oc.ConsecContrato,
                        oc.ProductId,
                        oc.CodProducto,
                        oc.NombreProducto,
                        oc.CodGrupo,
                        oc.NomGrupo,
                        oc.CodSubgru,
                        oc.NombreSubgrupo,
                        oc.Unidad,
                        oc.CantSolict,
                        oc.CantDespachada,
                        ISNULL(rvm.Quantity, 0) AS cantidadDespachadaRemisionManual,
                        (SELECT
                            CONCAT('| ', re.Code, '-',convert(varchar(10), re.RemissionDate, 112))
                        FROM inventory.remission_entrance_detail_extension AS rede 
                        INNER JOIN vie19.Inventory.RemissionEntranceDetail AS red on red.id=rede.remission_entrance_detail_id
                        INNER JOIN vie19.Inventory.RemissionEntrance AS re ON re.id = red.RemissionEntranceId
                        WHERE purchase_order_detail_id = oc.PurchaseOrderDetailId
                        GROUP BY re.Code, re.RemissionDate
                        FOR XML PATH('')) AS remisiones_manuales,
                        (oc.CantPendiente - ISNULL(rvm.Quantity, 0)) AS CantPendiente,
                        oc.ValorUnit,
                        oc.Descuento,
                        oc.VrIVA,
                        oc.Subtotal_OC,
                        ISNULL(ea.Quantity, 0) AS cantidad_x_entrada,
                        ISNULL(r.Quantity, 0) AS cantidad_x_remision,
                        ISNULL(do.Quantity, 0) AS cantidad_devuelta,
                        ISNULL(rnv.Quantity, 0) AS cantidad_x_remisino_sin_relacion,
                        re.Code AS RemissionEntranceCode,
                        re.RemissionDate,
                        rnv.remisiones,
                        rnv.quantities,
                        rnv.dates
                    FROM ordenes_compra oc
                    LEFT JOIN remisiones r ON r.PurchaseOrderDetailId = oc.PurchaseOrderDetailId
                    LEFT JOIN vie19.Inventory.RemissionEntrance AS re ON re.Id = r.RemissionEntranceId
                    LEFT JOIN entradas_almacen AS ea ON ea.PurchaseOrderDetailId = oc.PurchaseOrderDetailId
                    LEFT JOIN devoluciones_ordenes AS do ON do.PurchaseOrderDetailId = oc.PurchaseOrderDetailId
                    LEFT JOIN remision_no_vinculadas AS rnv ON rnv.SupplierId = oc.SupplierId AND rnv.ProductId = oc.ProductId AND oc.CantPendiente > 0
                    LEFT JOIN remisiones_vinculadas_manuales AS rvm ON rvm.purchase_order_detail_id = oc.PurchaseOrderDetailId
                    ORDER BY PurchaseOrderId, PurchaseOrderDetailId";

        return \DB::connection('SIGH')->select($querySQL, [$fecha_inicial_orden, $fecha_final_orden, $fecha_inicial_remision, $fecha_final_remision]);
        

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
        return view('SupplyManagement.report-management.reports.'.$code, compact('collaborator_type', 'bonding_type', 'companies','code'));
    }

    public function getEstructView(){
        return 'SupplyManagement.report-management.struct.supply-tracing-purchaseorder';
    }
	
	
}