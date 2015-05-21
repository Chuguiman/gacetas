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
	
	
	//echo $db."<BR><BR>";
    
	return;
   
  }
  
function crea_xml_gac ($pais, $ngac) {

	$host = "localhost";
	$user = "root";
	$pw = "";
	$db = "test";
		
	$link = mysql_connect ($host,$user,$pw) or die ("problemas");
	mysql_select_db ($db,$link) or die ("problemas db");
	
	//Exportamos datos a XML
		$query = 	"SELECT * FROM sam_precarga_gac_exterior";
		$result = mysql_query(utf8_decode($query));
	 
		$xml = new DomDocument('1.0', 'UTF-8');
	
		//NODO PRINCIPAL
		$root = $xml->createElement('GAC'.$pais);
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
		$xml->save('XML/GAC'.$pais.$ngac.'.xml');
		
		return;
		
}


function desc_actaweb_ec($signo, $carpeta) {
	
	$ngac=$signo;
	$nacta = $signo.".html"; 
	//$dirsolweb="http://10.211.55.4/work/orbisoft/bo/".$nacta;
	$dirsolweb="C:\\AppServ\\www\\work\\orbisoft\\ec\\tmp\\".$nacta;
    echo "<br>".$dirsolweb."<br>";
    
    if (trim($nacta) != "") {
        //Contenido de la página        
        $cadsw = ret_txtfile2($dirsolweb);
		if (mb_detect_encoding($cadsw, 'UTF-8', TRUE) !== 'UTF-8') {
			$cadsw = utf8_encode($cadsw);
		}
		
		
        $cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); //Elimina Contenido del HEAD
		$cadsw = strip_tags($cadsw);
		$cadsw = str_replace("signo de solicitud: denominativo", "<td>TIPO MARCA</td><td>denominativo</td><td>DENOMINACION</td><td>", $cadsw);
		$cadsw = str_replace("signo de solicitud: mixto", "</td><td>TIPO MARCA</td><td>mixto</td><td>DENOMINACION</td><td>", $cadsw);
		$cadsw = str_replace("signo de solicitud: figurativo", "</td><td>TIPO MARCA</td><td>figurativa</td><td>DENOMINACION</td><td>", $cadsw);
		$cadsw = str_replace("marca de producto", "</td><td>TIPO DENOMI</td><td>marca de producto</td>", $cadsw);
		$cadsw = str_replace("marca de servicios", "</td><td>TIPO DENOMI</td><td>marca de servicios</td>", $cadsw);
		$cadsw = str_replace("nombre comercial", "</td><td>TIPO DENOMI</td><td>nombre comercial</td>", $cadsw);
		$cadsw = str_replace("lema comercial ", "</td><td>TIPO DENOMI</td><td>lema comercial </td>", $cadsw);
		$cadsw = str_replace("país:", "<td>PAIS</td><td>", $cadsw);
		$cadsw = str_replace("(511)", "</td><td>CLASES NIZA</td><td>", $cadsw);
		$cadsw = str_replace("comprendidos en la clase internacional no.", "</td><td>CLASES NIZA</td><td>", $cadsw);
		$cadsw = str_replace("(220)", "</td><td>FECHA DE SOLICITUD</td><td>", $cadsw);
		$cadsw = str_replace("(210)", "</td><td>EXPEDIENTE</td><td>", $cadsw);
		$cadsw = str_replace("(730)", "</td><td>TITULAR</td><td>", $cadsw);
		$cadsw = str_replace("(740)", "</td><td>APODERADO</td><td>", $cadsw);
		$cadsw = str_replace(" productos: ", "</td><td>PRODYSERV</td><td>", $cadsw);
		$cadsw = str_replace(" servicios: ", "</td><td>PRODYSERV</td><td>", $cadsw);
		$cadsw = str_replace(" actividades: ", "</td><td>PRODYSERV</td><td>", $cadsw);
		$cadsw = str_replace("esta destinado a proteger", "<td>PRODYSERV", $cadsw);
		$cadsw = str_replace("<td> ", "<td>", $cadsw);
		$cadsw = str_replace(" </td>", "</td>", $cadsw);
		$cadsw = str_replace("---", "<td></td>", $cadsw);
		$cadsw = str_replace("powered by ", "</td>", $cadsw);
		$cadsw = str_replace("&quot;", "\"", $cadsw);
		$cadsw = str_replace("<td></td>", "", $cadsw);
		$cadsw = str_replace(". <td>TIPO MARCA</td>", ".</td><td>TIPO MARCA</td>", $cadsw);
		$cadsw = str_replace(" <td>TIPO MARCA</td>", ".</td><td>TIPO MARCA</td>", $cadsw);
		$cadsw = str_replace("</td></td>", "</td>", $cadsw);
		//$cadsw = str_replace("<td>SALTO PAG", "<tr><td>SALTO PAG</td></tr><td>", $cadsw);
		$cadsw = str_replace("<td> </td>", "<td></td>", $cadsw);
		$cadsw = str_replace("596 - septiembre - 2014", "</td><td>SALTO PAG", $cadsw);
		$cadsw = str_replace("597 - octubre - 2014", "", $cadsw);
		$cadsw = str_replace("tcpdf", "<td>", $cadsw);
 
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
		
		$bufer = str_replace("\(www.tcpdf.org\) </td>", "<td> </td>", $bufer);
		$bufer = str_replace("<td>PAISd>", "<td>PAIS</td>", $bufer);
		$bufer = str_replace("<td>marca de producto> ", "<td>marca de producto</td> ", $bufer);
		$bufer = str_replace("5/td>", "5</td>", $bufer);
		$bufer = str_replace("</td> d>TIPO DENOMI</td>", "</td><td>TIPO DENOMI</td>", $bufer);
		$bufer = str_replace("<td>TIPO MARCAd>", "<td>TIPO MARCA</td>", $bufer);
		$bufer = str_replace("</td>>TITULAR</td>", "</td><td>TITULAR</td>", $bufer);
		$bufer = str_replace("</td>d>", "</td><td>", $bufer);
		$bufer = str_replace("<td>EXPEDIENTE/td>", "<td>EXPEDIENTE</td>", $bufer);
		$bufer = str_replace("</td>d>EXPEDIENTE</td>", "</td><td>EXPEDIENTE</td>", $bufer);
		$bufer = str_replace("<td>APODERADO>", "<td>APODERADO</td>", $bufer);
		$bufer = str_replace("</td>>denominativo</td>", "</td><td>denominativo</td>", $bufer);
		$bufer = str_replace("<td>APODERADOtd>", "<td>APODERADO</td>", $bufer);
		$bufer = str_replace("<td> </td>", "", $bufer);
		$bufer = str_replace(". <td>PAIS", ".</td> <td>PAIS", $bufer);
		$bufer = str_replace("</td>>", "</td><td>", $bufer);
		$bufer = str_replace("ta>", "ta</td>", $bufer);
		$bufer = str_replace("</td> </td><td>", "</td> <td>", $bufer);
		$bufer = str_replace("0<td>", "0</td><td>", $bufer);
		$bufer = str_replace("1<td>", "1</td><td>", $bufer);
		$bufer = str_replace("2<td>", "2</td><td>", $bufer);
		$bufer = str_replace("3<td>", "3</td><td>", $bufer);
		$bufer = str_replace("4<td>", "4</td><td>", $bufer);
		$bufer = str_replace("5<td>", "5</td><td>", $bufer);
		$bufer = str_replace("6<td>", "6</td><td>", $bufer);
		$bufer = str_replace("7<td>", "7</td><td>", $bufer);
		$bufer = str_replace("8<td>", "8</td><td>", $bufer);
		$bufer = str_replace("9<td>", "9</td><td>", $bufer);
		$bufer = str_replace("<td>FECHA DE SOLICITUD/td>", "<td>FECHA DE SOLICITUD</td>", $bufer);
		$bufer = str_replace("r<td>", "r</td><td>", $bufer);
		$bufer = str_replace("<td>EXPEDIENTEtd>", "<td>EXPEDIENTE</td>", $bufer);
		$bufer = str_replace("<td>denominativotd>", "<td>denominativo</td>", $bufer);
		$bufer = str_replace("apariencia distintiva<td>", "apariencia distintiva</td><td>", $bufer);
		$bufer = str_replace("apariencia distintiva <td>", "apariencia distintiva</td><td>", $bufer);
		$bufer = str_replace("</td>td>", "</td><td>", $bufer);
		$bufer = str_replace("0d>", "0</td>", $bufer);
		$bufer = str_replace("1d>", "1</td>", $bufer);
		$bufer = str_replace("2d>", "2</td>", $bufer);
		$bufer = str_replace("3d>", "3</td>", $bufer);
		$bufer = str_replace("4d>", "4</td>", $bufer);
		$bufer = str_replace("5d>", "5</td>", $bufer);
		$bufer = str_replace("6d>", "6</td>", $bufer);
		$bufer = str_replace("7d>", "7</td>", $bufer);
		$bufer = str_replace("8d>", "8</td>", $bufer);
		$bufer = str_replace("9d>", "9</td>", $bufer);
		$bufer = str_replace("<td>TITULARtd>", "<td>TITULAR</td>", $bufer);
		$bufer = str_replace("</td>td>PRODYSERV</td>", "</td><td>PRODYSERV</td>", $bufer);
		$bufer = str_replace("<td>PRODYSERVd>", "<td>PRODYSERV</td>", $bufer);
		$bufer = str_replace("</td>td>TITULAR</td>", "</td><td>TITULAR</td>", $bufer);
		$bufer = str_replace("<td>DENOMINACION>", "<td>DENOMINACION</td>", $bufer);
		$bufer = str_replace("9td>", "9</td>", $bufer);
		$bufer = str_replace("8td>", "8</td>", $bufer);
		$bufer = str_replace("7td>", "7</td>", $bufer);
		$bufer = str_replace("6td>", "6</td>", $bufer);
		$bufer = str_replace("5td>", "5</td>", $bufer);
		$bufer = str_replace("4td>", "4</td>", $bufer);
		$bufer = str_replace("3td>", "3</td>", $bufer);
		$bufer = str_replace("2td>", "2</td>", $bufer);
		$bufer = str_replace("1td>", "1</td>", $bufer);
		$bufer = str_replace("0td>", "0</td>", $bufer);
		$bufer = str_replace("g/td>", "g</td>", $bufer);
		$bufer = str_replace("<td>TIPO MARCA/td>", "<td>TIPO MARCA</td>", $bufer);
		$bufer = str_replace(".>", ".</td>", $bufer);
		$bufer = str_replace("<td>TITULAR/td>", "<td>TITULAR</td>", $bufer);
		$bufer = str_replace("<td>TIPO MARCAtd>", "<td>TIPO MARCA</td>", $bufer);
		$bufer = str_replace("t>", "t</td>", $bufer);
		$bufer = str_replace("<td>PAIS>", "<td>PAIS</td>", $bufer);
		$bufer = str_replace("iepi-uio-pi-sd-", "", $bufer);
		$bufer = str_replace("<td>mixto/td>", "<td>mixto</td>", $bufer);
		$bufer = str_replace("<td>EXPEDIENTEd>", "<td>EXPEDIENTE</td>", $bufer);
		$bufer = str_replace("<td>TIPO DENOMItd>", "<td>TIPO DENOMI</td>", $bufer);
		$bufer = str_replace("9>", "9</td>", $bufer);
		$bufer = str_replace("8>", "8</td>", $bufer);
		$bufer = str_replace("7>", "7</td>", $bufer);
		$bufer = str_replace("6>", "6</td>", $bufer);
		$bufer = str_replace("5>", "5</td>", $bufer);
		$bufer = str_replace("4>", "4</td>", $bufer);
		$bufer = str_replace("3>", "3</td>", $bufer);
		$bufer = str_replace("2>", "2</td>", $bufer);
		$bufer = str_replace("1>", "1</td>", $bufer);
		$bufer = str_replace("0>", "0</td>", $bufer);
		$bufer = str_replace("<td>nombre comercialtd> ", "<td>nombre comercial</td> ", $bufer);
		$bufer = str_replace("tridimensional <td>", "tridimensional</td><td>", $bufer);
		$bufer = str_replace(".d>", ".</td>", $bufer);
		$bufer = str_replace("gd>", "g</td>", $bufer);
		$bufer = str_replace("e/td>", "e</td>", $bufer);
		$bufer = str_replace("iepi-", "", $bufer);
		$bufer = str_replace("<td>PRODYSERV/td>", "<td>PRODYSERV</td>", $bufer);
		$bufer = str_replace("<td>CLASES NIZA/td>", "<td>CLASES NIZA</td>", $bufer);
		$bufer = str_replace("ztd>", "z</td>", $bufer);
		$bufer = str_replace("std>", "s</td>", $bufer);
		$bufer = str_replace("</td>TIPO MARCA</td>", "</td><td>TIPO MARCA</td>", $bufer);
		$bufer = str_replace("<td>-d>", "", $bufer);
		$bufer = str_replace("<td>CLASES NIZA>", "<td>CLASES NIZA</td>", $bufer);
		$bufer = str_replace("<td>DENOMINACION/td>", "<td>DENOMINACION</td>", $bufer);
		$bufer = str_replace("<td>mixtod>", "<td>mixto</td>", $bufer);
		$bufer = str_replace("rtd>", "r</td>", $bufer);
		$bufer = str_replace("</td>9", "</td><td>9", $bufer);
		$bufer = str_replace("</td>8", "</td><td>8", $bufer);
		$bufer = str_replace("</td>7", "</td><td>7", $bufer);
		$bufer = str_replace("</td>6", "</td><td>6", $bufer);
		$bufer = str_replace("</td>5", "</td><td>5", $bufer);
		$bufer = str_replace("</td>4", "</td><td>4", $bufer);
		$bufer = str_replace("</td>3", "</td><td>3", $bufer);
		$bufer = str_replace("</td>2", "</td><td>2", $bufer);
		$bufer = str_replace("</td>1", "</td><td>1", $bufer);
		$bufer = str_replace("</td>0", "</td><td>0", $bufer);
		$bufer = str_replace("<td>marca de productod>", "<td>marca de producto</td>", $bufer);
		$bufer = str_replace("</td> 0", "</td><td>0", $bufer);
		$bufer = str_replace("</td> 1", "</td><td>1", $bufer);
		$bufer = str_replace("</td> 2", "</td><td>2", $bufer);
		$bufer = str_replace(". </td> </td>", "</td>", $bufer);
		$bufer = str_replace("atd>", "a</td>", $bufer);
		$bufer = str_replace("<td>marca de productotd> ", "<td>marca de producto</td> ", $bufer);
		$bufer = str_replace("", "", $bufer);
		$bufer = str_replace("", "", $bufer);
		$bufer = str_replace("", "", $bufer);
		$bufer = str_replace("", "", $bufer);
		$bufer = str_replace("", "", $bufer);
		
		//Inicio de XML Guardo y elimina txt
		$bufer = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<pag>\n" . $bufer . "</td>\n</pag>";
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
				case "TIPO MARCA":
					$act = "tipomarca";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
						
					}
					$j++;
					break;
				case "DENOMINACION":
					$act = "denominacion";
					break;
				case "TIPO DENOMI":
					$act = "tipo_denomi";
					break;
				case "PAIS":
					$act = "domicilio";
					break;
				case "CLASES NIZA":
					$act = "clases";
					break;
				case "FECHA DE SOLICITUD":
					$act = "fecha_solicitud";
					break;
				case "EXPEDIENTE":
					$act = "expediente";
					break;
				case "TITULAR":
					$act = "titular";
					break;
				case "APODERADO":
					$act = "apoderado";
					break;
				case "PRODYSERV":
					$act = "";
					break;
				case "SALTO PAG":
					$act = "";
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
		
		foreach ($datosgenerales as $datoadato) {
			/*echo "<br>==========================================";
			echo "<br><b>EXP:</b> ".$expediente=$datoadato["expediente"];
			echo "<br><b>CL:</b> ".substr(($clases=$datoadato["clases"]), 0, 3);
			echo "<br><b>APO:</b> ".$apoderado=$datoadato["apoderado"];
			echo "<br><b>TIT:</b> ".$titular=$datoadato["titular"];
			echo "<br><b>DOM:</b> ".$domicilio=$datoadato["domicilio"];
			echo "<br><b>TDE:</b> ".$tipo_denomi=$datoadato["tipo_denomi"];
			echo "<br><b>DENO:</b> ".$denominacion=$datoadato["denominacion"];
			echo "<br><b>TMA:</b> ".$tipomarca=$datoadato["tipomarca"];
			echo "<br><b>FSO:</b> ".substr(($fecha_solicitud=$datoadato["fecha_solicitud"]), 0, 14);
			}	*/
			$expediente = $datoadato["expediente"];
			$expediente = str_replace("-re", "", $expediente);
			$clases     = substr(($datoadato["clases"]), 0, 3);
			$apoderado  = strtoupper($datoadato["apoderado"]);
			$titular    = strtoupper($datoadato["titular"]);
			$domicilio    = strtoupper($datoadato["domicilio"]);
			$tipo_denomi  = strtoupper($datoadato["tipo_denomi"]);
			$denominacion = strtoupper($datoadato["denominacion"]);
			$tipomarca  = strtoupper($datoadato["tipomarca"]);
			$fecha_solicitud   = substr((trim($datoadato["fecha_solicitud"])), 0, 14);
			
			//Ingresa en Tabla de Precarga
			add_marca($np, utf8_decode($denominacion),  utf8_decode($tipo_denomi),  utf8_decode($tipomarca), $expediente, $fecha_solicitud, utf8_decode($titular),  utf8_decode($direccion), utf8_decode($domicilio),  utf8_decode($apoderado), $dir_apo, $clases, $ngac, $fecha_publicacion, $plazo_opo);
		
		}
		
			
		//unlink($carpeta . "/" . $txtfile2);
		
		$paisgac = "EC";
		crea_xml_gac ($paisgac, $ngac);
		
	}
}
?>