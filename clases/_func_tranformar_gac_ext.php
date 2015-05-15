<?php

//require_once ("../../clases/_func_tranformar_gac_ext.php");

function conectar($ipserver, $userdb, $pwduserdb, $tipo=1){
        if($tipo==1){
            $conexion = mysql_connect("localhost", "root", "");
			return $conexion;
        }
    }


    function selectdb($db,$conexion, $tipo=1){
        if($tipo==1){
            mysql_select_db($db);
        }
    }

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


	function reemplazaracentoshtml($cad){
		$mtexto   = array("Á","É","Í","Ó","Ú","á","é","í","ó","ú","Ñ","ñ");
		$mhtml=array("&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Ntilde;","&ntilde;");
		return str_replace($mhtml, $mtexto, $cad);
	}

	function corregir_otrosacentos($cad){
		$macentos = array("Ã?","Ã³","Ãº");
		$mtexto = array("Ó","ó","ú");
		
		return str_replace($macentos, $mtexto, $cad);    
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



	function add_marca($np, $denominacion, $tipo_denomi, $tipomarca, $expediente, $fecha_solicitud, $titular, $direccion, $domicilio, $apoderado, $dir_apo, $clase, $ngac, $fecha_pub, $plazo_opo, $prioridad, $prodyservs)  {
	global $data, $db;
	
	//Ingresa en Tabla de Precarga
	$host = "localhost";
	$user = "root";
	$pw = "";
	$db = "gacetas";
		
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
		`plazo_opo`,
		`prioridad`,
		`prodyservs`
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
			'$plazo_opo',
			'$prioridad',
			'$prodyservs'
		)";

	mysql_query ($db);
	//echo $db."<BR><BR>";
	
	//Caso en Bolivia
	$sql2=("DELETE FROM `sam_precarga_gac_exterior` WHERE (`expediente`=' Solicitud') AND (`clases`=' Clase');");
	mysql_query ($sql2);
	
	//Caso en Republica Dominicana
	$sql3=("DELETE FROM `sam_precarga_gac_exterior` WHERE (`expediente`='expediente') AND (`clases`=' Clases');");
	mysql_query ($sql3);

	$sql5=("UPDATE `sam_precarga_gac_exterior` SET `titular`= LTRIM (`titular`);");
	mysql_query ($sql5);

		   
	}

function crea_xml_gac ($pais, $ngac) {

	$host = "localhost";
	$user = "root";
	$pw = "";
	$db = "gacetas";
		
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

		echo "El Archivo XML GAC".$pais.$ngac.".xml fue creado con exito!";
		
		return;
		
}

?>