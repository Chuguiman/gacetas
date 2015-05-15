<?php


//Ingresa en Tabla de Precarga
	$host = "localhost";
	$user = "root";
	$pw = "";
	$db = "test";
		
	$link = mysql_connect ($host,$user,$pw) or die ("problemas");
	mysql_select_db ($db,$link) or die ("problemas db");
		
$data = array();		

function add_marca($np, $denominacion, $tipo_denomi, $tipomarca, $expediente, $fecha_solicitud, $titular, $direccion, $domicilio, $apoderado, $dir_apo, $clase, $ngac, $fecha_pub, $plazo_opo)  {
	global $data, $db;

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
																																							
 mysql_query($db);
 
	$sql2="DELETE FROM `sam_precarga_gac_exterior` WHERE (`expediente`='') AND (`clases`='');";
	mysql_query ($sql2);
	
	$sql3="DELETE FROM `sam_precarga_gac_exterior` WHERE (`expediente`='ACTA') AND (`clases`='CLASE');";
	mysql_query ($sql3);
// echo $db."<BR><BR>";
 
 $data []= array(
  'FECHA SOLICITUD' => $fecha_solicitud,
  'ACTA' => $expediente,
  'CLASE' => $clase,
  'DENOMINACION' => $denominacion,
  'TITULAR' => $titular,
  'PAIS' => $domicilio,
  'FECHA VENCE' => $plazo_opo  
  );
}

if ( $_FILES['file']['tmp_name'] )
{
 $dom = DOMDocument::load( $_FILES['file']['tmp_name'] );
 $rows = $dom->getElementsByTagName( 'Row' );
 $first_row = true;
 foreach ($rows as $row)
 {
   if ( !$first_row )
   {
	  $fecha_solicitud= "";
	  $expediente= "";
	  $clase= "";
	  $denominacion= "";
	  $titular= "";
	  $domicilio= "";
	  $plazo_opo= "";

     $index = 1;
     $cells = $row->getElementsByTagName( 'Cell' );
     foreach( $cells as $cell )
     {
       $ind = $cell->getAttribute( 'Index' );
       if ( $ind != null ) $index = $ind;

      if ( $index == 1 ) $fecha_solicitud = $cell->nodeValue;
	  if ( $index == 2 ) $expediente = $cell->nodeValue;
	  if ( $index == 3 ) $clase = $cell->nodeValue;
	  if ( $index == 4 ) $denominacion = $cell->nodeValue;
	  if ( $index == 5 ) $titular = $cell->nodeValue;
	  if ( $index == 6 ) $domicilio = $cell->nodeValue;
	  if ( $index == 7 ) $plazo_opo = $cell->nodeValue;

    $index += 1;
    }
    //Ingresa en Tabla de Precarga
	$ngac = "14";
	add_marca($np, utf8_decode($denominacion),  utf8_decode($tipo_denomi),  utf8_decode($tipomarca), $expediente, substr($fecha_solicitud, 0, -13), utf8_decode($titular),  utf8_decode($direccion), utf8_decode($domicilio),  utf8_decode($apoderado), $dir_apo, $clase, $ngac, $fecha_pub, substr($plazo_opo, 0, -13));
		
   }
   $first_row = false;
 }
}
	//Exportamos datos a XML
		$query = 	"SELECT * FROM sam_precarga_gac_exterior";
		$result = mysql_query(utf8_decode($query));
	 
		$xml = new DomDocument('1.0', 'UTF-8');
	
		//NODO PRINCIPAL
		$root = $xml->createElement('GACPY');
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
		$xml->save('XML/GACPY'.$ngac.'.xml');
		
?>
<html>
<body>
<h3>Los Datos han sido insertados correctamente!</h3>
<table border="1px">
  <tr>
	<th>FECHA DE SOLICITUD</th>
	<th>EXPEDIENTE</th>
	<th>CLASES</th>
	<th>DENOMINACION</th>
	<th>TITULAR</th>
	<th>PAIS</th>
	<th>FECHA VENCE</th>
  </tr>
  <?php foreach( $data as $row ) { ?>
  <tr>
  <td><?php echo( $row['FECHA SOLICITUD'] ); ?></td>
  <td><?php echo( $row['ACTA'] ); ?></td>
  <td><?php echo( $row['CLASE'] ); ?></td>
  <td><?php echo( $row['DENOMINACION'] ); ?></td>
  <td><?php echo( $row['TITULAR'] ); ?></td>
  <td><?php echo( $row['PAIS'] ); ?></td>
  <td><?php echo( $row['FECHA VENCE'] ); ?></td>
  </tr>
  <?php } ?>
  </table>
<p>	Ver <a href="XML/">Aquí</a> Archivo XML.</p>
</body>
</html>