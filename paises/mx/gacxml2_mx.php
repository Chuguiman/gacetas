<?PHP
	require_once ('gacxmlmx.php');

	$ngac = $_POST["ngac"];
	$fechapub = $_POST["fechapub"];
	// $ngac = "20150101";
	// $fechapub = "2015-01-30";
	gacxml2_mx ($ngac, $fechapub);

?>
