<?php

	require_once ("../../clases/_func_tranformar_gac_ext.php");
  
function desc_actaweb_pe($mes, $signo, $carpeta, $ngac) {
	
	$mesx=$mes."/dia/";
	
	if ($signo <= 9) {
		$ndia = $mesx.$signo."/0".$signo.".htm";
		$dirsolweb="http://www.estudiodelion.com.pe/wp/wpmark/".$ndia;
	} else {
		$ndia = $mesx.$signo."/".$signo.".htm";
		$dirsolweb="http://www.estudiodelion.com.pe/wp/wpmark/".$ndia;
	}
	
    echo "<br>".$dirsolweb."<br>";
    
	
    if (trim($ndia) != "") {
        //Contenido de la página        
        $cadsw = ret_txtfile2($dirsolweb);
		if (mb_detect_encoding($bufer, 'ISO-8859-1', TRUE) !== 'UTF-8') {//ISO-8859-1
			$bufer = utf8_encode($bufer);
		}
		
		$cadsw = strip_tags($cadsw,"<table><tr><td><th>");
		$cadsw = trim($cadsw);
		
		//Retornar Archivo
		$txtfile  = $signo.".txt";
		$txtfile2 = $signo.".html";
		
        save_txtinfile($carpeta . "/" . $txtfile, $cadsw);
		
		//Limpia
        $gestor = @fopen($carpeta . "/" . $txtfile, "r");
        if ($gestor) {
            while (!feof($gestor)) {
                $bufer.= fgetss($gestor, 4096, '<table><tr><td><th>');
            }
            fclose($gestor);
        }
		
		$arrayareemplazar = array("&nbsp;", "&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&bull;", "Ñ", "ñ");
        $arrayareemplazos = array("", "á", "e", "í", "ó", "ú", "", "&Ntilde;", "&ntilde;");
        $bufer = str_replace($arrayareemplazar, $arrayareemplazos, $bufer); 
		$bufer = preg_replace('/\s+/', ' ', $bufer);
		$bufer = preg_replace("/<table[[:space:]]*([^>]*>)/", "<table>", $bufer);
		$bufer = preg_replace("/<tr[[:space:]]*([^>]*>)/", "<tr>", $bufer);
        $bufer = preg_replace("/<td[[:space:]]*([^>]*>)/", "<td>", $bufer);        
        $bufer = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $bufer);
        $bufer = preg_replace("/^[[:space:]]*([^<]*<)/", "<", $bufer);
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);  
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);

		$bufer = str_replace('(d?mes/a?/td>', '</td>', $bufer);
		$bufer = str_replace('Publicaci?bsp;', 'Publicacion', $bufer);
		$bufer = str_replace('Pa?/td>', 'Pais</td>', $bufer);
		$bufer = str_replace('L?te', 'Limite', $bufer);
		
		$bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);
		$bufer = str_replace($arrayareemplazar, $arrayareemplazos, $bufer); 
		$bufer = str_replace('&', '&amp;', $bufer);
		$bufer = str_replace('<table> <tr><td> Marca</td>', '<table id="marcas"> <tr><td> Marca</td>', $bufer);
		$bufer = str_replace('<table> <tr> <td> <table>', '', $bufer);
		$bufer = str_replace('<tr> <td> </td> </tr> </table>', '', $bufer);
		$bufer = str_replace('Estudio Delion S.R.L PATENTES, MARCAS, DERECHOS DE AUTOR, ANTI-PIRATERIA E-mail : wp@estudiodelion.com.pe http: www.estudiodelion.com.pe </td> </tr> </table>', '', $bufer);
		$bufer = str_replace('<tr> <td> SOLICITUDES DE REGISTROS DE MARCAS PUBLICADAS EN EL DIARIO OFICIAL EL PERUANO </td> </tr>', '', $bufer);
        $bufer = str_replace('<tr> <td> LAS MARCAS MOSTRADAS EN ESTA PAGINA NO SON PROPIEDAD DE ESTUDIO DELION, SINO DE SUS RESPECTIVOS SOLICITANTES </td> </tr>', '', $bufer);
		$bufer = str_replace('<tr> <td> La Fecha Límite para presentar Oposición a estas Solicitudes (Expediente) es de (30) Treinta días útiles, contabilizados a partir del día útil siguiente a la fecha de publicación; cuyo plazo aparece en el campo Fecha para Oposición. </td> </tr>', '', $bufer);
		$bufer = str_replace('* Las marcas mostradas en esta pagina no son propiedad de Estudio Delion, sino de sus respectivos solicitantes. GRÁFICOS MARCAS ---&amp;gt;&amp;gt;', '', $bufer);
		$bufer = str_replace('<table> <tr> <td> QUIENES SOMOS - SERVICIOS -CIRCULARES - WPATENTS - WPMARK </td> </tr> </table>', '', $bufer);
		$bufer = str_replace("<table> <tr> <td> PERU", "", $bufer);
		$bufer = str_replace("</td> </tr>  </table> <table", "<table", $bufer);
		$bufer = str_replace("</td> </tr>   <tr> <td>TIPO", "", $bufer);
		$bufer = str_replace('<table id="marcas"> <tr><td> Marca</td>', '<table> <tr><td> Marca</td>', $bufer);
		
		
		$bufer = "<!DOCTYPE html>\n<html>\n<body>\n" . $bufer . "\n</body></html>";
				
		save_txtinfile($carpeta . "/" . $txtfile2, $bufer);
        unlink($carpeta . "/" . $txtfile);
		
		//Contenido del archivo
		$xmlstr = $carpeta.$txtfile2;
		
		// new dom object
		$dom = new DOMDocument();

		//load the html
		$html = $dom->loadHTMLFile("$xmlstr");

		//discard white space 
		$dom->preserveWhiteSpace = false; 

		//the table by its tag name
		$tables = $dom->getElementsByTagName('table'); 

		//get all rows from the table
		$rows = $tables->item(0)->getElementsByTagName('tr'); 

		// loop over the table rows
		foreach ($rows as $row) { 
			// get each column by tag name
			$index = 1;
			$cells = $row->getElementsByTagName('td'); 
			foreach( $cells as $cell ) {
			
			  if ( $index == 1 ) $denominacion = $cell->nodeValue;
			  if ( $index == 2 ) $clase = $cell->nodeValue;
			  if ( $index == 3 ) $expediente = $cell->nodeValue;
			  if ( $index == 4 ) $fecha_solicitud = $cell->nodeValue;
			  if ( $index == 5 ) $titular = $cell->nodeValue;
			  if ( $index == 6 ) $domicilio = $cell->nodeValue;
			  if ( $index == 7 ) $plazo_opo = $cell->nodeValue;
			  if ( $index == 8 ) $tipomarca = $cell->nodeValue;

			   $index += 1;
			}
			add_marca($np, utf8_decode($denominacion), $tipo_denomi, $tipomarca, $expediente, $fecha_solicitud, utf8_decode($titular), $direccion, utf8_decode($domicilio), $apoderado, $dir_apo, $clase, $ngac, $fecha_solicitud, $plazo_opo, utf8_decode($prioridad), utf8_decode($prodyservs));
			
		}				
		
		//Genero Archivo XML FINAL GAC
		$query = 	"SELECT * FROM sam_precarga_gac_exterior";
		$result = mysql_query($query);
	 
		$xml = new DomDocument('1.0', 'UTF-8');
	
		//NODO PRINCIPAL
		$root = $xml->createElement('gacpe');
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
		
		$child = $xml->createElement('fecha_pub');
		$child = $noticia->appendChild($child);
		$value = $xml->createTextNode(trim($array['fecha_solicitud']));
		$value = $child->appendChild($value);
		
		$child = $xml->createElement('plazo_opo');
		$child = $noticia->appendChild($child);
		$value = $xml->createTextNode(trim($array['plazo_opo']));
		$value = $child->appendChild($value);
		
		}

		$xml->formatOutput = true;
		 
		$strings_xml = $xml->saveXML();
		$xml->save('XML/GACPE'.$ngac.'.xml');	
				  
	
	}
	unlink($carpeta . "/" . $txtfile2);	
	
	//limpio tabla para la proxima GAC.
	//$limpiatable = "TRUNCATE sam_precarga_gac_exterior";
	//$rsq = mysql_query($limpiatable);
}
?>