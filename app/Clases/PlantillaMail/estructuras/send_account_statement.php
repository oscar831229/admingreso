<?php


$send_account_statement = array(

	'source' => 'notify_infos',
	'relationTable' => array(
		array(
			'tabla'=>'notify_info_dates',
			'enlace_left'=>'notify_info_dates.notify_info_id',
			'enlace_right'=>'notify_infos.id',
			'direccion'=>''
		),
		array(
			'tabla'=>'notify_info_tokens',
			'enlace_left'=>'notify_info_tokens.notify_info_cicle_id',
			'enlace_right'=>'notify_infos.notify_info_cicle_id',
			'direccion'=>''
		)
	),
	'where' => array(
		'id' => '1'
	),
	'components' => array(
		'codigo_informe' => array(
			'caption' => 'Código',
			'detailField' => 'code'
		),
		'nombre_reporte' => array(
			'type' => 'text',
			'caption' => 'Nombre reporte',
			'detailField' => 'report_name',
			'valueType' => 'textUpper'
		),
		'normatividad' => array(
			'type' => 'text',
			'caption' => 'Normatividad',
			'detailField' => 'normativity',
		),
		'periocidad' => array(
			'type' => 'text',
			'caption' => 'Periocidad',
			'detailField' => 'periodicity',
		),
		'unidad_responsable' => array(
			'type' => 'text',
			'caption' => 'Unidad responsable',
			'detailField' => 'responsible_unity',
		),
		'persona_responsable' => array(
			'type' => 'text',
			'caption' => 'Persona responsable',
			'detailField' => 'notify_infos.responsible_person',
		),
		'email_responsable' => array(
			'type' => 'text',
			'caption' => 'Email responsable',
			'detailField' => 'responsible_email',
		),
		'fecha_vencimiento' => array(
			'type' => 'text',
			'caption' => 'Fecha vencimiento',
			'detailField' => 'notify_info_dates.expiration_date',
		),
		'token' => array(
			'type' => 'text',
			'caption' => 'Token informes',
			'detailField' => 'notify_info_tokens.token',
		)
	),
	'details' =>array( 
	),
	'default' => array(
	)
);


?>