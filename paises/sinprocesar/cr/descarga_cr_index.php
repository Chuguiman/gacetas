<?PHP
	include_once '_func_xmlgac_cr.php';

	$carp = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'];
    $carpeta = str_replace("descarga_cr_index.php", "tmp/", $carp);
	
	$inicio="14112014";
	$cantidad = 1;
    
	//Descargamos del INPI
    $final = $inicio + $cantidad;
    for ($numsigno = $inicio; $numsigno < $final; $numsigno++) {
        desc_actaweb_cr($numsigno, $carpeta);
    }
	
	echo "<p>	Descargar <a href='XML/'>XML</a> Aquí.</p>";
?>