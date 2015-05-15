<?PHP	

	require_once ("../../clases/_func_tranformar_gac_ext.php");

	$pais = "DO";
	$ngac = $_POST["gaceta"];
	
	crea_xml_gac ($pais, $ngac);
	
	echo '<div class="demo-download">
			<img src="img/xml-icon.png">
			</div>
			<a class="btn btn-block btn-lg btn-info descarga_xml" href="do/XML">Download</a>
			<p class="demo-download-text">Carpeta de archivos XML!</p>
			</div>';
	
?>	