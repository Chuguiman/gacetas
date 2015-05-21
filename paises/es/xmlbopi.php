<?php
	
	require_once ("../../clases/_func_tranformar_gac_ext.php");
	require_once ("../../db/dbx.php");

	//$fecha_publicacion = "2015-04-27";

	$fecha_publicacion = $_POST['fechapub'];
	$plazo_opo = date('Y-m-d',strtotime('+60 days', strtotime($fecha_publicacion)));

	$ngac = $_POST['ngac'];
	//$ngac = "27042015";
	$archivo = "origen/".$ngac.".xml";

	$gestor = fopen($archivo, "rb");
	$contenido = stream_get_contents($gestor);
	fclose($gestor); 

	//Expresiones Regulares 1 (limpia Texto)
	$patrones[0] = "/<tns:/";
	$patrones[1] = "#</tns:#";
	$patrones[2] = "/<Tomo1 (.*?)>/";
	$patrones[3] = '/<imagen>.*?<\/imagen>/si';
	$patrones[4] = '/<Bis>.*?<\/Bis>/si';
	
	
	
	array_push($patrones, "/\s+/","#> <#");
	$reemplazos=array("\r<","</","<Tomo1>",'<imagen></imagen>','');
	    
	array_push($reemplazos, " ",">\r<");    
	$contenido = preg_replace($patrones, $reemplazos, $contenido);
	$contenido = str_replace("'", "\'", $contenido);
	//echo $contenido;
	
	$xmltrs = $ngac."c.xml";
	$carpeta = "origen/";
	save_txtinfile($carpeta.$xmltrs, $contenido);
			
	$archorginen = $carpeta.$xmltrs;

	if (file_exists($archorginen)) {
		$xml = simplexml_load_file($archorginen);
	}else{
		exit("No se puede cargar el archivo xml!");
	}

	/*echo "<pre>";
	print_r($xml);*/
	

	foreach ($xml->Marcas as $marcas) {
		foreach ($marcas->SolicitudesMarcas as $smarcas) {
			/*echo "<pre>";
			var_dump($smarcas);*/
			foreach ($smarcas->SolicitudMarca as $marca) {
				$np = $marca->PublicacionId;
				$signo = $marca->Modalidad;
				$exp = $marca->p21_NumSolicitud;
				$titular = $marca->ApellidosTitular;
				$titular = utf8_decode($titular);
				$fechapres = $marca->FechaDepositoRegular;
				$tipomarca = $marca->TipoSigno;
				$direccion = $marca->Domicilio;
				$direccion = utf8_decode($direccion);
				$codpais = $marca->PaisDeResidencia;
				$denomi = $marca->Denominacion;
				$denomi = utf8_decode($denomi);		
				$pyss = $marca->ProductosServiciosActividades;

				foreach ($pyss as $pys) {
					$clases = preg_replace("/[^0-9]/", "$1", $pys);
					$pys = preg_replace("/[0-9]/", "$1", $pys);
					$sql = "INSERT INTO 
							`sam_precarga_gac_exterior` 
								(`expediente`,`np`,`clases`,`tipo_denomi`,`domicilio`,`tipomarca`,`gaceta`,`fecha_publicacion`,`plazo_opo`,`fecha_solicitud`,`prodyservs`,`denominacion`,`titular`,`direccion`) 
						VALUES 
							('$exp','$np','$clases','$signo','$codpais','$tipomarca','$ngac','$fecha_publicacion','$plazo_opo','$fechapres','$pys','$denomi','$titular','$direccion');";
					$rsql = mysql_query($sql);
				}

				
			}

			
		}
	}

	echo "<br>Insertadas Marcas";

	foreach ($xml->NombresComerciales as $marcas) {
		foreach ($marcas->NCSolicitudes as $smarcas) {
			/*echo "<pre>";
			var_dump($smarcas);*/
			foreach ($smarcas->SolicitudNC as $marca) {
				$np = $marca->PublicacionId;
				$signo = $marca->Modalidad;
				$exp = $marca->p21_NumSolicitud;
				$titular = $marca->ApellidosTitular;
				$titular = utf8_decode($titular);
				$fechapres = $marca->FechaDepositoRegular;
				$tipomarca = $marca->TipoSigno;
				$direccion = $marca->Domicilio;
				$direccion = utf8_decode($direccion);
				$codpais = $marca->PaisDeResidencia;
				$denomi = $marca->Denominacion;
				$denomi = utf8_decode($denomi);		
				$pyss = $marca->ProductosServiciosActividades;

				foreach ($pyss as $pys) {
					$clases = preg_replace("/[^0-9]/", "$1", $pys);
					$pys = preg_replace("/[0-9]/", "$1", $pys);
					$sql = "INSERT INTO 
							`sam_precarga_gac_exterior` 
								(`expediente`,`np`,`clases`,`tipo_denomi`,`domicilio`,`tipomarca`,`gaceta`,`fecha_publicacion`,`plazo_opo`,`fecha_solicitud`,`prodyservs`,`denominacion`,`titular`,`direccion`) 
						VALUES 
							('$exp','$np','$clases','$signo','$codpais','$tipomarca','$ngac','$fecha_publicacion','$plazo_opo','$fechapres','$pys','$denomi','$titular','$direccion');";
					$rsql = mysql_query($sql);
				}
				

			}

			
		}
	}

	echo "<br>Insertadas Nombres Comerciales";

	unlink($archorginen);

	echo "<br>Vaciada Tabla<br>";

	$pais = "BOPI";
	crea_xml_gac ($pais, $ngac);

	$sqlx = "TRUNCATE TABLE `sam_precarga_gac_exterior`;";
	mysql_query($sqlx);	

?>