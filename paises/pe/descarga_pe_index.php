<?PHP

	include_once '_func_html2xmlgac_pe.php';

	$carp = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'];
    $carpeta = str_replace("descarga_pe_index.php", "../tmp/", $carp);

	$mes = $_POST["mes"];
	$inicio = $_POST["diadsd"];
	$cantidad = $_POST["cantidad"];
	$ngac = $_POST["ngac"];
	
	
	//Descargamos del Estudio de Lion(PERÚ)
    $final = $inicio + $cantidad;
    for ($numsigno = $inicio; $numsigno < $final; $numsigno++) {
        desc_actaweb_pe($mes, $numsigno, $carpeta, $ngac);
    }
		

	echo '	<br><br><br>
			<div class="col-xs-3">
				<div class="demo-download">
					<img src="img/xml-icon.png">
				</div>
				<a class="btn btn-block btn-lg btn-info descarga_xml" href="pe/XML">Download</a>
				<p class="demo-download-text">Carpeta de archivos XML!</p>
			</div>
		</div>';	

?>
