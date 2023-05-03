<?php


$notificacion_agenda_vacunacion = array(

	'source' => 'office_schedulings as os',
	'relationTable' => array(
		array(
			'tabla'=>'control_vaccinations as c',
			'enlace_left'=>'c.id',
			'enlace_right'=>'os.control_vaccination_id',
			'direccion'=>''
		),
		array(
			'tabla'=>'offices as o',
			'enlace_left'=>'o.id',
			'enlace_right'=>'os.office_id',
			'direccion'=>''
		),
		array(
			'tabla'=>'office_turns as ot',
			'enlace_left'=>'ot.id',
			'enlace_right'=>'os.office_turn_id',
			'direccion'=>''
		)
	),
	'where' => array(
		'os.id' => '1'
	),
	'components' => array(
		'email' => array(
			'caption' => 'Email agendado',
			'detailField' => 'c.correo'
		),
		'tipo_documento' => array(
			'caption' => 'Tipo identificación',
			'detailField' => 'c.tipo_identificacion'
		),
		'numero_documento' => array(
			'type' => 'text',
			'caption' => 'Número identificación',
			'detailField' => 'c.numero_identificacion',
			'valueType' => 'textUpper'
		),
		'nombre' => array(
			'type' => 'text',
			'caption' => 'Nombre completo',
			'detailField' => "CONCAT(IFNULL(c.primer_nombre,''),' ',IFNULL(c.segundo_nombre,''),' ',IFNULL(c.primer_apellido,''), ' ', IFNULL(c.segundo_apellido,''))",
		),
		'numero_aplicacion' => array(
			'type' => 'text',
			'caption' => 'Número aplicación',
			'detailField' => 'os.application_number',
		),
		'consultorio' => array(
			'type' => 'text',
			'caption' => 'Consultorio',
			'detailField' => 'o.name',
		),
		'consultorio_id' => array(
			'type' => 'text',
			'caption' => 'codígo consultorio',
			'detailField' => 'o.code',
		),
		'fecha_agenda' => array(
			'type' => 'text',
			'caption' => 'Fecha agenda',
			'detailField' => 'os.scheduling_date',
		),
		'hora_inicial' => array(
			'type' => 'text',
			'caption' => 'Hora inicia',
			'detailField' => 'ot.from_hour',
		),
		'hora_final' => array(
			'type' => 'text',
			'caption' => 'Hora final',
			'detailField' => 'ot.to_hour',
		)
	),
	'details' =>array( 
	),
	'default' => array(
	)
);


?>