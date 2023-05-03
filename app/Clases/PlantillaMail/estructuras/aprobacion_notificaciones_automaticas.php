<?php


$aprobacion_notificaciones_automaticas = array(

	'source' => 'notify_info_tokens',
	'relationTable' => array(
	),
	'where' => array(
		'id' => '1'
	),
	'components' => array(
		'email' => array(
			'caption' => 'Email responsable',
			'detailField' => 'email'
		),
		'responsible_person' => array(
			'type' => 'text',
			'caption' => 'Nombre responsable',
			'detailField' => 'responsible_person',
			'valueType' => 'textUpper'
		),
		'token' => array(
			'type' => 'text',
			'caption' => 'Token',
			'detailField' => 'token',
		)
	),
	'details' =>array( 
	),
	'default' => array(
	)
);


?>