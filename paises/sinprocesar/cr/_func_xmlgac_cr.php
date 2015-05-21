<?php

function ret_txtfile1($archivo){
    $gestor = fopen($archivo, "r");
    $contenido = fread($gestor, filesize($archivo));
    fclose($gestor);
    return $contenido;
}	
	
	
function ret_txtfile2($archivo){
    $gestor = fopen($archivo, "rb");
    $contenido = stream_get_contents($gestor);
    fclose($gestor);
    
	return $contenido;    
}


class SimpleXMLExtended extends SimpleXMLElement {
	  public function addCDATA($cData) {
	    $node = dom_import_simplexml($this);
	    $no = $node->ownerDocument;
	    $node->appendChild($no->createCDATASection($cData));
	  }
	}

function save_txtinfile($nombre_archivo,$contenido) {
    if (!$gestor = fopen($nombre_archivo, 'w')) {
        return 1;//No se puede abrir y/o crear el archivo
    }
    // Escribir $contenido en archivo.
    if (fwrite($gestor, $contenido) === FALSE) {
        return 2;//No se deja escribir el archivo
    }
    fclose($gestor);
    return 0;
}



function add_marca($np, $denominacion, $tipo_denomi, $tipomarca, $expediente, $fecha_solicitud, $titular, $direccion, $domicilio, $apoderado, $dir_apo, $clase, $ngac, $fecha_pub, $plazo_opo)  {
	global $data, $db;
	
	//Ingresa en Tabla de Precarga
	$host = "localhost";
	$user = "root";
	$pw = "";
	$db = "test";
		
	$link = mysql_connect ($host,$user,$pw) or die ("problemas");
	mysql_select_db ($db,$link) or die ("problemas db");
	
	$db = "INSERT INTO `sam_precarga_gac_exterior` (
		`np`,
		`denominacion`,
		`tipo_denomi`,
		`tipomarca`,
		`expediente`,
		`fecha_solicitud`,
		`titular`,
		`direccion`,
		`domicilio`,
		`apoderado`,
		`dir_apo`,
		`clases`,
		`gaceta`,
		`fecha_publicacion`,
		`plazo_opo`
	)
	VALUES
		(
			'$np',
			'$denominacion',
			'$tipo_denomi',
			'$tipomarca',
			'$expediente',
			'$fecha_solicitud',
			'$titular',
			'$direccion',
			'$domicilio',
			'$apoderado',
			'$dir_apo',
			'$clase',
			'$ngac',
			'$fecha_pub',
			'$plazo_opo'
		)";

	mysql_query ($db);
	$sql2=("DELETE FROM `sam_precarga_gac_exterior` WHERE (`expediente`=' Solicitud') AND (`clases`=' Clase');");
	mysql_query ($sql2);
	
	//echo $db."<BR><BR>";
    
	
   
  }


