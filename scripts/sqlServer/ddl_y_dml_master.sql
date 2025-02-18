-- VISTA PARA GESTION PRESUPUESTAL

	IF EXISTS(select * FROM sys.views where name = 'sigh_category_groups')
	BEGIN
		DROP VIEW budget.sigh_category_groups 
	END 
	GO

	CREATE VIEW budget.sigh_category_groups 
	AS
		WITH grupos_vigencia AS (
			SELECT 
				pg.Id AS product_group_id,
				pg.Name AS product_group_name,
				pg.Code AS product_group_code,
				bv.Id AS budgetary_validity_id,
				bv.year AS budgetary_validity_year
			FROM vie19.inventory.productgroup AS pg
			INNER JOIN vie19.budget.BudgetaryValidity AS bv ON 1 = 1
		), sigh_category_groups AS (
			SELECT
				cg.id,
				gv.*,
				cg.category_id,
				c.code AS category_code,
				c.Name AS category_name
			FROM grupos_vigencia AS gv 
			LEFT JOIN budget.category_groups AS cg ON cg.product_group_id = gv.product_group_id AND cg.budgetary_validity_id = gv.budgetary_validity_id
			LEFT JOIN vie19.budget.category c ON c.Id = cg.category_id
		)
		
		SELECT * FROM sigh_category_groups
	GO 
	
