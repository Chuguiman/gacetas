<?php
/*------------------------DIRECCION WEB DE DESCARGA---------------------------*/
$dirweb0="https://www.rpi.gob.gt/rpigaceta/Home/Edicto/";
/*-----------------------NOMBRE Y CARPETA DE DESCARGA-------------------------*/
$nompais="GT";
$numdesc= date("dmY");
$nombre= $nompais.$numdesc;
$filename = $nombre.".html";
$fd2 = fopen ($filename, "w");
/*-----------------------RANGO------------------------------------------------*/

$conn=mysql_connect('localhost','root','');
mysql_select_db('test',$conn);

   $sql="Select trim(a.nreg) AS X From sam_gacgt AS a ";
   $rsql=mysql_query($sql);
   if(mysql_num_rows($rsql)>0){
		for($i=0; $i<mysql_num_rows($rsql); $i++){
			$dsd=mysql_result($rsql, $i, "X");
			$dirweb=$dirweb0.$dsd;
			echo $dirweb."<br>";
			
			$fd= file("$dirweb");
			$consulta=$fd[34];
			$consulta2=str_replace("No se encontraron registros","",$consulta);
			if($consulta!=$consulta2){

			}else{
				$sizefd=sizeof($fd);
				for($i2=0; $i2<$sizefd; $i2++){
					$consulta2=$fd[$i2];
					$consulta=$consulta.$consulta2."\n";
				}
				fwrite($fd2,$consulta );
			}
		}
		fclose($fd2);
		//echo $dirweb."<br>";
	}

/*-----------------------ARCHIVO------------------------------------------------*/

$w = stream_get_wrappers();
echo '<BR>openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "<BR>";
echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "<BR>";
echo 'http wrapper: ', in_array('http', $w) ? 'si':'no', "<br />";
echo 'wrappers: ', var_dump($w);

?>