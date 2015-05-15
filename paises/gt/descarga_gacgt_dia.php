<?php

	require_once ("../../db/dbx.php");
	require_once ("../../clases/_func_tranformar_gac_ext.php");

	function descarga_gacgt_dia ($ir) {
		
		$ndia = date("dmy");
		$dirsolweb="https://www.rpi.gob.gt/rpigaceta/?fechaPublicacion=".$ir;
		//echo $dirsolweb;

		if ($dirsolweb != "") {
		    //Contenido de la página
		    $cadsw = str_replace("'", "\'", $cadsw);        
		    $cadsw = ret_txtfile2($dirsolweb);       
		    $cadsw = reemplazaracentoshtml($cadsw);
		    $cadsw = corregir_otrosacentos($cadsw);
		    $cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); //Elimina Contenido del HEAD
		    $cadsw = preg_replace("/<script[^>]*?>.*?<\/script>/si", "", $cadsw);
			$cadsw = preg_replace("/<input[^>]*?>/si", "", $cadsw);
			$cadsw = preg_replace("/class/", "type", $cadsw);
			$cadsw = preg_replace("/<form[^>]*?>.*?<\/form>/si", "", $cadsw);
			$cadsw = preg_replace("/<img[^>]*?>/si", "", $cadsw);
			$cadsw = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $cadsw);
			$cadsw = strip_tags($cadsw,"<div><a>");
			$cadsw = preg_replace('/>\r/', '>', $cadsw);
			$cadsw = preg_replace('/    </', '<', $cadsw);
			$cadsw = preg_replace('#</div>#', "", $cadsw);
			
			$patrones[0] = '/<div type="fechaEdicto">/';
			$patrones[1] = '/<div type="fechaPublicacion">/';
			$patrones[2] = '/<div type="expediente">/';
			$patrones[3] = '/<div type="nombre">/';
			$patrones[4] = '/<div type="clase">/';
			$patrones[5] = '/<div type="mandatario">/';
			$patrones[6] = '#<div type="pdfEdicto"><a href="\/rpigaceta\/Home\/Edicto\/#';
			$patrones[7] = '/<div type="titular">/';
			$patrones[8] = '/<div type="line">/';
			$patrones[9] = '#</a>|\[Descargar\]|Boletín Oficial del Registro de la Propiedad Intelectual - BORPI#';
							
			array_push($patrones, "/\s+/","#> <#");
			$reemplazos=array("<fechaEdicto>","</fechaEdicto>\r<fechapub>","</fechapub>\r<exp>","</exp>\r<denomi>","</denomi>\r<clases>","</clases>\r<apoderado>","</apoderado>\r<nedicto>"
								,"</nedicto>\r<titular>","</titular>\r","");
			
			array_push($reemplazos, " ",">\r<");    
			$cadsw = preg_replace($patrones, $reemplazos, $cadsw);
			
			$letrascod = array('&#209;','&#243;','&#201;','&#211;','&#237;','&#161;','&#241;','&#220;','&#233;','&#39;');
			$decletras = array('Ñ','ó','É','Ó','í','','ñ','U','é',"\'");
			$cadsw = str_replace($letrascod, $decletras, $cadsw);
			$cadsw = trim($cadsw);
			$cadsw = strip_tags($cadsw,"<fechaEdicto><fechapub><exp><denomi><clases><apoderado><nedicto><titular>");
			
			//echo $cadsw;
			
			$xmltrs = $ndia.".xml";
			$carpeta = "origen/";
			$cadsw = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r<pag>\r<marca>\r" . $cadsw . "\r</marca>\r</pag>";
			$cadsw = preg_replace('/\s(?=\s)/', '', $cadsw);
			save_txtinfile($carpeta.$xmltrs, $cadsw);
			
			$archorginen = $carpeta.$xmltrs;

			if (file_exists($archorginen)) {
				$xml2 = simplexml_load_file($archorginen);
			} else{
				exit("No se puede cargar el archivo xml!");
			}

			/*echo "<pre>\n";
			print_r($xml2);*/
			
			foreach ($xml2->marca as $marca) {
				
				for ($x=0; $x<count($marca); $x++) {
					$fechaEdicto = $marca->fechaEdicto[$x];
					$fechapub = $marca->fechapub[$x];
					$exp = $marca->exp[$x];
					$denomi = $marca->denomi[$x];
					$denomi = utf8_decode($denomi);
					$clases = $marca->clases[$x];		
					$apoderado = $marca->apoderado[$x];
					$apoderado = utf8_decode($apoderado);
					$nedicto = $marca->nedicto[$x];
					$nedicto = str_replace('">', '', $nedicto);
					$nedicto = trim($nedicto);
					//$nedicto = preg_replace("/[0-9]/", "$1", $cadsw);
					$nedicto = str_replace(" ", "", $nedicto);
					$titular = $marca->titular[$x];
					$titular = utf8_decode($titular);
					$plazo_opo = date('Y-m-d',strtotime('+60 days', strtotime($fechapub)));
					
					if(!$exp==""){
						$sql = "INSERT INTO `sam_precarga_gac_exterior` 
								(`denominacion`, `expediente`, `fecha_solicitud`, `titular`, `apoderado`, `clases`, `fecha_publicacion`, `plazo_opo`) 
							VALUES 
								('$denomi', '$exp', '$fechaEdicto', '$titular', '$apoderado', '$clases', '$fechapub', '$plazo_opo');";		
						$rsql = mysql_query($sql);
						//echo "<br>".$sql;
						//echo "<br>".$fechaEdicto." | ".$fechapub." | ".$exp." | ".$denomi." | ".$clases." | ".$apoderado." | ".$nedicto." | ".$titular;
						$sql2 = "INSERT INTO 
									`sam_gacgt` (`nreg`) 
								VALUES 
									('$nedicto');";
						$rsql2 = mysql_query($sql2);
					}
				
						
				}
				
			}
			unlink($archorginen);
			
		}
		
		/*------------------------DIRECCION WEB DE DESCARGA---------------------------*/
		$dirweb0="https://www.rpi.gob.gt/rpigaceta/Home/Edicto/";
		/*-----------------------NOMBRE Y CARPETA DE DESCARGA-------------------------*/
		$nompais="GT";
		$numdesc= date("dmY");
		$nombre= $nompais.$numdesc;
		$filename = $nombre.".xml";
		$fd2 = fopen ($filename, "w");
		/*-----------------------RANGO------------------------------------------------*/

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
		
		if ($filename!="") {
		    //Contenido de la página
		    $cadsw = str_replace("'", "\'", $cadsw);        
		    $cadsw = ret_txtfile2($filename);       
		    $cadsw = reemplazaracentoshtml($cadsw);
		    $cadsw = corregir_otrosacentos($cadsw);
		    $cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); //Elimina Contenido del HEAD
		    $cadsw = preg_replace("/<script[^>]*?>.*?<\/script>/si", "", $cadsw);
			$cadsw = preg_replace("/<style[^>]*?>.*?<\/style>/si", "", $cadsw);
			$cadsw = str_replace("Boletín Oficial del Registro de la Propiedad Intelectual - BORPI", "", $cadsw);
			$cadsw = preg_replace('/\s(?=\s)/', '', $cadsw);
			$cadsw = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $cadsw);
			$cadsw = preg_replace('/[\n\r\t]/', ' ', $cadsw); 
			$cadsw = strip_tags($cadsw,"<p>");
			$letrascod = array('&#209;','&#243;','&#201;','&#211;','&#237;','&#161;','&#241;','&#220;','&#233;','&#39;','&#205;','&#193;');
			$decletras = array('Ñ','ó','É','Ó','í','','ñ','U','é',"\'",'Í','Á');
			$cadsw = str_replace($letrascod, $decletras, $cadsw);
			$cadsw = preg_replace("/Solicita Registro| , ubicada en/", "</text>\r", $cadsw);
			$cadsw = preg_replace("/ de: /", "\r<text>", $cadsw);
			$cadsw = preg_replace('/> /', '>', $cadsw);
			$cadsw = preg_replace('/ </', '<', $cadsw);
			$cadsw = preg_replace("/<text>.*?<\/text>/si", "$0", $cadsw);
			$cadsw = preg_replace('/<text>[^<]*?<\/text>/si','<marca><domicilio pais="$0">$0</domicilio>', $cadsw);
			$cadsw = str_replace('Expediente: ', '<exp>', $cadsw);
			$cadsw = str_replace('. REGISTRO', '</exp></marca>', $cadsw);
			$cadsw = str_replace('<text>', '', $cadsw);
			$cadsw = str_replace('</text>', '', $cadsw);
			$cadsw = strip_tags($cadsw,"<domicilio><marca><exp>");
			$cadsw = preg_replace('/<\/domicilio>.*?[^<]*/si','</domicilio>', $cadsw);
			
			//echo $cadsw;
			
			$xmltrs = $filename;
			//$carpeta = "localhost/work/gacetas/paises/gt/";
			$cadsw = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r<pag>\r" . $cadsw . "\r</pag>";
			$cadsw = preg_replace('/\s(?=\s)/', '', $cadsw);
			save_txtinfile($carpeta.$xmltrs, $cadsw);
			
			$archorginenx = $carpeta.$xmltrs;
			

			if (file_exists($archorginenx)) {
				$xml = simplexml_load_file($archorginenx);
			} else{
				exit("No se puede cargar el archivo xml!");
			}
			/*echo "<pre>";
			print_r($xml);*/
			
			foreach ($xml->marca as $marca) {
				$domiciliox = $marca->domicilio["pais"];
				//$domiciliox = utf8_encode($domiciliox);
				$expw = $marca->exp;
				$expw = str_replace('-', '', $expw);
				
				$sqlx = "UPDATE `sam_precarga_gac_exterior` SET `domicilio`='$domiciliox' WHERE (`expediente`='$expw');";
				$rsqlx = mysql_query($sqlx);
				//echo $sqlx;
				$sqlz = "TRUNCATE sam_gacgt";
				$rsqlz = mysql_query($sqlz);				
			}
			//echo $archorginenx;
			
		}	
		unlink($filename);
		

			
	}

	$w = stream_get_wrappers();
	echo '<BR>openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "<BR>";
	echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "<BR>";
	echo 'http wrapper: ', in_array('http', $w) ? 'si':'no', "<br />";
	
	
?>