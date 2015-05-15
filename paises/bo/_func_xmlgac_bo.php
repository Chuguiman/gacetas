<?php

require_once ("../../clases/_func_tranformar_gac_ext.php");

function desc_actaweb_bo($signo, $fechapub, $carpeta) {
	
	$ngac=$signo;
	$fecha_gac = $fechapub;
	$nacta = $signo.".html"; 
	
	$dirsolweb="origen/".$nacta;
	
    echo "<br>".$dirsolweb."<br>";
    
    if (trim($nacta) != "") {
        //Contenido de la página        
        $cadsw = ret_txtfile2($dirsolweb);
		if (mb_detect_encoding($cadsw, 'UTF-8', TRUE) !== 'UTF-8') {
			$cadsw = utf8_encode($cadsw);
		}
		
		
        $cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); //Elimina Contenido del HEAD
		$cadsw = strip_tags($cadsw,"<b>");
		$cadsw = trim($cadsw);
		$cadsw = str_replace("<b>", "<td>", $cadsw);
		$cadsw = str_replace("</b>", "</td>", $cadsw);
		$cadsw = str_replace("<td> ", "<td>", $cadsw);
		$cadsw = str_replace(" </td>", "</td>", $cadsw);
		$cadsw = str_replace("NUMERO DE PUBLICACION", "<td>NUMERO DE PUBLICACION</td>", $cadsw);
		$cadsw = str_replace("NOMBRE DEL SIGNO", "<td>NOMBRE DEL SIGNO</td>", $cadsw);
		$cadsw = str_replace("GENERO DEL SIGNO", "<td>GENERO DEL SIGNO</td>", $cadsw);
		$cadsw = str_replace("TIPO DE SIGNO", "<td>TIPO DE SIGNO</td>", $cadsw);
		$cadsw = str_replace("NUMERO DE  SOLICITUD", "<td>NUMERO DE  SOLICITUD</td>", $cadsw);
		$cadsw = str_replace("NUMERO DE    SOLICITUD", "<td>NUMERO DE  SOLICITUD</td>", $cadsw);
		$cadsw = str_replace("FECHA DE SOLICITUD", "<td>FECHA DE SOLICITUD</td>", $cadsw);
		$cadsw = str_replace("NOMBRE DEL TITULAR", "<td>NOMBRE DEL TITULAR</td>", $cadsw);
		$cadsw = str_replace("DIRECCION DEL TITULAR", "<td>DIRECCION DEL TITULAR</td>", $cadsw);
		$cadsw = str_replace("PAIS DEL TITULAR", "<td>PAIS DEL TITULAR</td>", $cadsw);
		$cadsw = str_replace("NOMBRE DEL APODERADO", "<td>NOMBRE DEL APODERADO</td>", $cadsw);
		$cadsw = str_replace("DIRECCION DEL APODERADO", "<td>DIRECCION DEL APODERADO</td>", $cadsw);
		$cadsw = str_replace("CLASE INTERNACIONAL", "<td>CLASE INTERNACIONAL</td>", $cadsw);
		$cadsw = str_replace("&quot;", "", $cadsw);
		
		
		
 
        //Retornar Archivo
        $txtfile  = $signo.".txt";
		$txtfile2 = $signo.".xml";
        save_txtinfile($carpeta . "/" . $txtfile, utf8_decode($cadsw));
        
		//Limpia
        $gestor = @fopen($carpeta . "/" . $txtfile, "r");
        if ($gestor) {
            while (!feof($gestor)) {
                $bufer.= fgetss($gestor, 4096, "<td>");
            }
            fclose($gestor);
        }
		
		$bufer = preg_replace('/\s+/', ' ', $bufer);
        $bufer = preg_replace("/<td[[:space:]]*([^>]*>)/", "<td>", $bufer);        
        $bufer = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $bufer);
        $bufer = preg_replace("/^[[:space:]]*([^<]*<)/", "<", $bufer);
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);  
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);
		$bufer = str_replace("'", "\'", $bufer);
		$bufer = str_replace("", "", $bufer);
		$bufer = str_replace("", "", $bufer);
		$bufer = str_replace("", "", $bufer);
			
		//Inicio de XML Guardo y elimina txt
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
		$datosbasicos = array();
		$datosgenerales = array();
		$j = -1;
		foreach ($xml->td as $td) {
			$ant2= $ant;
			$ant = $act;
			$act = "";
			$td = trim($td);
			switch ($td) {
				case "NUMERO DE PUBLICACION":
					$act = "np";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
						
					}
					$j++;
					break;
				case "NOMBRE DEL SIGNO":
					$act = "denominacion";
					break;
				case "GENERO DEL SIGNO":
					$act = "tipo_denomi";
					break;
				case "TIPO DE SIGNO":
					$act = "tipomarca";
					break;
				case "NUMERO DE SOLICITUD":
					$act = "expediente";
					break;
				case "FECHA DE SOLICITUD":
					$act = "fecha_solicitud";
					break;
				case "NOMBRE DEL TITULAR":
					$act = "titular";
					break;
				case "DIRECCION DEL TITULAR":
					$act = "direccion";
					break;
				case "PAIS DEL TITULAR":
					$act = "domicilio";
					break;
				case "NOMBRE DEL APODERADO":
					$act = "apoderado";
					break;
				case "DIRECCION DEL APODERADO":
					$act = "dir_apo";
					break;	
				case "CLASE INTERNACIONAL":
					$act = "clases";
					break;
				default:
					if ($act == "" & $ant != "") {
						$datosbasicos[$ant] = trim($td);
					}elseif ($act == "" & $ant == ""){
						$ant = $ant2;
						$datosbasicos[$ant].=" ". trim($td);
					}
					break;
			}

			
		}		
		$j++;
		//echo "<br>Matriz de Datos Basicos:<br>";
		//print_r($datosgenerales);
		
		//Insertamos los datosgenerales a tabla
		$con = mysql_connect("localhost","root","");
		mysql_select_db("test", $con);
		
		foreach ($datosgenerales as $array) {
			$sql  = "INSERT INTO sam_precarga_gac_exterior";
			$sql .= " (`".implode("`, `", array_keys($array))."`)";
			$sql .= " VALUES ('".implode("', '", $array)."') ";
			$result = mysql_query(utf8_decode($sql)) or die(mysql_error());
			
		}
		
		$sql2 = "UPDATE `sam_precarga_gac_exterior` SET `gaceta`='".$ngac."', `fecha_publicacion`='".$fecha_gac."';";
		$result2 = mysql_query(utf8_decode($sql2)) or die(mysql_error());
		
		$querys = 	"UPDATE `sam_precarga_gac_exterior` SET `clases` = REPLACE (`clases`, 'PRODUCTOS', '');";
		$result = mysql_query($querys);
		
		$querysx = 	"UPDATE `sam_precarga_gac_exterior` SET `clases` = REPLACE (`clases`, 'DESCRIPCION', '');";
		$result = mysql_query($querysx);
		
		$querysx1 = 	"UPDATE `sam_precarga_gac_exterior` SET `clases` = REPLACE (`clases`, '1 SOLICITADAS DECISION 486 de la Comun', '');";
		$result = mysql_query($querysx1);
		
		$querysx2 = 	"UPDATE `sam_precarga_gac_exterior` SET `clases` = REPLACE (`clases`, '2 SUBSANADAS DECISION 486 de la Comun', '');";
		$result = mysql_query($querysx2);
		
		$querysx3 = 	"UPDATE `sam_precarga_gac_exterior` SET `clases` = REPLACE (`clases`, '2 SUBSANADAS DECISION 486', '');";
		$result = mysql_query($querysx3);
			
		//Exportamos datos a XML
		$query = 	"SELECT * FROM sam_precarga_gac_exterior";
		$result = mysql_query(utf8_decode($query));
	 
		$xml = new DomDocument('1.0', 'UTF-8');
	
		//NODO PRINCIPAL
		$root = $xml->createElement('gacbo');
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
		$value = $xml->createTextNode(trim(utf8_encode($array['tipo_denomi'])));
		$value = $child->appendChild($value);
		 
		$child = $xml->createElement('tipomarca');
		$child = $noticia->appendChild($child);
		$value = $xml->createTextNode(trim(utf8_encode($array['tipomarca'])));
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
		$xml->save('XML/GACBO'.$ngac.'.xml');
		

		unlink($carpeta . "/" . $txtfile2);
		
		/*limpio tabla para la proxima GAC.
		$limpiatable = "TRUNCATE sam_precarga_gac_bo";
		$rsq = mysql_query($limpiatable);*/
		
	}
}
?>