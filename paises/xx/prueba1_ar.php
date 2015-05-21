<?php
/*------------------------DIRECCION WEB DE DESCARGA---------------------------*/
$dirweb0="https://portaltramites.inpi.gob.ar/Docs/ResultadosConsultas/ResultadoSolicitudMarca2.asp?Va=";
/*-----------------------NOMBRE Y CARPETA DE DESCARGA-------------------------*/
$nompais="AR";
$numdesc= date("dmY");
$nombre= $nompais.$numdesc;
$filename = $nombre.".html";
$fd2 = fopen ($filename, "w");
/*-----------------------RANGO------------------------------------------------*/
//$dsd=$_GET["dsd"];
//$hst=$_GET["hst"];
$dsd=3294575;
$hst=3294576;
/*----------------------------------------------------------------------------*/
for($j1=$dsd; $j1<=$hst; $j1++){
        $consulta= "";
        $dirweb=$dirweb0.$j1."&Vb=M&Vc=0&Vd=";
		
		$fd= file("$dirweb");
			$consulta=$fd[34];
			$consulta2=str_replace("No se encontraron registros","",$consulta);
			if($consulta!=$consulta2){

			}else{
				$sizefd=sizeof($fd);
				for($i2=0; $i2<$sizefd; $i2++){
					$consulta2=$fd[$i2];
					$consulta=trim(utf8_decode($consulta));
					$consulta=$consulta.$consulta2."\n";
				}
				fwrite ($fd2,$consulta );
			}
		}
		fclose($fd2);
		//echo $dirweb."<br>";

$w = stream_get_wrappers();
echo '<BR>openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "<BR>";
echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "<BR>";
?>