-------------------------------------------------------------------------------------------
-- 2. PROCEDIMIENTO ALMACENADO ACTUALIZACION DE SALDOS COMPROBANTES DESAGREGADO DANE
-- FECHA CREACION - 2022-02-15
-------------------------------------------------------------------------------------------

	IF OBJECT_ID ('[budget].[sp_balance_update_disaggregated_dane]') IS NOT NULL
		DROP PROCEDURE [budget].[sp_balance_update_disaggregated_dane]
	GO

	CREATE PROCEDURE [budget].[sp_balance_update_disaggregated_dane]
		@entity VARCHAR(60),
		@entity_id BIGINT,
		@error BIT OUTPUT,
		@message VARCHAR(255) OUTPUT
	AS
	BEGIN

		SET NOCOUNT ON;
		
		SET @error = 0
		SET @message = ''
		
		-- ACTUALIZAR VALOR ACTUAL DESAGREGADO DE COMPROMISOS
		IF ISNULL(@entity, '') = 'Commitment' AND EXISTS(SELECT * FROM vie19.budget.commitment where Id =  @entity_id)
		BEGIN
			
			-- ACTUALIZAR SALDOS DESAGREGADO CODIGO DANES
			WITH commitment AS (
				SELECT cdd.* FROM budget.commitment_extensions ce
				INNER JOIN budget.commitment_detail_danes cdd ON cdd.commitment_extension_id = ce.id
				WHERE ce.commitment_id = @entity_id AND ce.state = 'C'
			), commitment_adjustment AS (
				SELECT 
					c.id AS commitment_detail_dane_id, 
					c.current_value, 
					avd.* 
				FROM commitment AS c
				INNER JOIN budget.adjustment_voucher_details AS avd ON avd.entity_id = c.id AND avd.entity_name = 'commitment_detail_danes'
				INNER JOIN budget.adjustment_vouchers AS av ON av.id = avd.adjustment_voucher_id AND av.state = 'A'
			), adjusment_final AS (
				SELECT 
					ca.commitment_detail_dane_id,
					ca.current_value, 
					SUM(
						CASE 
							WHEN ca.movement = 'C' THEN ca.value
							WHEN ca.movement = 'D' THEN -ca.value
						END
					) AS adjusment_value
				FROM commitment_adjustment ca
				GROUP BY ca.commitment_detail_dane_id, ca.current_value
			)

			UPDATE cdd SET  cdd.current_value = cdd.initial_value + ISNULL(af.adjusment_value,0)
			FROM budget.commitment_detail_danes AS cdd
			INNER JOIN commitment as c on c.id = cdd.id
			LEFT JOIN adjusment_final AS af ON af.commitment_detail_dane_id = cdd.id
			
			
		END 

		-- ACTUALIZAR VALOR ACTUAL DESAGREGADO DE OBLIIGACION
		IF ISNULL(@entity, '') = 'Obligation' AND EXISTS(SELECT * FROM vie19.budget.obligation where Id =  @entity_id)
		BEGIN
			
			-- ACTUALIZAR SALDOS DESAGREGADO CODIGO DANES
			WITH obligation AS (
				SELECT odd.* FROM budget.obligation_extensions oe
				INNER JOIN budget.obligation_detail_danes odd ON odd.obligation_extension_id = oe.id
				WHERE oe.obligation_id = @entity_id AND oe.state = 'C'
			), obligation_adjustment AS (
				SELECT o.id AS obligation_detail_dane_id, o.current_value, avd.* FROM obligation AS o
				INNER JOIN budget.adjustment_voucher_details AS avd ON avd.entity_id = o.id AND avd.entity_name = 'obligation_detail_danes'
				INNER JOIN budget.adjustment_vouchers AS av ON av.id = avd.adjustment_voucher_id AND av.state = 'A'
			), adjusment_final AS (
				SELECT 
					oa.obligation_detail_dane_id,
					oa.current_value, 
					SUM(
						CASE 
							WHEN oa.movement = 'C' THEN oa.value
							WHEN oa.movement = 'D' THEN -oa.value
						END
					) AS adjusment_value
				FROM obligation_adjustment oa
				GROUP BY oa.obligation_detail_dane_id, oa.current_value
			)

			UPDATE odd SET  odd.current_value = odd.initial_value + ISNULL(af.adjusment_value,0)
			FROM budget.obligation_detail_danes AS odd
			INNER JOIN obligation as o on o.id = odd.id
			LEFT JOIN adjusment_final AS af ON af.obligation_detail_dane_id = odd.id
			
		END 


		-- ACTUALIZAR VALOR ACTUAL DESAGREGADO DE ORDEN DE PAGO
		IF ISNULL(@entity, '') = 'PaymentOrder' AND EXISTS(SELECT * FROM vie19.budget.paymentorder where Id =  @entity_id)
		BEGIN
			
			-- ACTUALIZAR SALDOS DESAGREGADO CODIGO DANES
			WITH paymentorder AS (
				SELECT podd.* FROM budget.paymentorder_extensions poe
				INNER JOIN budget.paymentorder_detail_danes podd ON podd.paymentorder_extension_id = poe.id
				WHERE poe.paymentorder_id = @entity_id AND poe.state = 'C'
			), paymentorder_adjustment AS (
				SELECT 
					po.id AS paymentorder_detail_dane_id, 
					po.current_value, 
					avd.* 
				FROM paymentorder AS po
				INNER JOIN budget.adjustment_voucher_details AS avd ON avd.entity_id = po.id AND avd.entity_name = 'paymentorder_detail_danes'
				INNER JOIN budget.adjustment_vouchers AS av ON av.id = avd.adjustment_voucher_id AND av.state = 'A'
			), adjusment_final AS (
				SELECT 
					poa.paymentorder_detail_dane_id,
					poa.current_value, 
					SUM(
						CASE 
							WHEN poa.movement = 'C' THEN poa.value
							WHEN poa.movement = 'D' THEN -poa.value
						END
					) AS adjusment_value
				FROM paymentorder_adjustment poa
				GROUP BY poa.paymentorder_detail_dane_id, poa.current_value
			)

			UPDATE podd SET  podd.current_value = podd.initial_value + ISNULL(af.adjusment_value,0)
			FROM budget.paymentorder_detail_danes AS podd
			INNER JOIN paymentorder as po on po.id = podd.id
			LEFT JOIN adjusment_final AS af ON af.paymentorder_detail_dane_id = podd.id
			
			
		END
		
	END

	GO
