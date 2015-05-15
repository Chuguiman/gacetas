<?php
	
	require_once ("../../clases/_func_tranformar_gac_ext.php");

	$fecha_publicacion = $_POST['fechapub'];
	$plazo_opo = date('Y-m-d',strtotime('+60 days', strtotime($fecha_publicacion)));

	$ngac = $_POST['ngac'];
	$archorginen = "origen/".$ngac.".xml";

	
	if (file_exists($archorginen)) {
		$xml = simplexml_load_file($archorginen);
	} else{
		exit("No se puede cargar el archivo xml!");
	}

	/*echo "<pre>";
	print_r($xml);*/

	foreach ($xml->processo as $processo) {
		$exp = $processo['numero'];
		$fecha_pres = $processo{'data-deposito'};
		$codpros = $processo->despachos->despacho['codigo'];
		$titular = $processo->titulares->titular['nome-razao-social'];
		$domicilio = $processo->titulares->titular['pais'];
		$tipomarca = $processo->marca['apresentacao'];
		$tipodenomi = $processo->marca['natureza'];
		$denomi = $processo->marca->nome;
		$clasniza = $processo->{'classe-nice'}['codigo'];
		$apoderado = $processo->procurador;
		$pys = $processo->{'classe-nice'}->especificacao;
		$cfe = $processo->{'classes-vienna'}->{'classe-vienna'};
		$cfecant = $processo->{'classes-vienna'}['edicao'];

		if ($codpros=="IPAS009") {
				$exp;
				$fecha_pres;
				$tipodenomi;
				$denomi;
				$tipomarca;
				$clasniza;
				$titular;
				$domicilio;
				$apoderado;
				$cfe;
				
				//."<br>PYS: ".$pys;
				
				for ($x=0; $x<count($cfe) ; $x++) { 
					$cfe[$x]['codigo'].", ";
				}
		

			add_marca($np, utf8_decode($denomi),  utf8_decode($signo),  utf8_decode($tipomarca), $exp, $fecha_pres, utf8_decode($titular),  utf8_decode($direccion), utf8_decode($domicilio),  utf8_decode($apoderado), $dir_apo, $clasniza, $ngac, $fecha_publicacion, $plazo_opo, utf8_decode($prioridad), utf8_decode($prodyservs));			
		}

		
	}

	$pais = "BR";
	crea_xml_gac ($pais, $ngac);

?>