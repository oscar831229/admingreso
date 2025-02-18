<?php


$send_new_token = array(

	'source' => 'wallet_users',
	'relationTable' => array(

	),
	'where' => array(
		'id' => '1'
	),
	'components' => array(
        'numero_documento' => array(
			'caption' => 'Nombre usuario tiquetera',
			'detailField' => "document_number"
		),
		'nombre_usuario' => array(
			'caption' => 'Nombre usuario tiquetera',
			'detailField' => "CONCAT(IFNULL(first_name, ''), ' ', IFNULL(second_name, ''), ' ', IFNULL(first_surname, ''), ' ', IFNULL(second_surname, ''))"
		),
		'imgqr_base64' => array(
			'caption' => 'Token informes',
			'detailField' => 'imgqr',
		)
	),
	'details' =>array(
	),
	'default' => array(
	)
);


?>
