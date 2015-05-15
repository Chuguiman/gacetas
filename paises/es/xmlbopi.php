<?php
	
	require_once ("../../clases/_func_tranformar_gac_ext.php");

	$fecha_publicacion = $_POST['fechapub'];
	$plazo_opo = date('Y-m-d',strtotime('+60 days', strtotime($fecha_publicacion)));

	//$ngac = $_POST['ngac'];
	$ngac = "27042015";
	$archorginen = "origen/".$ngac.".xml";

	
	if (file_exists($archorginen)) {
		$xml = simplexml_load_file($archorginen);
	} else{
		exit("No se puede cargar el archivo xml!");
	}

	echo "<pre>";
	print_r($xml);

	foreach ($xml->tns as $processo) {
		$exp = $processo['p21_NumSolicitud'];
		echo $exp;
		

			//add_marca($np, utf8_decode($denomi),  utf8_decode($signo),  utf8_decode($tipomarca), $exp, $fecha_pres, utf8_decode($titular),  utf8_decode($direccion), utf8_decode($domicilio),  utf8_decode($apoderado), $dir_apo, $clasniza, $ngac, $fecha_publicacion, $plazo_opo, utf8_decode($prioridad), utf8_decode($prodyservs));			
		

		
	}
	/*
	$pais = "BR";
	crea_xml_gac ($pais, $ngac);*/

?>