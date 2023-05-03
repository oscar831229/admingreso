<?php


$notificacion_compromisos_responsable = array(

	'source' => 'mdc_meetings AS mm',
	'relationTable' => array(
	),
	'where' => array(
		'mm.id' => '1'
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
		)
	),
	'details' =>array( 
	),
	'default' => array(
	)
);


?>