<?php
	
	require_once ("../../clases/_func_tranformar_gac_ext.php");

	function desc_actaweb_sv4($archivo, $fechapub, $destino) {
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
	    $patrones[6] = "/<text>DIARIO OFICIAL.- San Salvador, (.*?)>/";
	    $patrones[7] = "/<text>P(.*?)gina:(.*?)>/";    
	    $patrones[8] = "/\s+/";//Quita Doble espacio
	    $patrones[9] = "#> <#";
	    $patrones[10]= "/> \(/";
	    $patrones[11]= "# </i>|</i>|</b>| </b>|<text>LA INFRASCRITA REGISTRADORA,</text>#";
	    $patrones[12]= "# </text>#";
	    $patrones[13] = "/No. de Expediente\:/";
	    $patrones[14] = "/No. de Presentaci(.*?)n\:|No. de Presentacion\:/";
	    $patrones[15] = "/CLASE\:/";
	    $patrones[16] = "#EL INFRASCRITO REGISTRADOR|<text>\r</text>|</text>\r</text>|<text>DIARIO</text>|<text>OFICIAL</text>|<text>SOLO</text>
	    |<text>PARA</text>|<text>CONSULTA</text>|<text>NO</text>|<text>TIENE</text>|<text>VALIDEZ LEGAL</text>|<text>SOLO</text>|<text></text>
	    |<text>LA INFRASCRITA REGISTRADORA</text>|\(n\)|<text></text>| presentado#";
	    $patrones[17] = "#<text>HACE SABER: Que a esta ofi cina se ha\(n\) presentado |<text>HACE SABER: Que a esta ofi cina se ha presentado |<text>HACE SABER: Que a esta ofi cina se ha#";
	    $patrones[18] = "#, en su calidad de APODERADO de</text>|, en su calidad de APODERADO ESPECIAL de</text>|, en su calidad de APODERADO de So-</text>|, en su calidad de APODERADO</text>|, en su calidad de GESTOR OFICIOSO de</text>|, en su calidad de APODERA-</text>|, en su calidad de APODE-</text>|, en su calidad de GESTOR</text>|, en su calidad de REPRESENTANTE LEGAL de</text>#";
	    $patrones[19] = "#, en su calidad de REPRESEN-</text>\r<text>TANTE LEGAL de|, en su calidad de REPRESENTANTE</text>\r<text>LEGAL de|, en su calidad de APODERADO</text>\r<text>de |, en su calidad de REPRESENTANTE LEGAL</text>\r<text>de |, en su calidad de APODERADO</text>\r<text>ESPECIAL de |, en su calidad de APODERADO</text>\r<text>GENERAL JUDICIAL de |, en su calidad de APODE-</text>\r<text>RADO de 
	    				|, en su calidad de REPRESENTAN-</text>\r<text>TE LEGAL de |, en su calidad de GESTOR OFICIOSO</text>\r<text>de |, en su calidad de APODERA-</text>\r<text>DO de |, en su calidad de GESTOR</text>\r<text>OFICIOSO de |, en su calidad</text>\r<text>de REPRESENTANTE LEGAL de |, en su calidad de REPRESENTANTE</text>|, en su calidad de APODE-</text>\r<text>RADO de 
	    				|, en su calidad de</text>\r<text>APODERADO ESPECIAL de |, en su calidad de APODERADO ESPE-</text>\r<text>CIAL de |, en su calidad de APO-</text>\r<text>DERADO ESPECIAL de |, en su calidad de APODE-</text>\r<text>RADO de |, en su calidad de APODERA-</text>\r<text>DO ESPECIAL de |, en su calidad de APO-</text>\r<text>DERADO de |
	    				|, en su calidad de APODE-</text>\r<text>RADO ESPECIAL de |, en su calidad de APODERADO ESPECIAL</text>\r<text>de #";
	    $patrones[20] = "#, en calidad de</text>\r<text>PROPIETARIOS, solicitando el registro de la|, en su</text>\r<text>calidad de PROPIETARIO, solicitando el registro de la |, en</text>\r<text>su calidad de PROPIETARIO, solicitando el registro de la |<text>en su calidad de PROPIETARIO, solicitando el registro de la 
	    				|, en su calidad de PROPIETARIO, solicitando el registro de la</text>\r<text>|<text>calidad de PROPIETARIO, solicitando el registro de la|, en calidad de PRO-</text>\r<text>PIETARIOS, solicitando el registro de la|<text>en su calidad de PROPIETARIO, solicitando el registro de la|, en su calidad de PROPIETARIO, solicitando el registro</text>\r<text>de la 
	    				|, en su calidad de</text>\r<text>PROPIETARIO, solicitando el registro de la |, en su calidad de PROPIETARIO, solicitando el registro</text>\r<text>de la |, en su calidad de PROPIE-</text>\r<text>TARIO|,</text>\r<text>en su calidad de |, en su calidad</text>\r<text>de PROPIETARIO|, en su calidad de PRO-</text>\r<text>PIETARIO#";
	    $patrones[21] = "#, en su calidad de APODERADO de |, en su calidad de GESTOR OFICIOSO de #";
	    $patrones[22] = "#, de</text>\r<text>nacionalidad |, de nacionalidad |, de na-</text>\r<text>cionalidad |, de nacionalidad</text>\r<text>|. de nacionalidad |, de nacio-</text>\r<text>nalidad #";
	    $patrones[23] = "#<text>de nacionalidad #";
	    $patrones[24] = "#, solicitando el registro de la |, solicitando el registro</text>\r<text>de la |, solicitando</text>\r<text>el registro de la |,</text>\r<text>solicitando el registro de la |, solicitando el registro de</text>\r<text>la |, solicitando el</text>\r<text>registro de la |, solicitando el registro del</text>\r<text>
	    				|, solici-</text>\r<text>tando el registro de la | solicitando el registro de la |, solicitando el registro del |, solicitando el registro de la</text>\r<text>EXPRESION O |, solicitando el registro de la</text>\r<text>|, soli-</text>\r<text>citando el registro de la #";
	    $patrones[25] = "#, solicitando el registro del NOMBRE</text>\r<text>COMERCIAL,</text>|, solicitando</text>\r<text>el registro del NOMBRE COMERCIAL,</text>|<text>solicitando el registro del NOMBRE COMERCIAL.</text>#";
	    $patrones[26] = "#<text>Consistente en: la palabra |<text>Consistente en: las palabras |<text>Consistente en: la expresión |<text>Consistente en: |<text>Consistente en: los números #";
	    $patrones[27] = "#, que servirá para: AMPARAR: |, que servirá para:</text>\r<text>AMPARAR |, que servirá para: AMPARAR:</text>\r<text>|, que servirá</text>\r<text>para: AMPARAR: |, que servirá para:</text>\r<text>AMPARAR: |, que servirá para: AMPARAR</text>\r<text>|, que servirá para: AMPA-</text>\r<text>RAR: |, que servirá para: AM-</text>\r<text>PARAR: |, que</text>\r<text>servirá para: AMPARAR: |, que servirá</text>\r<text>para: AMPARAR|
	    				|,</text>\r<text>que servirá para: AMPARAR: |, que servirá para: AMPA-</text>\r<text>RAR |, que servirá para: IDENTIFICAR UNA</text>\r<text>EMPRESA DE COMERCIO,|, que servirá para:</text>\r<text>IDENTIFICAR UN|,</text>\r<text>que servirá para: IDENTIFICAR |, que servirá para: IDENTIFICAR</text>\r<text>EL ESTABLECIMIENTO|, que</text>\r<text>servirá para: |, que servirá para: IDEN-</text>\r<text>TIFICAR UN |
	    				|, que servirá</text>\r<text>para: IDENTIFICAR |. La marca</text>\r<text>a la que hace |, que servirá para: IDENTIFI-</text>\r<text>CAR UN |, que servirá para: AM-</text>\r<text>PARAR |, que ser-</text>\r<text>virá para: AMPARAR: #";
	    $patrones[28] = "#<text>La solicitud fue presentada el día #";
	    $patrones[29] = "#<text>REGISTRO DE LA PROPIEDAD INTELECTUAL,|<text>DIRECCION DE PROPIEDAD INTELECTUAL,#";
	    $patrones[30] = "/<text> |<text>Las palabras |<text>La palabra |<text>La expresión |<text>DO ESPECIAL de |<text>DO de |<text>la palabras |<text>RADO de |<text>OFICIOSO de /";
	    			

	    array_push($patrones, "/\s+/","#> <#");
	    $reemplazos=array("","","","<text>","","","<text>",""," ",">\r<",">(", "", "</text>","</text>\r<text>===========</text>\r<text>(21)","(20)","(511)","","<text>740</text>\r<text>","</text>\r<text>730</text>\r",
	    					"</text>\r<text>730</text>\r<text>","</text>\r<text>730</text>\r<text>PROPIETARIO</text>\r<text>551</text>\r<text>","</text>\r<text>730</text>\r<text>",
	    					"</text>\r<text>731</text>\r<text>","<text>731</text>\r<text>","</text>\r<text>551</text>\r<text>","</text>\r<text>551</text>\r<text>NOMBRE COMERCIAL</text>\r",
	    					"<text>540</text>\r<text>","</text>\r<text>570</text>\r<text>","<text>22</text>\r<text>","<text>=======================</text>\r<text>","<text>");
	    
	    array_push($reemplazos, " ",">\r<");    
	    $contenido = preg_replace($patrones, $reemplazos, $contenido);
	    $contenido = str_replace("'", "\'", $contenido);
	    $contenido = str_replace("º", "o.", $contenido);
	    //echo $contenido;
	    //Expresiones regulares 2 (Organiza Texto y termina Limpieza)
	    $mcods=array("21","20","511","740","730","731","551","540","570","220","FIN");
	    for($i=0; $i<sizeof($mcods)-1; $i++){
	        $patronx="/>\((".$mcods[$i].")\) /";
	        $contenido = preg_replace($patronx, ">$1</text>\r<text>", $contenido);

	    }
		
		$contenido = preg_replace("#</text>\r</text>#", "</text>", $contenido);

	    //echo $contenido;
	    
	    //LEER XML-------------------------//
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
				case "===========":
				$act = "===========";
					if($j>=0){
						$datosgenerales[$j]=$datosbasicos;
						$datosbasicos = array();
					}
					$j++;
					break;
				case "21":
					$act = "exp";
					break;
				case "20":
					$act = "exp2";
					break;
				case "511":
					$act = "clases";
					break;
				case "740":
					$act = "apoderado";
					break;
				case "730":
					$act = "titular";
					break;
				case "731":
					$act = "domicilio";
					break;
				case "551":
					$act = "signo";
					break;
				case "540":
					$act = "denomi";
					break;
				case "570":
					$act = "pys";
					break;
				case "22":
					$act = "fechapres";
					break;
				case "=======================":
					$act = "fin";
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
		// echo "<pre>";
		// print_r($datosgenerales);
		//var_dump($datosgenerales);
		
		foreach ($datosgenerales as $datoadato) {
			$expediente = substr(($datoadato["exp2"]), 0, 11);
			$clases     = substr(($datoadato["clases"]), 0 , 20);
			$clases = preg_replace("/LA INFRASCRITA R/", "", $clases);
			$apoderado  = strtoupper($datoadato["apoderado"]);
			$titular    = strtoupper($datoadato["titular"]);
			$domicilio    = substr((trim(strtoupper($datoadato["domicilio"]))), 0, 14);
			$signo    = strtoupper($datoadato["signo"]);
			$signo = preg_replace("/- /", "", $signo);
			$signo = ereg_replace(" ?(, +){1}.*", "", $signo);
			$denominacion = strtoupper($datoadato["denomi"]);
			$denominacion = ereg_replace(". ?(, +){1,2}.*", "", $denominacion);
			$denominacion = ereg_replace(". ?(; +){1}.*", "", $denominacion);
			$pys = strtoupper($datoadato["pys"]);
			
			$fecha_pres = strtoupper($datoadato["fechapres"]);
			$fecha_pres = str_replace("AñO", "", $fecha_pres);
			$fecha_pres = str_replace("A?O", "", $fecha_pres);
			$fecha_pres = str_replace("a?o", "", $fecha_pres);
			$fecha_pres = str_replace("DíA", "", $fecha_pres);
			$fecha_pres = str_replace("d?a", "", $fecha_pres);
			$fecha_pres = str_replace("D?A", "", $fecha_pres);
			$fecha_pres = str_replace("MIL", "", $fecha_pres);
			$fecha_pres = str_replace("DIA", "", $fecha_pres);

			$fecha_pres = str_replace("UNO", "01", $fecha_pres);
			$fecha_pres = str_replace("PRIMERO", "01", $fecha_pres);
			$fecha_pres = str_replace("DOS", "02", $fecha_pres);
			$fecha_pres = str_replace("TRES", "03", $fecha_pres);
			$fecha_pres = str_replace("CUATRO", "04", $fecha_pres);
			$fecha_pres = str_replace("CINCO", "05", $fecha_pres);
			$fecha_pres = str_replace("SEIS", "06", $fecha_pres);
			$fecha_pres = str_replace("SIETE", "07", $fecha_pres);
			$fecha_pres = str_replace("OCHO", "08", $fecha_pres);
			$fecha_pres = str_replace("NUEVE", "09", $fecha_pres);
			$fecha_pres = str_replace("DIEZ", "10", $fecha_pres);
			$fecha_pres = str_replace("ONCE", "11", $fecha_pres);
			$fecha_pres = str_replace("DOCE", "12", $fecha_pres);
			$fecha_pres = str_replace("TRECE", "13", $fecha_pres);
			$fecha_pres = str_replace("CATORCE", "14", $fecha_pres);
			$fecha_pres = str_replace("QUINCE", "15", $fecha_pres);
			$fecha_pres = str_replace("DIECISéIS", "16", $fecha_pres);
			$fecha_pres = preg_replace("/DIECISIETE|DIECI07/", "17", $fecha_pres);
			$fecha_pres = preg_replace("/DIECIOCHO|DIECI08/", "18", $fecha_pres);
			$fecha_pres = preg_replace("/DIECINUEVE|DIECI09/", "19", $fecha_pres);
			$fecha_pres = str_replace("VEINTE", "20", $fecha_pres);
			$fecha_pres = preg_replace("/VEINTIUNO|VEINTI01/", "21", $fecha_pres);
			$fecha_pres = str_replace("VEINTIDOS", "22", $fecha_pres);
			$fecha_pres = preg_replace("/VEINTID(.*?)S/", "22", $fecha_pres);
			$fecha_pres = str_replace("VEINTITRéS", "23", $fecha_pres);
			$fecha_pres = str_replace("VEINTI04", "24", $fecha_pres);
			$fecha_pres = str_replace("VEINTI05", "25", $fecha_pres);
			$fecha_pres = str_replace("VEINTIS?IS", "26", $fecha_pres);
			$fecha_pres = preg_replace("/VEINTIS(.*?)IS/", "26", $fecha_pres);
			$fecha_pres = str_replace("VEINTI07", "27", $fecha_pres);
			$fecha_pres = str_replace("VEINTI08", "28", $fecha_pres);
			$fecha_pres = str_replace("VEINTI09", "29", $fecha_pres);
			$fecha_pres = str_replace("TREINTA", "30", $fecha_pres);
			$fecha_pres = str_replace("30 Y 01", "31", $fecha_pres);

			$fecha_pres = str_replace("DE ENERO DEL", "/01/", $fecha_pres);
			$fecha_pres = str_replace("DE FEBRERO DEL", "/02/", $fecha_pres);
			$fecha_pres = str_replace("DE MARZO DEL", "/03/", $fecha_pres);
			$fecha_pres = str_replace("DE ABRIL DEL", "/04/", $fecha_pres);
			$fecha_pres = str_replace("DE MAYO DEL", "/05/", $fecha_pres);
			$fecha_pres = str_replace("DE JUNIO DEL", "/06/", $fecha_pres);
			$fecha_pres = str_replace("DE JULIO DEL", "/07/", $fecha_pres);
			$fecha_pres = str_replace("DE AGOSTO DEL", "/08/", $fecha_pres);
			$fecha_pres = str_replace("DE SEPTIEMBRE DEL", "/09/", $fecha_pres);
			$fecha_pres = str_replace("DE OCTUBRE DEL", "/10/", $fecha_pres);
			$fecha_pres = str_replace("DE NOVIEMBRE DEL", "/11/", $fecha_pres);
			$fecha_pres = str_replace("DE DICIEMBRE DEL", "/12/", $fecha_pres);

			$fecha_pres = str_replace("DOS MIL DOCE.", "2012", $fecha_pres);
			$fecha_pres = str_replace("DOS MIL TRECE.", "2013", $fecha_pres);
			$fecha_pres = str_replace("DOS MIL CATORCE.", "2014", $fecha_pres);
			$fecha_pres = str_replace("DOS MIL QUINCE.", "2015", $fecha_pres);

			$fecha_pres = str_replace("/  02  02.", "/2002", $fecha_pres);
			$fecha_pres = str_replace("/  02  03", "/2003", $fecha_pres);
			$fecha_pres = str_replace("/  02  05.", "/2005", $fecha_pres);
			$fecha_pres = str_replace("/  02  13.", "/2013", $fecha_pres);
			$fecha_pres = str_replace("/  02  14.", "/2014", $fecha_pres);
			$fecha_pres = str_replace("/  02  15.", "/2015", $fecha_pres);
			$fecha_pres = str_replace(" /", "/", $fecha_pres);

			$fecha_pres = substr($fecha_pres, 0, 12);

			$ngac = substr($archivo, 7, -4);

			$plazo_opo = date('Y-m-d',strtotime('+60 days', strtotime($fechapub)));

			add_marca($np, utf8_decode($denominacion),  utf8_decode($signo),  utf8_decode($tipomarca), $expediente, trim($fecha_pres), utf8_decode($titular),  utf8_decode($direccion), utf8_decode($domicilio),  utf8_decode($apoderado), $dir_apo, $clases, $ngac, $fechapub, $plazo_opo, utf8_decode($prioridad), utf8_decode($prodyservs));
			/*echo $expediente." ".$clases." ".$apoderado." ".$titular." "
			.$domicilio." ".$signo." ".$denominacion." ".$pys." ".$fecha_pres."<br><br><br>";*/
		}	
		echo "OK";
		
	    	


	}

	

?>