function desc_actaweb_cr($signo, $carpeta) {
	
	$ngac=$signo;
	$nacta = $signo.".html"; 
	//$dirsolweb="http://10.211.55.4/work/orbisoft/bo/".$nacta;
	//$dirsolweb="C:\\AppServ\\www\\x\\cr\\tmp\\".$nacta;
	$dirsolweb="C:\\AppServ\\www\\work\\orbisoft\\cr\\tmp\\".$nacta;
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
		$cadsw = trim($cadsw);
		$cadsw = str_replace("<td>PUBLICACIÓN DE TERCERA VEZ</td>", "<tr><td>Apoderado</td><td>", $cadsw);
		$cadsw = str_replace(").", "</td></tr>\n<tr><td>apoderado</td><td>", $cadsw);
		$cadsw = str_replace(", en calidad de apoderado especial de ", "</td><td>Titular</td><td>", $cadsw);
		$cadsw = str_replace("con domicilio en ", "</td><td>Direccion y Domicilio</td><td>", $cadsw);
		$cadsw = str_replace("solicita  la  inscripción", "</td><td>Denominacion</td>", $cadsw);
		$cadsw = str_replace("de:  <td>", "<td>", $cadsw);
		$cadsw = str_replace("como  marca  de  fábrica  y  comercio  en  ", "<td>Tipo Denominacion</td><td>marca  de  fábrica  y  comercio</td>", $cadsw);
		$cadsw = str_replace("clase:  ", "<td>Clase:</td><td>", $cadsw);
		$cadsw = str_replace("internacional,  para", "</td><td>", $cadsw);
		$cadsw = str_replace(". Presentada el ", "</td><td>Fecha Publicacion</td><td>", $cadsw);
		$cadsw = str_replace(". Solicitud Nº", "</td><td>Expediente</td><td>", $cadsw);
		$cadsw = str_replace(". A efectos de publicación,", "</td><td>", $cadsw);
		$cadsw = str_replace("--(IN", "</td><td>", $cadsw);
		$cadsw = str_replace("&quot;", "\"", $cadsw);
		$cadsw = str_replace(",  en  calidad  de  apoderado  especial  de  ", "</td><td>Titular</td><td>", $cadsw);
		$cadsw = str_replace("solicita la inscripción de:", "</td><td>Denominacion</td>", $cadsw);
		$cadsw = str_replace("como marca de fábrica y comercio en ", "<td>Tipo Denominacion</td><td>marca  de  fábrica  y  comercio</td>", $cadsw);
		$cadsw = str_replace("clase: ", "<td>Clase:</td><td>", $cadsw);
		$cadsw = str_replace("internacional,", "</td><td>", $cadsw);
		$cadsw = str_replace("edicto.  Presentada  el", "</td><td>Fecha Publicacion</td><td>", $cadsw);
		$cadsw = str_replace(".  Solicitud  Nº  ", "</td><td>Expediente</td><td>", $cadsw);
		$cadsw = str_replace("domicilio en ", "</td><td>Direccion y Domicilio</td><td>", $cadsw);
		$cadsw = str_replace("como marca de comercio en", "<td>Tipo Denominacion</td><td>marca de comercio</td>", $cadsw);
		$cadsw = str_replace("edicto. Presentada", "</td><td>Fecha Publicacion</td><td>", $cadsw);
		$cadsw = str_replace(".  A  efectos  de", "</td><td>", $cadsw);
		$cadsw = str_replace(". A efectos", "</td><td>", $cadsw);
		$cadsw = str_replace("como marca de servicios en", "<td>Tipo Denominacion</td><td>marca de servicios</td>", $cadsw);
		//$cadsw = str_replace("&amp;", "&", $cadsw);
		$cadsw = str_replace(", en calidad de apoderado generalísimo de", "</td><td>Titular</td><td>", $cadsw);
		$cadsw = str_replace("(IN", "</td><td>", $cadsw);
		$cadsw = str_replace("como  marca  de  servicios  en  <td>Clase", "<td>Tipo Denominacion</td><td>marca de servicios</td><td>Clase", $cadsw);
		$cadsw = str_replace("como nombre comercial,", "<td>Tipo Denominacion</td><td>nombre comercial</td><td>", $cadsw);
		$cadsw = str_replace("La Gaceta Nº 220 -- Viernes 14 de noviembre del 2014", "", $cadsw);
		$cadsw = str_replace(". A efectos de publicación,", "</td><td>", $cadsw);
		$cadsw = str_replace("marca de fábrica y comercio", "<td>Tipo Denominacion</td><td>marca de fábrica y comercio</td>", $cadsw);
		$cadsw = str_replace("REGISTRO DE LA PROPIEDAD INTELECTUAL", "", $cadsw);
		$cadsw = str_replace("</td>como marca de servicios", "</td><td>Tipo Denominacion</td><td>marca de servicios</td>", $cadsw);
		$cadsw = str_replace("Solicitud Nº ", "</td><td>Expediente</td><td>", $cadsw);
		$cadsw = str_replace("</td>como marca de fábrica", "<td>Tipo Denominacion</td><td>marca  de  fábrica ", $cadsw);
		$cadsw = str_replace("y comercio en", "y comercio</td>", $cadsw);
		$cadsw = str_replace("y servicios en", "y servicios</td>", $cadsw);
		$cadsw = str_replace(",  en  calidad  de  apoderado  generalísimo  de", "</td><td>Titular</td><td>", $cadsw);
		$cadsw = str_replace(",  con  domicilio", "</td><td>Direccion y Domicilio</td><td>", $cadsw);
		$cadsw = str_replace("como  marca  de  comercio  en  <td>", "<td>Tipo Denominacion</td><td>marca  de  comercio</td><td>", $cadsw);
		$cadsw = str_replace("</td>como Marca", "<td>Tipo Denominacion</td><td>marca", $cadsw);
		$cadsw = str_replace("de Comercio en clase (s):", "de  comercio</td><td>Clase:", $cadsw);
		$cadsw = str_replace("con domicilio", "</td><td>Direccion y Domicilio</td><td>", $cadsw);
		$cadsw = str_replace("internacional (es),", "</td><td>", $cadsw);
		$cadsw = str_replace("</td>como  marca  de  comercio  y  servicios", "<td>Tipo Denominacion</td><td>marca  de  comercio  y  servicios</td>", $cadsw);
		$cadsw = str_replace("<td></td>", "", $cadsw);
		$cadsw = str_replace("en  calidad  de  apoderada  generalísima  de", "</td><td>Titular</td><td>", $cadsw);
		$cadsw = preg_replace('/<td>Pág[[:space:]]*([^>]*>)/', '', $cadsw);
		$cadsw = trim($cadsw);
		$cadsw = str_replace("domicilio  en  ", "</td><td>Direccion y Domicilio</td><td>", $cadsw);
		$cadsw = str_replace("como  marca  de  fábrica  y  comercio,  en  clase,", "<td>Tipo Denominacion</td><td>marca  de  fábrica  y  comercio</td><td>Clase:", $cadsw);
		$cadsw = str_replace("como  marca  de  servicios,  en  clase", "<td>Tipo Denominacion</td><td>marca  de  servicios</td><td>Clase:", $cadsw);
		$cadsw = str_replace("</td> como marca de fábrica y", "<td>Tipo Denominacion</td><td>marca  de  fábrica  y", $cadsw);
		$cadsw = str_replace("comercio,  en  clase  ", "comercio</td><td>Clase:", $cadsw);
		$cadsw = str_replace("", "", $cadsw);
		$cadsw = str_replace("", "", $cadsw);
		$cadsw = str_replace("", "", $cadsw);
		$cadsw = str_replace("", "", $cadsw);
		$cadsw = str_replace("", "", $cadsw);
		$cadsw = str_replace("", "", $cadsw);
		
		
		
		
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
		
			
		//Inicio de XML Guardo y elimina txt
		$bufer = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<pag>\n" . $bufer . "</td>\n</pag>";
        if (mb_detect_encoding($bufer, 'UTF-8', TRUE) !== 'UTF-8') {
			$bufer = utf8_encode($bufer);
		}
		
		save_txtinfile($carpeta . "/" . $txtfile2, $bufer);
        //unlink($carpeta . "/" . $txtfile);  
        
		//Contenido del archivo
		$xmlstr = ret_txtfile1($carpeta."/".$txtfile2);
		
		//Lectura de XML
		$ant = $act = "";
		$xml = new SimpleXMLElement($xmlstr);
		$datosbasicos = array();
		$datosgenerales = array();
		$j = -1;
		foreach ($xml->td as $td) {
			$ant = $act;
			$act = "";
			$td = trim($td);
			switch ($td) {
				case "apoderado":
					$act = "apoderado";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
						
					}
					$j++;
					break;
				case "Direccion y Domicilio":
					$act = "domicilio";
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
				case "CLASE INTERNACIONAL":
					$act = "clases";
					break;
				default:
					if ($act == "" & $ant != "") {
						$datosbasicos[$ant] = trim($td);
					}
					break;
			}

			
		}		
		$j++;
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
		
		$child = $xml->createElement('fecha_pub');
		$child = $noticia->appendChild($child);
		$value = $xml->createTextNode(trim($array['fecha_pub']));
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