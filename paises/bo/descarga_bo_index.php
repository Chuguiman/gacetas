<?PHP
	include_once '_func_xmlgac_bo.php';

	$carp = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'];
    $carpeta = str_replace("descarga_bo_index.php", "../tmp/", $carp);
	
	$inicio = $_POST["ngac"];
	$fechapub = $_POST["fechapub"];
	$cantidad = 1;
    
	//Descargamos del INPI
    $final = $inicio + $cantidad;
    for ($numsigno = $inicio; $numsigno < $final; $numsigno++) {
        desc_actaweb_bo($numsigno, $fechapub, $carpeta);
    }
	

	echo '	<br><br><br>
			<div class="col-xs-3">
				<div class="demo-download">
					<img src="img/xml-icon.png">
				</div>
				<a class="btn btn-block btn-lg btn-info descarga_xml" href="bo/XML">Download</a>
				<p class="demo-download-text">Carpeta de archivos XML!</p>
			</div>
		</div>';
?>