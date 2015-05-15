<?php
	
	require_once ("../../clases/_func_tranformar_gac_ext.php");
	require_once ("../../db/dbx.php");

	function transforma_gacec($ngac, $fechapub, $archivo) {
		
		$plazo_opo = date('Y-m-d',strtotime('+30 days', strtotime($fechapub)));
		$archivo = "origen/".$ngac.".xml";

		//Abre Archivo para Lectura
	    $gestor = fopen($archivo, "rb");
	    $contenido = stream_get_contents($gestor);
	    fclose($gestor); 

	    //Expresiones Regulares 1 (limpia Texto)
	    $patrones[0] = "/<Page (.*?)>/";
	    $patrones[1] = "#</Page>#";
	    $patrones[2] = "/\s+/";//Quita Doble espacio
	    $patrones[3] = "#> <#";
	    $patrones[4] = "/> \(/";
	    $patrones[5] = "#<Pages>|</Pages>|powered by tcpdf \(www.tcpdf.org\)#";
	    $patrones[6] = "/signo de solicitud: /";
	    $patrones[7] = "#<Search></text>#";
	    $patrones[8] = "#<text>mixto #";
	    $patrones[9] = "#<text>denominativo #";
	    $patrones[10] = "#<text>figurativo #";
	    $patrones[11] = "#<text>tridimensional #";
	    $patrones[12] = "#marca de producto país: |marca de servicios país: #";
   	    $patrones[13] = "#nombre comercial país: #";
   	    $patrones[14] = "#lema comercial país: #";
   	    $patrones[15] = "#\(511\)#";
   	    $patrones[16] = "#\(220\)#";
   	    $patrones[17] = "#\(210\)#";
   	    $patrones[18] = "#\(730\)#";
   	    $patrones[19] = "#\(740\)#";
   	    $patrones[20] = "#productos: |servicios: |actividades: #";
   	    $patrones[21] = "#602 - marzo - 2015#";
   	    $patrones[22] = "#<text> #";
   	    $patrones[23] = "# </text>#";
   	    $patrones[24] = "#<text></Search>#";

	    array_push($patrones, "/\s+/","#> <#");
	    $reemplazos=array("","-finpage</text>\r<text>"," ",">\r<",">(","","</text>\r<text>======================</text>\r<text>(40)</text>\r<text>","<Search>\r","<text>mixto</text>\r<text>(54)</text>\r<text>",
	    					"<text>denominativo</text>\r<text>(54)</text>\r<text>","<text>figurativo</text>\r<text>(54)</text>\r<text>","<text>tridimensional</text>\r<text>(54)</text>\r<text>",
	    					"</text>\r<text>(55)</text>\r<text>M</text>\r<text>(731)</text>\r<text>","</text>\r<text>(55)</text>\r<text>NC</text>\r<text>(731)</text>\r<text>",
	    					"</text>\r<text>(55)</text>\r<text>L</text>\r<text>(731)</text>\r<text>","</text>\r<text>(511)</text>\r<text>","</text>\r<text>(220)</text>\r<text>",
	    					"</text>\r<text>(210)</text>\r<text>","</text>\r<text>(730)</text>\r<text>","</text>\r<text>(740)</text>\r<text>","</text>\r<text>(57)</text>\r<text>",
	    					"</text>\r<text>(3333)</text>\r<text>","<text>","</text>","<text></text>\r</Search>");
	    
	    array_push($reemplazos, " ",">\r<");    
	    $contenido = preg_replace($patrones, $reemplazos, $contenido);
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
				case "======================":
				$act = "======================";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
					}
					$j++;
					break;
				case "(40)":
					$act = "tipomarca";
					break;	
				case "(54)":
					$act = "denomi";
					break;
				case "(55)":
					$act = "signo";
					break;		
				case "(731)":
					$act = "dom";
					break;
				case "(511)":
					$act = "clases";
					break;
				case "(220)":
					$act = "fechapres";
					break;
				case "(210)":
					$act = "exp";
					break;
				case "(730)":
					$act = "titular";
					break;
				case "(740)":
					$act = "apoderado";
					break;
				case "(57)":
					$act = "pys";
					break;
				case "(3333)":
					$act = "sltpag";
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
		
		//echo "<pre>\n";
		//var_dump($datosgenerales);
		//print_r($datosgenerales);	

		$tarray = count($datosgenerales);
		echo "<br>Cant. de Exp: ".$tarray."<br>";
		echo "<br>Sin Expedientes (errados):<br>";
		
		foreach ($datosgenerales as $datoadato) {
			
			$exp = $datoadato["exp"];
			$exp = preg_replace('/[^0-9]/', '$1', $exp);
			$fechapres = substr(($datoadato["fechapres"]),0,10);
			$signo = $datoadato["signo"];
			$denomi = utf8_decode(strtoupper($datoadato["denomi"]));
			$tipomarca = strtoupper($datoadato["tipomarca"]);
			$clases = $datoadato["clases"];
			$titular = utf8_decode(strtoupper($datoadato["titular"]));
			$dom = utf8_decode(strtoupper($datoadato["dom"]));
			$apoderado = utf8_decode(strtoupper($datoadato["apoderado"]));
			$pys = utf8_decode(strtoupper($datoadato["pys"]));

			if (!$exp=="") {
				$sql = "INSERT INTO 
							`sam_precarga_gac_exterior` 
							(`expediente`, `clases`,`fecha_solicitud`, `denominacion`,`tipo_denomi`, `tipomarca`,`titular`, `domicilio`,`apoderado`, `prodyservs`, `gaceta`,`fecha_publicacion`, `plazo_opo`) 
						VALUES 
							('$exp', '$clases','$fechapres', '$denomi', '$signo', '$tipomarca','$titular', '$dom','$apoderado', '$pys','$ngac', '$fechapub','$plazo_opo');";
				$rsql= mysql_query($sql);
				//echo "<br>".$sql;
			}elseif($exp==""){
				$sqlx = "INSERT INTO 
							`sam_precarga_gac_exterior` 
							(`expediente`, `clases`,`fecha_solicitud`, `denominacion`,`tipo_denomi`, `tipomarca`,`titular`, `domicilio`,`apoderado`, `prodyservs`, `gaceta`,`fecha_publicacion`, `plazo_opo`) 
						VALUES 
							('0', '$clases','$fechapres', '$denomi', '$signo', '$tipomarca','$titular', '$dom','$apoderado', '$pys','$ngac', '$fechapub','$plazo_opo');";
				$rsqlx= mysql_query($sqlx);
				//echo "<br>".$sqlx;

				echo "<br> ".$exp." | ".$fechapres." | ".$signo." | ".$denomi." | ".$clases." | ".$titular." | ".$dom." | ".$apoderado;
			}
			

		}

		echo "<br> 	<br> OK <br>";
		
		
	}    

?>