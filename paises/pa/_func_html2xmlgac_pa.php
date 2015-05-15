<?php
	
	require_once ("../../clases/_func_tranformar_gac_ext.php");

	function transforma_gacpa($ngac, $fechapub, $archivo) {
		
		$plazo_opo = date('Y-m-d',strtotime('+60 days', strtotime($fechapub)));
		$archivo = "origen/".$ngac.".xml";

		//Abre Archivo para Lectura
	    $gestor = fopen($archivo, "rb");
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
	    $patrones[10]= "# </i>#";
	    $patrones[11]= "# </text>#";
	    $patrones[12] = "/01 Tipo:/";
	    $patrones[13]= "# </b>#";
	    $patrones[14]= "#</b>#";
	    $patrones[15]= "#</i>#";
	    $patrones[16] = "/<text> /";
	    $patrones[17] = "#<text>\r</text>#";
	    $patrones[18] = "/EXAMINADOR RESPONSABLE:/";
	    $patrones[19] = "# </text>#";
	    $patrones[20] = "/ 5 7 |5 7|57/";
  		$patrones[21] = "/Mixta/";
  		$patrones[22] = "/<text>\[57]<\/text>|<text>\[57]/";
  		$patrones[23] = "/Denominaci(.*?)n Comercial/";
  		$patrones[24] = "/ 2 1 |2 1|21/";
  		$patrones[25] = "/<text>\[21]<\/text>/";
  		$patrones[26] = "/Figurativa/";
  		$patrones[27] = "/Denominativa/";
  		$patrones[28] = "/<text>\r<text>/";
  		$patrones[29] = "#<text>MICI BOLETIN N°319 8 DE ABRIL DE 2015</text>#";//
  		$patrones[30] = "/DIGERPI/";
  		$patrones[31] = "/ 5 4 |5 4|54/";
  		$patrones[32] = "/ 2 2 |2 2|22/";
  		$patrones[33] = "/ 3 0 |3 0|30/";
  		$patrones[34] = "/ 5 1 |5 1|51/";
  		$patrones[35] = "/ 5 8 |5 8|58/";
  		$patrones[36] = "/ 7 3 |7 3|73/";
  		$patrones[37] = "/ 7 4 |7 4|74/";

	    
	    array_push($patrones, "/\s+/","#> <#");
	    $reemplazos=array("","","","<text>","","", ""," ",">\r<",">(", "", "</text>","</text>\r<text>900</text>\r<text>", "", "", "", "<text>","","</text>\r<text>600</text>\r<text>", "</text>", "57","1200</text>\r<text>Mixta</text>\r<text>","<text>57</text>\r<text>","900</text>\r<text>Denominacion Comercial</text>\r<text>","21","<text>21</text>","1200</text>\r<text>Figurativa</text>\r<text>","1200</text>\r<text>Denominativa</text>\r<text>","<text>","","</text><text>DIGERPI:","54"
	    					,"22","30","51","58","73","74");
	    
	    array_push($reemplazos, " ",">\r<");    
	    $contenido = preg_replace($patrones, $reemplazos, $contenido);
		
		$mcods=array("21","900","22","51","54","57","59","73","74","30","600","1200","DIGERPI:","58","=============================");
	    for($i=0; $i<sizeof($mcods)-1; $i++){
	        $patronx="/>\[(".$mcods[$i].")\] /";
	        $contenido = preg_replace($patronx, ">$1</text>\r<text>", $contenido);        
	    }

	    $contenido = preg_replace("#<text></text><text> DIGERPI </text>#", "<text> DIGERPI </text>", $contenido);

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
				case "=============================":
				$act = "=============================";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
					}
					$j++;
					break;
				case "21":
					$act = "exp";
					break;	
				case "22":
					$act = "fechapres";
					break;
				case "30":
					$act = "prioridad";
					break;		
				case "51":
					$act = "clases";
					break;
				case "54":
					$act = "denomi";
					break;
				case "57":
					$act = "pys";
					break;
				case "73":
					$act = "titularydom";
					break;
				case "74":
					$act = "apoderado";
					break;
				case "900":
					$act = "tipo_denomi";
					break;
				case "600":
					$act = "examinador";
					break;
				case "1200":
					$act = "tipomarca";
					break;
				case "DIGERPI:":
					$act = "basura";
					break;
				case "58":
					$act = "basura2";
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
		var_dump($datosgenerales);
		
		echo "</pre>";*/
		/*echo "<pre>\n";
		print_r($datosgenerales);	*/
		foreach ($datosgenerales as $datoadato) {
			
			$expediente = substr(($datoadato["exp"]), 0, 11);
			$expediente = str_replace(" ", "", $expediente);
			
			$clases     = $datoadato["clases"];
			
			/*if (!$denominacion=="") {
				$denominacion = strtoupper($datoadato["denomi"]);
			}else{
				$denominacion = "denomi-vacia";
			}*/

			$signo = strtoupper($datoadato["tipo_denomi"]);
			$tipomarca = strtoupper($datoadato["tipomarca"]);
			//$apoderado = strtoupper($datoadato["apoderado"]);
			
			$fecha_pres = strtoupper($datoadato["fechapres"]);
			$fecha_pres = str_replace("DE ENERO DE", "/01/", $fecha_pres);
			$fecha_pres = str_replace("DE FEBRERO DE", "/02/", $fecha_pres);
			$fecha_pres = str_replace("DE MARZO DE", "/03/", $fecha_pres);
			$fecha_pres = str_replace("DE ABRIL DE", "/04/", $fecha_pres);
			$fecha_pres = str_replace("DE MAYO DE", "/05/", $fecha_pres);
			$fecha_pres = str_replace("DE JUNIO DE", "/06/", $fecha_pres);
			$fecha_pres = str_replace("DE JULIO DE", "/07/", $fecha_pres);
			$fecha_pres = str_replace("DE AGOSTO DE", "/08/", $fecha_pres);
			$fecha_pres = str_replace("DE SEPTIEMBRE DE", "/09/", $fecha_pres);
			$fecha_pres = str_replace("DE OCTUBRE DE", "/10/", $fecha_pres);
			$fecha_pres = str_replace("DE NOVIEMBRE DE", "/11/", $fecha_pres);
			$fecha_pres = str_replace("DE DICIEMBRE DE", "/12/", $fecha_pres);
			$fecha_pres = str_replace(" ", "", $fecha_pres);
			
			//$titular = strtoupper($datoadato["titularydom"]);

			add_marca($np, utf8_decode($denominacion),  utf8_decode($signo),  utf8_decode($tipomarca), trim($expediente), $fecha_pres, utf8_decode($titular),  utf8_decode($direccion), utf8_decode($domicilio),  utf8_decode($apoderado), $dir_apo, $clases, $ngac, $fechapub, $plazo_opo, utf8_decode($prioridad), utf8_decode($prodyservs));	
			

		}

		echo "OK";
		
		$conn=mysql_connect('localhost','root','');
		mysql_select_db('gacetas',$conn);
		
		$sql="Select trim(a.expediente) AS X From sam_precarga_gac_exterior AS a";
		$rsql=mysql_query($sql);
		
		if(mysql_num_rows($rsql)>0){
			for($i=0; $i<mysql_num_rows($rsql); $i++){
				
				$numsigno=mysql_result($rsql, $i, "X");
				$numsigno*1;
				$dirsolweb="http://www.digerpi.gob.pa/pls/digerpi2/varios.dato_marcas?age_wk=0&usu_wk=0&solicitud_wk=".$numsigno."&secuencia_wk=01";
    			

    			if ($dirsolweb != "") {
			        //Contenido de la página
			        $cadsw = str_replace("'", "\'", $cadsw);        
			        $cadsw = ret_txtfile2($dirsolweb);       
			        $cadsw = reemplazaracentoshtml($cadsw);
			        $cadsw = corregir_otrosacentos($cadsw);
			        $cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); //Elimina Contenido del HEAD
			        $cadsw = preg_replace("/<script[^>]*?>.*?<\/script>/si", "", $cadsw);
			        $cadsw = preg_replace("/<a[^>]*?>.*?<\/a>/si", "", $cadsw);
			        $cadsw = preg_replace("/<input[^>]*?>/si", "", $cadsw);
			        $cadsw = preg_replace("/<BASE[^>]*?>/si", "", $cadsw);
			        $cadsw = preg_replace("/<img[^>]*?>/si", "", $cadsw);
			        $cadsw = preg_replace("/<embed[^>]*?>/si", "", $cadsw);
			        $cadsw = preg_replace("/<font[^>]*?>/si", "<font>", $cadsw);
			        $cadsw = preg_replace("/<body[^>]*?>/si", "<body>", $cadsw);
			        $cadsw = preg_replace("/<table[^>]*?>/si", "<table>", $cadsw);
			        $cadsw = preg_replace("/<td[^>]*?>/si", "<td>", $cadsw);
			        $cadsw = preg_replace("/<p[^>]*?>/si", "<p>", $cadsw);
			        $cadsw = str_replace("<?", "", $cadsw);
			        $cadsw = str_replace("?>", "", $cadsw);
			        $cadsw = str_replace('TARGET="_new"">', 'TARGET="_new">', $cadsw);
			        $bas = array('&nbsp;','&gt;','<b>','</b>','<br>','<BR>');
			        $cadsw = str_replace($bas, "", $cadsw);
			       	$cadsw = preg_replace('/\s(?=\s)/', '', $cadsw);
			       	$cadsw = str_replace("Pa&#237s", "Pais", $cadsw);
			       	$cadsw = str_replace("<TITLE>Consulta de Marcas</TITLE>", "<html>", $cadsw);
			       	$cadsw = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $cadsw);
			       	$cadsw = strip_tags($cadsw,"<font>");
			        $cadsw = utf8_encode($cadsw);
			        $cadsw = preg_replace('/\s(?=\s)/', '', $cadsw);
			        $cadsw = str_replace("<font>Nombre</font>", "<font>TITULAR</font>", $cadsw);
			        $cadsw = str_replace("<font>Domicilio</font>", "<font>DIRECCION</font>", $cadsw);
			        $cadsw = str_replace("<font>Pais</font>", "<font>PAIS</font>", $cadsw);
			        $cadsw = str_replace("<font>Estado</font>", "", $cadsw);
			        $cadsw = preg_replace("#<font>\nTitulares\n</font>#", "<titulares>\n", $cadsw);
			        $cadsw = preg_replace("#<font>\nProductos\n</font>#", "\n</titulares>", $cadsw);
			        $reed = array('<font>','</font>','><','<info> </info>','Limitar productos o servicios',' </info>','©','|','	</info>','<info> ','<info></info>','<info>
');
			        $reea = array('<info>','</info>','> <','','','</info>','','','</info>','<info>','');
			        $cadsw = str_replace($reed, $reea, $cadsw);
			        $cadsw = preg_replace('/&[^; ]{0,6}.?/e', "((substr('\\0',-1) == ';') ? '\\0' : '&amp;'.substr('\\0',1))", $cadsw);
			        $cadsw = preg_replace('/\s(?=\s)/', '', $cadsw);
			        
			        $xmltrs = $numsigno.".xml";
			        $carpeta = "origen/";
			        $cadsw = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r<pag>\r<marca>\r" . $cadsw . "</info>\r</marca>\r</pag>";
			        $cadsw = preg_replace('/\s(?=\s)/', '', $cadsw);
					save_txtinfile($carpeta.$xmltrs, $cadsw);

					$archorginen = $carpeta.$xmltrs;

					if (file_exists($archorginen)) {
						$xml2 = simplexml_load_file($archorginen);
					} else{
						exit("No se puede cargar el archivo xml!");
					}

					/*echo "<pre>\n";
					print_r($xml2);*/

					foreach ($xml2->marca as $marca) {
						$exp = $marca->info[3];
						$exp = str_replace("Solicitud", "", $exp);
						$exp = substr($exp, 0,7);

						$denominacion = $marca->info[6];
						$denominacion = str_replace("Nombre", "", $denominacion);

						$titularesx = $marca->titulares->info[3];
						// $titularesx = ereg_replace(" ?(, +){1,2}.*", "", $titularesx);

						$dirx = $marca->titulares->info[4];
						$paisx = $marca->titulares->info[5];

						$apo = $marca->info[14];
						$apo = str_replace("Abogado", "", $apo);

						if ($exp==$numsigno) {
							$sql1 = "UPDATE `sam_precarga_gac_exterior` SET `denominacion`='".$denominacion."', `titular`='".$titularesx."', `direccion`='".$dirx."', `domicilio`='".$paisx."', `apoderado`='".$apo."' WHERE (`expediente`='".$numsigno."');";
							$rsql1=mysql_query(utf8_decode($sql1));
						} else {
							echo '<br>'.$dirsolweb.'<---OK!<br></div>';
							echo "no actualiza";
						}
						
						
					}	

					unlink($archorginen);
					
				} 
		
			
			}

		}

		
		
	}    
	ini_set("auto_detect_line_endings", true);
?>