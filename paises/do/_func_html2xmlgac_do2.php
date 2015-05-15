<?php

require_once ("../../clases/_func_tranformar_gac_ext.php");

  
function desc_actaweb_do($signo, $carpeta) {
	
	$ngac=$signo;
	$nacta = $signo.".html"; 
	$dirsolweb = "origen/".$nacta;
    echo "<br>".$dirsolweb."<br>";
    
	
    if (trim($nacta) != "") {
        //Contenido de la página        
        $cadsw = ret_txtfile2($dirsolweb);
		if (mb_detect_encoding($cadsw, 'ISO-8859-1', TRUE) !== 'UTF-8') {//ISO-8859-1
			$cadsw = utf8_encode($cadsw);
		}
		
		$cadsw = strip_tags($cadsw,"<b><br>");
		$cadsw = trim($cadsw);
		$cadsw = str_replace("<b>", "<td>", $cadsw);
		$cadsw = str_replace("</b>", "</td>", $cadsw);
		$cadsw = str_replace("<td> ", "<td>", $cadsw);
		$cadsw = str_replace(" </td>", "</td>", $cadsw);
		$cadsw = str_replace("<br>", "</td><td>", $cadsw);
		
		//Retornar Archivo
		$txtfile  = $signo.".txt";
		$txtfile2 = $signo."x.html";
		
        save_txtinfile($carpeta . "/" . $txtfile, utf8_decode($cadsw));
		
		//Limpia
        $gestor = @fopen($carpeta . "/" . $txtfile, "r");
        if ($gestor) {
            while (!feof($gestor)) {
                $bufer.= fgetss($gestor, 4096, '<table><tr><td><th>');
            }
            fclose($gestor);
        }
		
		
		$bufer = preg_replace('/\s+/', ' ', $bufer);
		$bufer = preg_replace("/<tr[[:space:]]*([^>]*>)/", "<tr>", $bufer);
        $bufer = preg_replace("/<td[[:space:]]*([^>]*>)/", "<td>\n", $bufer);        
        $bufer = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $bufer);
        $bufer = preg_replace("/^[[:space:]]*([^<]*<)/", "<", $bufer);
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);  
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);


		$bufer = str_replace('<td> [210]</td>', '</td><tr><td>expediente', $bufer);
		$bufer = str_replace('<td> [220] FECHA</td>', 'fecha solicitud', $bufer);
		$bufer = str_replace('<td> [511] C.I.</td>', 'Clases', $bufer);
		$bufer = str_replace('<td> [561] MARCA</td>', 'Denominacion y tipo', $bufer);
		$bufer = str_replace('<td> [730] SOLICITANTE</td>', 'Titular', $bufer);
		$bufer = str_replace('<td> PRODUCTOS / SERVICIOS</td>', 'PRODUCTOS / SERVICIOS', $bufer);
		$bufer = str_replace('<td> EXPEDIENTE</td>', '</td></tr>', $bufer);
		$bufer = str_replace('<td> ( M A R C A S )', '', $bufer);
		$bufer = str_replace('fecha <td>', 'fecha</td><td>', $bufer);
		$bufer = str_replace('<td> </td>', '', $bufer);
		$bufer = str_replace('<td> E/', '</td></tr><tr><td>', $bufer);
		$bufer = str_replace('( TLT ).', '</td></td>', $bufer);
		$bufer = str_replace('</td></td>', '', $bufer);
		$bufer = str_replace('</tr></tr>', '</tr>', $bufer);
		$bufer = str_replace('D etalle de productos y ser vicios cor respondientes a la ', '', $bufer);
		$bufer = str_replace('confor me el tratado sobre derecho de marca', '', $bufer);
		$bufer = str_replace('<td> P U B L I C A C I', '', $bufer);
		$bufer = str_replace('N S O L I C I T U D S I G N O S D I S T I N T I V O S</td><td> publicaci', '', $bufer);
		$bufer = str_replace('n de fecha </td><td>', '', $bufer);
		$bufer = str_replace('</td><tr><td>expediente</td>', '<tr><td>expediente</td>', $bufer);
		$bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);
		$bufer = str_replace("'", "\'", $bufer);
		$bufer = str_replace('&quot;', '\"', $bufer);
		
		
		$bufer = "<!DOCTYPE html>\n<html>\n<body>\n<table border='1px'>\n" . $bufer . "</td></tr>\n</table>\n</body></html>";
				
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
				
				if ( $index == 1 ) $expediente = $cell->nodeValue;
				if ( $index == 2 ) $fecha_solicitud = $cell->nodeValue;
				if ( $index == 3 ) $clase = $cell->nodeValue;
				if ( $index == 4 ) $denominacion = $cell->nodeValue;
				if ( $index == 5 ) $titular = $cell->nodeValue;
				if ( $index == 6 ) $productos = $cell->nodeValue;
			  

			   $index += 1;
			}
			add_marca($np, utf8_decode($denominacion), $tipo_denomi, $tipomarca, $expediente, $fecha_solicitud, utf8_decode($titular), $direccion, utf8_decode($domicilio), $apoderado, $dir_apo, $clase, $ngac, $fecha_pub, $plazo_opo, utf8_decode($prioridad), utf8_decode($productos));
			
		}				
	
	}
	unlink($carpeta . "/" . $txtfile2);	
	
}
?>