<?php

	require_once ("../../clases/_func_tranformar_gac_ext.php");
	
	function desc_actaweb_ar($signo, $carpeta) {

		$nacta = $signo; 
		$dirsolweb="https://portaltramites.inpi.gob.ar/Docs/ResultadosConsultas/ResultadoSolicitudMarca2.asp?Va=".$nacta;

	    echo '<br>'.$dirsolweb.'<---OK!<br></div>';
		
	    if (trim($nacta) != "") {
	        //Contenido de la página        
	        $cadsw = ret_txtfile2($dirsolweb);
			if (mb_detect_encoding($cadsw, 'ISO-8859-1', TRUE) !== 'UTF-8') {//ISO-8859-1
				$cadsw = utf8_decode($cadsw);
			}
			
			$cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); 
	 		$cadsw = strip_tags($cadsw,"<td>");
			$cadsw = trim($cadsw);
			$cadsw = str_replace("'","\'",$cadsw);
			
			
	        //Retornar Archivo
	        $txtfile  = $signo.".txt";
			$txtfile2 = $signo.".xml";
	        save_txtinfile($carpeta . "/" . $txtfile, $cadsw);
	        
			//Limpia
	        $gestor = @fopen($carpeta . "/" . $txtfile, "r");
	        if ($gestor) {
	            while (!feof($gestor)) {
	                $bufer.= fgetss($gestor, 4096, "<td>");
	            }
	            fclose($gestor);
	        }
			$bufer = utf8_encode($bufer);
			$bufer = trim($bufer);
			$bufer = str_replace("´","",$bufer);
	        $bufer = preg_replace('/[\s\t\n\r\f\0]/', ' ', $bufer);
	        $bufer = str_replace("  ", " ", $bufer);
	        $arrayareemplazar = array("&nbsp;", "&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&bull;");
	        $arrayareemplazos = array("", "á", "e", "í", "ó", "ú", "");
	        $bufer = str_replace($arrayareemplazar, $arrayareemplazos, $bufer); 
	        $bufer = preg_replace('/\s+/', ' ', $bufer);
	        $bufer = preg_replace("/<td[[:space:]]*([^>]*>)/", "<td>", $bufer);        
	        $bufer = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $bufer);
	        $bufer = preg_replace("/^[[:space:]]*([^<]*<)/", "<", $bufer);
	        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
	        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);  
	        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
	        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);
			$bufer = str_replace("<td> <td></td> <td></td> </td> <td>", "<td>", $bufer);
			$bufer = str_replace("<td> Marcas Patentes T.Tecnologia I.Tecnologica Modelos Exp Adm. Foro Mis Tramites </td> <td>","", $bufer);
			$bufer = str_replace('<td> Usuario sin registrar </td> <td> </td> </td> <td></td> swfobject.registerObject("FlashID"); <td> Imprimir', '', $bufer);
			$bufer = str_replace("<td>Direccion de Marcas - ACTA", "<td>ACTA</td><td>", $bufer);
			$bufer = str_replace("<td> Haga clic aquí para activar su Clave Fiscal </td>", "", $bufer);
			$bufer = str_replace("<td></td>", "", $bufer);
			$bufer = str_replace("<td>Tipo Marca :", "<td>Tipo Marca :</td><td>", $bufer);
			$bufer = str_replace("<td>N&ordf; Efector:</td>","", $bufer);
			$bufer = str_replace("<td>-----------</td>", "", $bufer);	
			$bufer = str_replace("<td>--------</td>", "", $bufer);
			$bufer = str_replace("<td>--------------</td>", "", $bufer);
			$bufer = str_replace("-----------", "", $bufer);
			$bufer = str_replace("<td>PUBLICACI&Oacute;N</td> <td>Fecha:</td>", "<td>Fecha de Publicacion</td>", $bufer);
			$bufer = str_replace("</td>  </td>", "", $bufer);
			$bufer = str_replace("<td>Gestion del tramite</td>", "</td><td>Gestion del tramite</td>", $bufer);
			$bufer = str_replace("<td>Toma Raz</td> </td>", "<td>Toma Raz</td>", $bufer);
			$bufer = str_replace("</td>  Clase</td>", "</td><td>  Clase</td>", $bufer);
			$bufer = str_replace("&","&amp;", $bufer);
			
			//Iniciamos Archivo XML final		
	        $bufer = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<pag>\n" . $bufer . "\n</pag>";
	        if (mb_detect_encoding($bufer, 'UTF-8', TRUE) !== 'UTF-8') {
				$bufer = utf8_encode($bufer);
			}
			save_txtinfile($carpeta . "/" . $txtfile2, $bufer);
	        unlink($carpeta . "/" . $txtfile);  
	        
			
	        //Contenido del archivo
	        $xmlstr = ret_txtfile1($carpeta."/".$txtfile2);
	        
			//Lectura de XML
			$ant = $act = "";
	        $xml = new SimpleXMLElement($xmlstr);
	        $minfomarca = array();
	        $datosbasicos=array();
	        foreach ($xml->td as $td) {
			
	            $ant=$act;            
	            $act="";
	            switch ($td) {
	                case "ACTA":
	                    $act="numexp";
	                    break;                
	                case "Presentación:":
	                    $act="fecha_solicitud";
	                    break;
					case "Denominación: ":
	                    $act="denominacion";
	                    break;	
					case "Tipo Marca :":
	                    $act="tipomarca";
	                    break;	
					case " Clase":
	                    $act="clase";
	                    break;
					case "Nombre ":
	                    $act="titulares";
	                    break;
					case "País":
	                    $act="domicilio";
	                    break;
					case "Domicilio Real":
	                    $act="direccion";
	                    break;
					case "Localidad":
	                    $act="direccion2";
	                    break;	
					case "Agente:":
	                    $act="apoderado";
	                    break;		
	                case "Fecha de Publicacion":
	                    $act="fecha_publi";
	                    break;
					case "Numero:":
	                    $act="gacpub";
	                    break;
	                    break;		
	                default:
	                    if($act=="" & $ant!=""){
	                        $datosbasicos[$ant]=trim($td);
	                    }
	                    break;
	            }
	        }
			
			//print_r($datosbasicos);
			
			$expedientei = $datosbasicos["numexp"];
			$expediente = " ".$expedientei;
			$fecha_solicitud = $datosbasicos["fecha_solicitud"];
			$denominacion = strtoupper($datosbasicos["denominacion"]);
			$tipomarca = strtoupper($datosbasicos["tipomarca"]);
			$clasei = $datosbasicos["clase"];
			$clase = " ".$clasei;
			$titular = strtoupper($datosbasicos["titulares"]);
			$titular = str_replace("Ê","Ú", $titular);
			$domicilio = strtoupper($datosbasicos["domicilio"]);
			$direccion1 = strtoupper($datosbasicos["direccion"]);
			$localidad = strtoupper($datosbasicos["direccion2"]);
			$direccion = $direccion1.", ".$localidad;
			$apoderado = strtoupper($datosbasicos["apoderado"]);
			$fecha_pub = $datosbasicos["fecha_publi"];
			$ngac = $datosbasicos["gacpub"];
			
			
			//Ingresa en Tabla de Precarga
			add_marca($np, utf8_decode($denominacion),  utf8_decode($tipo_denomi),  utf8_decode($tipomarca), $expediente, $fecha_solicitud, utf8_decode($titular),  utf8_decode($direccion), utf8_decode($domicilio),  utf8_decode($apoderado), $dir_apo, $clase, $ngac, $fecha_pub, $plazo_opo, utf8_decode($prioridad), utf8_decode($prodyservs));
			
			
		   //Genero Archivo XML FINAL GAC
		    $query = 	"SELECT * FROM sam_precarga_gac_exterior";
			$result = mysql_query($query);
		 
			$xml = new DomDocument('1.0', 'UTF-8');
		
			//NODO PRINCIPAL
			$root = $xml->createElement('gacar');
			$root = $xml->appendChild($root);
			//NODOS HIJOS
			while($array = mysql_fetch_array($result)) {
		 
			$noticia=$xml->createElement('marca');
			$noticia =$root->appendChild($noticia);
			
			$child = $xml->createElement('np');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode($array['np']);
			$value = $child->appendChild($value);
		 
			$child = $xml->createElement('expediente');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode($array['expediente']);
			$value = $child->appendChild($value);
			 
			$child = $xml->createElement('fecha_solicitud');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(trim($array['fecha_solicitud']));
			$value = $child->appendChild($value);
			 
			$child = $xml->createElement('denominacion');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(trim(utf8_encode($array['denominacion'])));
			$value = $child->appendChild($value);
			
			$child = $xml->createElement('tipo_denomi');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(trim($array['tipo_denomi']));
			$value = $child->appendChild($value);
			 
			$child = $xml->createElement('tipomarca');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(trim($array['tipomarca']));
			$value = $child->appendChild($value);
			 
			$child = $xml->createElement('clases');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode($array['clases']);
			$value = $child->appendChild($value);
			 
			$child = $xml->createElement('titular');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(trim(utf8_encode($array['titular'])));
			$value = $child->appendChild($value);
			
			$child = $xml->createElement('domicilio');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(utf8_encode($array['domicilio']));
			$value = $child->appendChild($value);
			
			$child = $xml->createElement('direccion');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(utf8_encode($array['direccion']));
			$value = $child->appendChild($value);
			
			$child = $xml->createElement('apoderado');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(trim(utf8_encode($array['apoderado'])));
			$value = $child->appendChild($value);
			
			$child = $xml->createElement('gaceta');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode($array['gaceta']);
			$value = $child->appendChild($value);
			
			$child = $xml->createElement('fecha_publicacion');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(trim($array['fecha_publicacion']));
			$value = $child->appendChild($value);
			
			$child = $xml->createElement('plazo_opo');
			$child = $noticia->appendChild($child);
			$value = $xml->createTextNode(trim($array['plazo_opo']));
			$value = $child->appendChild($value);
			
			}

			$xml->formatOutput = true;
			 
			$strings_xml = $xml->saveXML();
			$xml->save('XML/GACAR'.$ngac.'.xml');
			
			
	    }
		unlink($carpeta . "/" . $txtfile2);	

	}

$w = stream_get_wrappers();
echo '<BR>openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "<BR>";
echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "<BR>";

?>
