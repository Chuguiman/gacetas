<?php
	require_once ("../../clases/_func_tranformar_gac_ext.php");
	require_once ("../../db/dbx.php");

	function gacxml2_mx ($ngac, $fechapub) {
		
		$plazo_opo = date('Y-m-d',strtotime('+30 days', strtotime($fechapub)));
		$archivo1 = "origen/".$ngac.".xml";

		//Abre Archivo de lista de Denominaciones para Lectura
	    $gestor = fopen($archivo1, "rb");
	    $contenido = stream_get_contents($gestor);
	    fclose($gestor); 

	    //Expresiones Regulares 1 (limpia Texto)
	    $patrones[0] = "/<page(.*?)>|<\/page>|<fontspec(.*?)>/";
	    $patrones[1] = "/<text (.*?)>|<text>P(.*?)gina:(.*?)>/";
	    $patrones[2] = "/\s+/";//Quita Doble espacio
	    $patrones[3] = "#> <#";
	    $patrones[4]= "/> \(/";
	    $patrones[5]= "#<i>|<b>| </i>|</i>| </b>|</b>|gaceta#";
	    $patrones[6]= "# </text>#";
	    $patrones[7] = "#<text>\r</text>#";
	    $patrones[8] = "#\[730\]#";
	    $patrones[9] = "#<text> #";
	    $patrones[10] = "/<text>\|<\/text>/";
	    $patrones[11] = "/Instituto Mexicano de la Propiedad Industrial/";

	    	    
	    array_push($patrones, "/\s+/","#> <#");
	    $reemplazos=array("","<text>"," ",">\r<",">[", "", "</text>","","730</text>\r<text>","\r<text>","<text>=============</text>","<text>=============</text>");
	    
	    array_push($reemplazos, " ",">\r<");    
	    $contenido = preg_replace($patrones, $reemplazos, $contenido);
	    
	    $mcods=array("111","151","210","220","540","510","570","730");
	    for($i=0; $i<sizeof($mcods)-1; $i++){
	        $patronx="/>\[(".$mcods[$i].")\] |>\[(".$mcods[$i].")\]/";
	        $contenido = preg_replace($patronx, ">$1</text>\r<text>", $contenido);        
	    }

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
				case "111":
				$act = "cert";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
					}
					$j++;
					break;
				case "151":
					$act = "fechareg";
					break;	
				case "210":
					$act = "exp";
					break;
				case "220":
					$act = "fechapres";
					break;		
				case "540":
					$act = "denomi";
					break;
				case "510":
					$act = "clases";
					break;
				case "570":
					$act = "pys";
					break;
				case "730":
					$act = "titularydom";
					break;
				case "=============":
					$act = "bas";
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
			$np = substr(($datoadato["cert"]), 0, 7);
			$exp = substr(($datoadato["exp"]), 0, 7);
			$clases = $datoadato["clases"];
			$denomi = $datoadato["denomi"];
			$fechapres = $datoadato["fechapres"];
			$titular = $datoadato["titularydom"];
			$titular = preg_replace("# ?(, +){1}.*#", "", $titular);
			$domicilio = substr(($datoadato["titularydom"]), -30);
			$domiciliox = explode(", ", $domicilio, 5);
			$ultimodom = end($domiciliox);
			$pys = $datoadato["pys"];

			//echo "<br>".$exp." | ".$np." | ".$fechapres." | ".$denomi." | ".$clases." | ".$titular." | ".$ultimodom;

			if (!$exp=="") {
				$sql = "INSERT INTO 
							`sam_precarga_gac_exterior` 
								(`expediente`,`np`,`clases`,`domicilio`,`gaceta`,`fecha_publicacion`,`plazo_opo`,`fecha_solicitud`,`prodyservs`,`denominacion`,`titular`,`tipo_denomi`) 
						VALUES 
							('$exp','$np','$clases','$ultimodom','$ngac','$fechapub','$plazo_opo','$fechapres','$pys','$denomi','$titular','MARCA');";
				$rsql = mysql_query(utf8_decode($sql));
				//echo "<br>".$sql;
			}else{
				echo "<br> ".$sql." es repetido";	
			}
			

			
			
		}		
	
	echo "<br>OK --- Insert ---<br>";

	/*==========================================Creamos XML==========================================*/

	$pais = "MX";
	crea_xml_gac ($pais, $ngac);

	/*==========================================Vaciamos la Tabla====================================*/
	$sqlx = "TRUNCATE TABLE `sam_precarga_gac_exterior`;";
	mysql_query($sqlx);	


	}    
?>
		