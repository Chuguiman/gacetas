<?PHP 
	/*$host="localhost";
	$db="gacetas";
	$user="root";
	$password="";
	
	$conexion = new mysqli ($host, $user, $password, $db);
	
	if ($conexion->connect_error) {
		trigger_error ("No se puede conectar a la DB".$conexion->connect_error, E_USER_ERROR);
	}
	
	$conexion->query("SET NAMES UTF8");*/
	$host = "localhost";
	$user = "root";
	$pw = "";
	$db = "gacetas";
		
	$link = mysql_connect ($host,$user,$pw) or die ("problemas");
	mysql_select_db ($db,$link) or die ("problemas db");
	
?>