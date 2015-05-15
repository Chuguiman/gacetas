<?php

	require_once ("../../clases/_func_tranformar_gac_ext.php");
	require_once ("../../db/dbx.php");

	function gacxml2_uy ($ngac, $fechapub) {
		
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
	    $patrones[10]= "# </i>|</i>|\(#";
	    $patrones[11]= "# </text>#";
	    $patrones[12]= "# </b>|</b>#";
	    $patrones[13] = "/<text> /";
	    $patrones[14] = "#<text>\r</text>#";
	    $patrones[15] = "/<text>(\d{6})/";
	    $patrones[16] = "/BOLETIN DE LA PROPIEDAD INDUSTRIAL/";
	    $patrones[17] = "/mixta/";
	    $patrones[18] = "/figurativa/";
	    $patrones[19] = "/tridimensional/";
	    	    
	    array_push($patrones, "/\s+/","#> <#");
	    $reemplazos=array("","","","<text>","","", ""," ",">\r<",">(", "", "</text>","","<text>","","<text>EXP:</text>\r<text>$1</text>\r<text>DENOMI:</text>\r<text>",
	    					"</text>\r<text>XXX:</text>\r<text>","</text>\r<text>TIP:</text>\r<text>MIXTA</text>\r<text>","</text>\r<text>TIP:</text>\r<text>FIGURATIVA</text>\r<text>",
	    					"</text>\r<text>TIP:</text>\r<text>TRIDIMENSIONAL</text>\r<text>");
	    
	    array_push($reemplazos, " ",">\r<");    
	    $contenido = preg_replace($patrones, $reemplazos, $contenido);

	    $contenido = str_replace("<text>)</text>", "<text>TITULAR:</text>", $contenido);
	    //$contenido = preg_replace('/&[^; ]{0,6}.?/e', "((substr('\\0',-1) == ';') ? '\\0' : '&amp;'.substr('\\0',1))", $contenido);
	    $contenido = str_replace("'", "\'", $contenido);
	    //echo $contenido;

	    $ant = $act = "";
	    $xml = new SimpleXMLElement($contenido);
				
		$datosbasicos = array();
		$datosgenerales = array();
		$j = 0;
	    
	    foreach ($xml->text as $texto) {        
	        $ant2= $ant;
			$ant = $act;
			$act = "";      
	        $texto=trim($texto);
	        //echo $texto."\n";
	        switch ($texto) {
				case "EXP:":
				$act = "exp";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
					}
					$j++;
					break;
				case "DENOMI:":
					$act = "denomi";
					break;
				case "XXX:":
					$act = "bas";
					break;			
				case "TIP:":
					$act = "tipomarca";
					break;
				case "TITULAR:":
					$act = "titular";
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
		/*echo "<pre>\n";
		var_dump($datosgenerales);*/
		
		/*echo "<pre>\n";
		print_r($datosgenerales);	*/

		foreach ($datosgenerales as $datoadato) {
			
			$expediente = substr(($datoadato["exp"]), 0, 6);
			$tipomarca = $datoadato["tipomarca"];
			$denominacion = $datoadato["denomi"];

			if (!$expediente=="") {
				$sql = "INSERT INTO `sam_precarga_gac_exterior` (`denominacion`,`tipomarca`, `expediente`, `gaceta`, `fecha_publicacion`, `plazo_opo`) VALUES ('".$denominacion."', '".$tipomarca."', '".$expediente."', '".$ngac."', '".$fechapub."', '".$plazo_opo."');";
				$rsql = mysql_query(utf8_decode($sql));
				//echo "<br>".$sql;
			}
			
			
		}		
		echo "OK --- Insert de lista de Expedientes y Tipo Marca M, F, T y S ---<br>";

		/*===================================================================================================*/
		$archivo2 = "origen/".$ngac."m.xml";

		//Abre Archivo de Marcas para Lectura
	    $gestor2 = fopen($archivo2, "rb");
	    $contenido2 = stream_get_contents($gestor2);
	    fclose($gestor2); 

	    $patrones2[0] = "/<page(.*?)>/";
	    $patrones2[1] = "#</page>#";
	    $patrones2[2] = "/<fontspec(.*?)>/";
	    $patrones2[3] = "/<text (.*?)>/";
	    $patrones2[4] = "/<i>/";
	    $patrones2[5] = "/<b>/";
	    $patrones2[6] = "/<text>P(.*?)gina:(.*?)>/";    
	    $patrones2[7] = "/\s+/";//Quita Doble espacio
	    $patrones2[8] = "#> <#";
	    $patrones2[9]= "/> \(/";
	    $patrones2[10]= "# </i>|</i>#";
	    $patrones2[11]= "# </text>#";
	    $patrones2[12]= "# </b>|</b>#";
	    $patrones2[13] = "/<text> /";
	    $patrones2[14] = "#<text>\r</text>#";

	    array_push($patrones2, "/\s+/","#> <#");
	    $reemplazos2=array("","","","<text>","","", ""," ",">\r<",">(", "", "</text>","","<text>","");
	    
	    array_push($reemplazos2, " ",">\r<");    
	    $contenido2 = preg_replace($patrones2, $reemplazos2, $contenido2);

	    $mcods2=array("210","730","220","511","540","551","554","730","740","300","556","591");
	    for($i=0; $i<sizeof($mcods2)-1; $i++){
	        $patron2="/>\((".$mcods2[$i].")\) /";
	        $contenido2 = preg_replace($patron2, ">$1</text>\r<text>", $contenido2);        
	    }
	    $contenido2 = str_replace("<text>(540)</text>", "<text>540</text>", $contenido2);
	    $contenido2 = str_replace("<text>(591) ", "<text>591</text>\r<text>", $contenido2);
	    $contenido2 = str_replace(" (S/D) y ", ", ", $contenido2);
	    $contenido2 = str_replace(" (S/D) ", "", $contenido2);
	    $contenido2 = str_replace(" (S/D),", ",", $contenido2);
	    $contenido2 = preg_replace('/&[^; ]{0,6}.?/e', "((substr('\\0',-1) == ';') ? '\\0' : '&amp;'.substr('\\0',1))", $contenido2);
	    $contenido2 = str_replace("'", "\'", $contenido2);
	    //echo $contenido2;

	    $ant = $act = "";
	    $xml = new SimpleXMLElement($contenido2);
				
		$datosbasicos = array();
		$datosgenerales = array();
		$j = 0;
	    
	    foreach ($xml->text as $texto) {        
	        $ant2= $ant;
			$ant = $act;
			$act = "";      
	        $texto=trim($texto);
	        switch ($texto) {
				case "210":
				$act = "exp";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
					}
					$j++;
					break;
				case "540":
					$act = "denomi2";
					break;
				case "730":
					$act = "titular";
					break;			
				case "220":
					$act = "fechapres";
					break;
				case "511":
					$act = "clases";
					break;
				case "740":
					$act = "codapoderado";
					break;
				case "300":
					$act = "prioridad";
					break;
				case "591":
					$act = "revcolor";
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
		/*echo "<pre>\n";
		var_dump($datosgenerales);*/
		
		/*echo "<pre>\n";
		print_r($datosgenerales);	*/

		foreach ($datosgenerales as $datoadato) {
			
			$expediente = substr(($datoadato["exp"]), 0, 6);
			$titular = $datoadato["titular"];
			$titular = preg_replace("# ?(; +){1}.*#", "", $titular);
			$domicilio = substr(($datoadato["titular"]), -2);
			$fechapres = $datoadato["fechapres"];
			$clases = $datoadato["clases"];
			$clases = str_replace(" (S/D),", ",", $clases);
			$clases = str_replace(" y ", ", ", $clases);
			$clases = str_replace(" (S/D)", "", $clases);
			$codapoderado = $datoadato["codapoderado"];
			$denominacion = $datoadato["denomi2"];
			$prioridad = $datoadato["prioridad"];

			$sql3 = "SELECT a.`expediente` AS X, a.`tipomarca` AS TPM  FROM `sam_precarga_gac_exterior` AS a WHERE a.`expediente` = '$expediente';";
			$rsql3 = mysql_query ($sql3);
				
			if(mysql_num_rows($rsql3)>0){
				$expexiste=mysql_result($rsql3,0,"X");
				$sql2 = "UPDATE `sam_precarga_gac_exterior` SET `fecha_solicitud`='$fechapres', `prioridad`='$prioridad', `titular`='$titular', `domicilio`='$domicilio', `apoderado`='$codapoderado', `clases`='$clases' WHERE (`expediente`='$expexiste');";
				mysql_query(utf8_decode($sql2));
				//echo "<br>".$sql2;
				
				$tipomarcav=mysql_result($rsql3,0,"TPM");
				if ($tipomarcav=="") {
					$sql7 = "UPDATE `sam_precarga_gac_exterior` SET `denominacion` = '$denominacion', `tipomarca` = 'NOMINATIVA' WHERE (`expediente`='$expexiste');";
					mysql_query(utf8_decode($sql7));
					//echo "<br>".$sql7;
				}

			}elseif(!$expediente==""){
				add_marca($np, utf8_decode($denominacion), $signo, $tipomarca, $expediente, $fechapres, utf8_decode($titular), $direccion, utf8_decode($domicilio),  utf8_decode($codapoderado), $dir_apo, $clases, $ngac, $fechapub, $plazo_opo, utf8_decode($prioridad), utf8_decode($prodyservs));

			}

			$sql4 = "SELECT a.apoderado as X FROM sam_precarga_gac_exterior AS x , apoderados_uy AS a WHERE x.apoderado = a.cod_apo;";
			$rsql4 = mysql_query ($sql4);
			
			if(mysql_num_rows($rsql4)>0){
				$nom_apo=mysql_result($rsql4,0,"X");
				$sql5 = "UPDATE `sam_precarga_gac_exterior` SET `apoderado` = '$nom_apo' WHERE (`expediente`='$expediente');";
				mysql_query ($sql5);
			}


		}		
		echo "<br>OK --- Insert otros datos y Actualización de Apoderados ---<br>";

	}


?>