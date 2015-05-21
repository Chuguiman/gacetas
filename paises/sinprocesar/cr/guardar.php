<?php
	include("conexion.php");
	
	if (isset($_POST['ano'])) {
		$ano = $_POST['ano'];
		$expedi =$_POST['expedi'];
		$dia=$_POST['dd'];
		$mes=$_POST['mm'];
		$yea=$_POST['year'];
		$fpub=$yea."-".$mes."-".$dia;
		$tipomarca=$_POST['tipomarca'];
		$denomi=$_POST['denomi'];
		$titular=$_POST['titular'];
		$pais=$_POST['paistit'];
		$ngac=$_POST['ngac'];
		
		$clases = $_POST['clasesn'];

		$txtclase="";
		$conector="";
		
		foreach($clases as $value){
			$txtclase.=$conector.$value;
			$conector=",";
		}
		

	//echo $ano."<br/>".$expedi."<br/>". $fpub."<br/>". $tipomarca."<br/>". $denomi."<br/>".$txtclase."<br/>".$titular."<br/>". $pais ."<br/>".$ngac."<br/>";
	
	
  
	// Si entramos es que todo se ha realizado correctamente
	
	$link = mysql_connect ($host,$user,$pw) or die ("problemas");
	mysql_select_db ($db,$link) or die ("problemas db");

	// Con esta sentencia SQL insertaremos los datos en la base de datos
	mysql_query("INSERT INTO `gaccr` (`id`, `ano`, `expedi`, `fecha_pub`, `tipomarca`, `denomi`, `clasesn`, `titular`, `paistit`, `ngac`) 
	VALUES ('','$ano', '$expedi', '$fpub', '$tipomarca', '$denomi', '$txtclase', '$titular', '$pais', '$ngac')",$link);
	
	echo "<p ><a href='form_ingresodatos_cr.html' class='btn btn-block btn-lg btn-primary'>1 Registro </a><p/>";
	
	} else {
	
		echo "<p style='margin:0 auto 0 auto;'><a class='btn btn-block btn-lg btn-danger' href='#fakelink'>Error.</a><p/>"; 
	
	}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
    <title>Info datos</title>
	
	<!-- Loading Flat UI -->
    <link href="flat-ui.css" rel="stylesheet">

	</head>
  <body>
  </body>
</html>

