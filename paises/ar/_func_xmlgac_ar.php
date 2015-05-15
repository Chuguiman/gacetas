<?php

	require_once ("../../db/dbx.php");
	require_once ("../../clases/_func_tranformar_gac_ext.php");
	require_once ("_func_html2xmlgac_ar.php");

	function gacxml2_ar ($ngac, $fechapub) {
		
		$plazo_opo = date('Y-m-d',strtotime('+30 days', strtotime($fechapub)));
		$archivo1 = "origen/".$ngac.".xml";

		//Abre Archivo de lista de Denominaciones para Lectura
	    $gestor = fopen($archivo1, "rb");
	    $contenido = stream_get_contents($gestor);
	    fclose($gestor); 

	    //Expresiones Regulares 1 (limpia Texto)
	    $patrones[0] = "/<page(.*?)>/";
	    $patrones[1] = "#</page>#";
	    $patrones[2] = "/<fontspec(.*?)>/";
	    $patrones[3] = "/<text (.*?)>/";
	    $patrones[4] = "/<i>/";
	    $patrones[5] = "/<b>/";
	    $patrones[6] = "/<text>P(.*?)gina:(.*?)>/";    
	    $patrones[7] = "/\s+/";//Quita Doble espacio
	    $patrones[8] = "#> <#";
	    $patrones[9]= "/> \(/";
	    $patrones[10]= "# </i>|</i>#";
	    $patrones[11]= "# </text>#";
	    $patrones[12]= "# </b>|</b>#";
	    $patrones[13] = "/<text> /";
	    $patrones[14] = "#<text>\r</text>#";
	    $patrones[15] = "/<text>BOLET(.*?)N DE MARCAS/";
	    $patrones[16] = "/Acta /";
	    $patrones[17] = "/- \(51\)/";
	    $patrones[18] = "/ \(54\)/";
	    $patrones[19] = "/ \(73\)/";
	    $patrones[20] = "/ \(44\)/";
	    $patrones[21] = "/<text>\(44\) /";
	    	    
	    array_push($patrones, "/\s+/","#> <#");
	    $reemplazos=array("","","","<text>","","", ""," ",">\r<",">(", "", "</text>","","<text>","","<text>(556)","","</text>\r<text>51</text>\r<text>","</text>\r<text>54</text>\r<text>"
	    					,"</text>\r<text>73</text>\r<text>","</text>\r<text>44</text>\r<text>","<text>44</text>\r<text>");
	    
	    array_push($reemplazos, " ",">\r<");    
	    
	    $contenido = preg_replace($patrones, $reemplazos, $contenido);
		$mcods=array("556","21","22","51","54","57","59","73","74","30","40","44");
	    for($i=0; $i<sizeof($mcods)-1; $i++){
	        $patron="/>\((".$mcods[$i].")\) /";
	        $contenido = preg_replace($patron, ">$1</text>\r<text>", $contenido);        
	    }

		$contenido = str_replace("'", "\'", $contenido);

	    //echo $contenido;

	    $ant = $act = "";
	    $xml = new SimpleXMLElement($contenido);
				
		$datosbasicos = array();
		$datosgenerales = array();
		$j = -1;
	    foreach ($xml->text as $texto) {        
	        $ant2= $ant;
			$ant = $act;
			$act = "";      
	        $texto=trim($texto);
	        //echo $texto."\n";
	        switch ($texto) {
				case "21":
				$act = "exp";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
					}
					$j++;
					break;
				case "51":
					$act = "clases";
					break;
				case "40":
					$act = "tipomarca";
					break;			
				case "22":
					$act = "fechapres";
					break;
				case "57":
					$act = "pys";
					break;	
				case "74":
					$act = "codapo";
					break;
				case "44":
					$act = "fechapub";
					break;			
				case "73":
					$act = "titulardom";
					break;
				case "30":
					$act = "prioridad";
					break;
				case "59":
					$act = "rein";
					break;			
				case "54":
					$act = "denomi";
					break;
				case "556":
					$act = "pag";
					break;										
				default:
					if ($act =="" & $ant !="") {
						$datosbasicos[$ant] = trim($texto);
					}elseif ($act =="" & $ant =="") {
						$ant = $ant2;
						$datosbasicos[$ant].=" ". trim($texto);
					}
					break;	
			}		

		}
		
		// echo "<pre>\n";
		// var_dump($datosgenerales);
		// print_r($datosgenerales);

		$tarray = count($datosgenerales);
		echo "<br>Cant. de Exp: ".$tarray."<br>";

		foreach ($datosgenerales as $datoadato) {
			
			$expediente = substr(($datoadato["exp"]), 0, 9);
			$expediente = str_replace(".", "", $expediente);
			$clases = $datoadato["clases"];
			$clases = str_replace("Clase", "", $clases);
			$tipomarca = $datoadato["tipomarca"];
			$denomi = $datoadato["denomi"];
			$fechapres = $datoadato["fechapres"];
			$titular = $datoadato["titulardom"];
			$titular = preg_replace("# ?(- +){1,2}.*#", "", $titular);
			$domicilio = substr(($datoadato["titulardom"]), -4);
			$domicilio = str_replace("*", "", $domicilio);
			$pys = $datoadato["pys"];
			$prioridad = $datoadato["prioridad"];
			$codapo = $datoadato["codapo"];
			$codapo = str_replace("Ag ", "", $codapo);
			$codapo = str_replace("-", "", $codapo);

			if (!$expediente=="") {
				$sql1 = "INSERT INTO 
							`sam_descar_gac_ar` (`nreg`) 
						VALUES 
							('$expediente')";
				$rsql1 = mysql_query($sql1);
				$sqlx = "INSERT INTO  
							`sam_precarga_gac_exterior` (`gaceta`,`fecha_publicacion`,`plazo_opo`) 
						VALUES 
							('$ngac','$fechapub','$plazo_opo')";
				$rsqlx = mysql_query($sqlx);
			}

			
			
		}		
		echo "OK --- Insert ---<br>";

			
	}

	

?>