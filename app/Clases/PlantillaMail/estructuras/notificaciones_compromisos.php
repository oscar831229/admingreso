<?php


$notificaciones_compromisos = array(

	'source' => 'mdc_meetings AS mm',
	'relationTable' => array(
		array(
			'tabla'=>'mdc_commitments AS mc',
			'enlace_left'=>'mc.mdc_meeting_id',
			'enlace_right'=>'mm.id',
			'direccion'=>''
		),
		array(
			'tabla'=>'mdc_commitment_details AS mcd',
			'enlace_left'=>'mcd.mdc_commitment_id',
			'enlace_right'=>'mc.id',
			'direccion'=>''
		),
		array(
			'tabla'=>'detail_definitions AS mdd',
			'enlace_left'=>'mdd.id',
			'enlace_right'=>'mm.type',
			'direccion'=>''
		),
		array(
			'tabla'=>'detail_definitions AS pdd',
			'enlace_left'=>'pdd.id',
			'enlace_right'=>'mc.priority',
			'direccion'=>''
		),
		array(
			'tabla'=>'people AS p',
			'enlace_left'=>'p.id',
			'enlace_right'=>'mcd.person_id',
			'direccion'=>''
		)
	),
	'where' => array(
		'mcd.id' => '1'
	),
	'components' => array(
		'reunion_id' => array(
			'detailField' => 'mm.id',
			'caption' => 'Nombre reunion'
		),
		'nombre_reunion' => array(
			'detailField' => 'mm.name',
			'caption' => 'Nombre reunion'
		),
		'descripcion_reunion' => array(
			'detailField' => 'mm.description',
			'caption' => 'Descripción reunión'
		),
		'fecha_reunion' => array(
			'detailField' => 'mm.meeting_date',
			'caption' => 'fecha reunión'
		),
		'hora_reunion' => array(
			'detailField' => 'mm.meeting_hour',
			'caption' => 'hora reunion'
		),
		'nombre_compromiso' => array(
			'detailField' => 'mc.name',
			'caption' => 'nombre compromiso'
		),
		'descripcion_compromiso' => array(
			'detailField' => 'mc.description',
			'caption' => 'descripcion del compromiso'
		),
		'prioridad_alta' => array(
			'detailField' => 'pdd.name',
			'caption' => 'prioridad'
		),
		'nombre_responsable' => array(
			'detailField' => "CONCAT(IFNULL(p.first_name,''), ' ', IFNULL(p.second_name,''), ' ', IFNULL(p.first_surname,''), ' ', IFNULL(p.second_surname,''))",
			'caption' => 'Nombre responsable'
		),
		'token' => array(
			'detailField' => 'p.token_notification',
			'caption' => 'token'
		),
		'fecha_cumplimiento' => array(
			'detailField' => 'mcd.compliance_date',
			'caption' => 'fecha cumplimiento'
		),
		'descripcion_responsable' => array(
			'detailField' => 'mcd.description',
			'caption' => 'descripción actividad'
		)
	),
	'details' =>array( 
	),
	'default' => array(
	)
);


?>