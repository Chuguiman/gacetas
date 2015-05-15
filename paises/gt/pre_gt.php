<?php
	require_once ('descarga_gacgt_dia.php');
	
	$fechapub = $_POST["fechapub"];
	descarga_gacgt_dia ($fechapub);
?>
