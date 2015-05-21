<?php
    include_once '../../../jhc/precarga/nueva/_func_pdf2txtgac.php';
    
	$ngac = "694";
	$nom_tit = "TIT";
	
	$txtgac = $nom_tit.$ngac;
	$xmlfile = $txtgac.".xml";
    print_r(xmlgactit2txt($xmlfile, $txtgac.".txt"));
?>
