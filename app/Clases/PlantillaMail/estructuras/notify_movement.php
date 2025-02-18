<?php


$notify_movement = array(

	'source' => 'movements as m',
	'relationTable' => array(
		array(
			'tabla'=>'movement_types AS mt',
			'enlace_left'=>'mt.id',
			'enlace_right'=>'m.movement_type_id',
			'direccion'=>''
		),
		array(
			'tabla'=>'electrical_pocket_wallet_user AS epwu',
			'enlace_left'=>'epwu.id',
			'enlace_right'=>'m.electrical_pocket_wallet_user_id',
			'direccion'=>''
		),
		array(
			'tabla'=>'electrical_pockets AS ep',
			'enlace_left'=>'ep.id',
			'enlace_right'=>'m.electrical_pocket_id',
			'direccion'=>''
		),
		array(
			'tabla'=>'wallet_users AS wu',
			'enlace_left'=>'wu.id',
			'enlace_right'=>'epwu.wallet_user_id',
			'direccion'=>''
		),
		array(
			'tabla'=>'stores AS s',
			'enlace_left'=>'s.id',
			'enlace_right'=>'m.store_id',
			'direccion'=>''
		)
	),
	'where' => array(
		'm.id' => '1'
	),
	'components' => array(
		'nombre_bolsillo' => array(
			'caption' => 'Código',
			'detailField' => 'ep.name'
		),
		'documento_transaccion' => array(
			'type' => 'text',
			'caption' => 'Nombre reporte',
			'detailField' => 'm.transaction_document_number',
		),
		'comercio' => array(
			'type' => 'text',
			'caption' => 'Normatividad',
			'detailField' => "s.name",
		),
		'movimiento' => array(
			'type' => 'text',
			'caption' => 'Normatividad',
			'detailField' => "CONCAT(mt.code, ' ', mt.name)",
		),
		'valor_movimiento' => array(
			'type' => 'text',
			'caption' => 'Periocidad',
			'detailField' => "CASE
				WHEN m.nature_movement = 'C' THEN m.value
				ELSE -m.value
			END",
		),
		'usuario_pos' => array(
			'type' => 'text',
			'caption' => 'Unidad responsable',
			'detailField' => 'm.user_code',
		),
		'codigo_cus' => array(
			'type' => 'text',
			'caption' => 'Persona responsable',
			'detailField' => 'm.cus',
		),
		'fecha_movimiento' => array(
			'type' => 'text',
			'caption' => 'Email responsable',
			'detailField' => 'm.movement_date',
		),
		'nombre_usuario' => array(
			'caption' => 'Nombre usuario tiquetera',
			'detailField' => "CONCAT(IFNULL(first_name, ''), ' ', IFNULL(second_name, ''), ' ', IFNULL(first_surname, ''), ' ', IFNULL(second_surname, ''))"
		),
	),
	'details' =>array( 
	),
	'default' => array(
	)
);


?>