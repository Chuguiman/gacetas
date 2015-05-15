<?PHP

	include_once '_func_html2xmlgac_ar.php';
	require_once ('_func_xmlgac_ar.php');
	require_once ("../../db/dbx.php");

	$ngac = $_POST["ngac"];
	$fechapub = $_POST["fechapub"];

	gacxml2_ar ($ngac, $fechapub);

	$carp = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'];
    $carpeta = str_replace("descarga_ar_index.php", "../tmp/", $carp);
	
	$sql="Select trim(a.nreg) AS X From sam_descar_gac_ar AS a";
	$rsql=mysql_query($sql);
	
	if(mysql_num_rows($rsql)>0){
		for($i=0; $i<mysql_num_rows($rsql); $i++){
			$numsigno=mysql_result($rsql, $i, "X");
			desc_actaweb_ar($numsigno, $carpeta);
		
		}
	}
	
	
	echo '	<br><br><br>
			<div class="col-xs-3">
				<div class="demo-download">
					<img src="img/xml-icon.png">
				</div>
				<a class="btn btn-block btn-lg btn-info descarga_xml" href="../ar/XML">Download</a>
				<p class="demo-download-text">Carpeta de archivos XML!</p>
			</div>
		</div>';
?>