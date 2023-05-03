<?php


$notificacion_desprendibles_pago = array(

	'source' => 'VIE19.Payroll.Employee AS pe',
	'connection' => 'SIGH',
	'relationTable' => array(
		array(
			'tabla'=>'VIE19.Common.ThirdParty AS ct',
			'enlace_left'=>'ct.Id',
			'enlace_right'=>'pe.ThirdPartyId',
			'direccion'=>''
		)
	),
	'where' => array(
		'pe.Id' => '1'
	),
	'components' => array(
		'identificacion' => array(
			'caption' => 'Identificacion',
			'detailField' => 'ct.Nit'
		),
		'nombre_funcionario' => array(
			'type' => 'text',
			'caption' => 'Nombre funcionario',
			'detailField' => 'ct.Name',
			'valueType' => 'textUpper'
		)
	),
	'details' =>array( 
	),
	'default' => array(
	)
);


?>