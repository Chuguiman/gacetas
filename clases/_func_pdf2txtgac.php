<?php

//include_once '../../clases/func_sic.php';

//$numgac=643;
//$carpeta="C:\AppServ\www\jhc\precarga\tmp";
//$origen=$carpeta."\GACETA".$numgac.".xml";
//$destino=$carpeta."\GACETA".$numgac.".txt";
//xmlgac2txt($origen,$destino);

//$carpeta="C:/AppServ/www/jhc/precarga/tmp";
//$anorad="12";
//$numrad="61842";//Mixta Multiclase
//$numrad="55446";//Figurativa una clase
//$numrad="54779";//Nominativa una clase
//$numrad="54781";//Tridimensional una clase
//$numrad="56191";//Lema una clase
//$numrad="59469";//Nombre Comercial una clase
//$numrad="51441";//Denominación de Origen
//$numrad="54343";//Marca Colectiva//
//$numrad="53437";//Enseña Comercial
//$numrad="047989";//Sonora (año 09)//
//$numrad="047635";//Olfativa (año 05)
//$numrad="173452";//Marcas de Certificación
//$numrad="17932";//Multiples Titulares (año 06)
//$numrad="45474";//Con Prioridad

//ret_mmarcas_solweb($anorad,$numrad,$carpeta);

//retorna en una cadena el contenido de un archivo
function ret_txtfile1($archivo){
    $gestor = fopen($archivo, "r");
    $contenido = fread($gestor, filesize($archivo));
    fclose($gestor);
    return $contenido;
}

function ret_txtfile2($archivo){
    $gestor = fopen($archivo, "rb");
    $contenido = stream_get_contents($gestor);
    fclose($gestor);
    return $contenido;    
}


function save_txtinfile($nombre_archivo,$contenido) {
    if (!$gestor = fopen($nombre_archivo, 'w')) {
        return 1;//No se puede abrir y/o crear el archivo
    }
    // Escribir $contenido en archivo.
    if (fwrite($gestor, $contenido) === FALSE) {
        return 2;//No se deja escribir el archivo
    }
    fclose($gestor);
    return 0;
}

function xmlgac2txt($xmlfile,$txtfile){
    $mmarcas = ret_mmarcas_xmlgac2($xmlfile);//SIN <B>
    //$mmarcas = ret_mmarcas_xmlgac($xmlfile);//Con <B>
    //$sl="<BR>";
    $sl="\n";
    $flecha="----------------------->".$sl;
    $cad=$flecha;
    for($i=0; $i<sizeof($mmarcas); $i++){
        if(trim($mmarcas[$i][0])!=""){
            $cad.="-denomi: ".$mmarcas[$i][0].$sl;
        }
        $cad.="-clase: ".$mmarcas[$i][9].$sl;
        $cad.="-fechap: ".$mmarcas[$i][1].$sl;
        $cad.="-numpub: ".$mmarcas[$i][3].$sl;
        $anoexp=$mmarcas[$i][2];
        $manoexp=  explode("-", $anoexp);
        
        $concontrol=false;
        $npact=$mmarcas[$i][3]*1;
        if($i>0){
            if($i==sizeof($mmarcas)-1){
                $npant=$mmarcas[$i-1][3]*1;
                if($npact==$npant){
                    $concontrol=true;
                }                
            }else{
                $npsig=$mmarcas[$i+1][3];
                $npant=$mmarcas[$i-1][3]*1;                
                if($npact==$npant || $npact==$npsig){                    
                    $concontrol=true;
                }                 
            }
        }else{            
            $npsig=$mmarcas[$i+1][3]*1;
            if($npact==$npsig){
                $concontrol=true;
            }
        }        
        if($concontrol){
            $cadaec="-anoexp: ".$manoexp[0]." ".$manoexp[1]." ".$mmarcas[$i][9].$sl;
        }else{
            $cadaec="-anoexp: ".$manoexp[0]." ".$manoexp[1].$sl;            
        }       
        $cad.=$cadaec;
        $cad.="-solic: ".$mmarcas[$i][6].$sl;
        $domic = str_replace("-", ", ", str_replace(",", " ", $mmarcas[$i][8]));
        $cad.="-domic: ".$domic.$sl;
        $cad.="-apod: ".$mmarcas[$i][7].$sl;
        $cad.="-tipo: ".$mmarcas[$i][5].$sl;
        $cad.="-Prodserv:  ".$mmarcas[$i][10].$sl;
        if(trim($mmarcas[$i][11])!=""){
            $cad.="-Prioridad:  ".$mmarcas[$i][11].$sl;
        }        
        $cad.=$flecha;        
    } 
    save_txtinfile($txtfile,$cad);
}


function ret_mmarcas_xmlgac($xmlfile){
    //Contenido del archivo
    $xmlstr = ret_txtfile1($xmlfile);
    //Lectura de XML
    $xml = new SimpleXMLElement($xmlstr);

    $tant = "";
    $consecm = -1;
    $mmarcas=array();
    //$num;
    foreach ($xml->page as $pagina) {
        foreach ($pagina->text as $texto) {
            $tant = $tact;
            $tact = "";
            foreach ($texto->b as $tit) {
                $tit = trim($tit);
                $titx=  str_replace("SUPERINTENDENCIA DE INDUSTRIA Y COMERCIO", "", $tit);
                $titx=  str_replace("GACETA No", "", $titx);
                $titx=  str_replace("SOLICITUDES DE MARCA", "", $titx);
                $titx=  str_replace("Página:", "", $titx);
                $titx=  str_replace("SOLICITUDES DE LEMAS", "", $titx);
                if($titx==$tit){
                    $letini = substr($tit,0,1);
                    if($letini!="("){
                        $fraini = substr($tit,0,3);
                        if($fraini!= "NP "){
                            $tact = "DENOMINACION";
                            if($tant=="DENOMINACION"){
                                $mmarcas[$consecm][0]=$mmarcas[$consecm][0]." ".$tit;
                            }else{
                                $consecm++;
                                $mmarcas[$consecm][0]=$tit; 
//                                echo "<HR>";                                
                            }                            
                        }else{
                            $mmarcas[$consecm][3]=trim(str_replace("NP ", "", $titx))*1;
                            $tact = "NUMPUB";
                        }                        
                    }else{
                        $codcampo = substr($tit,1);
                        $codcampo = strtok($codcampo,")");
                        if($codcampo*1 == 22){
                            if($tant!="DENOMINACION"){
                                $consecm++;
//                                echo "<HR>";
                            }
                            $titx = str_replace("(22)", "", $tit);
                            $msol = explode("Exp:", $titx);
                            $fechasol=trim($msol[0]);
                            $anoexp=trim($msol[1]);
                            $mmarcas[$consecm][1]=$fechasol;
                            $mmarcas[$consecm][2]=$anoexp;
                            $tact = "FECHSOLEXP";
                        }elseif($codcampo*1 == 51){
                            $titx = str_replace("(51)", "", $tit);
                            $titx = str_replace("NIZA.Ver.", "", $titx);
                            $titx = str_replace("PRODUCTOS/SERVICIOS", "", $titx);
                            $titx = str_replace("(", "", $titx);
                            $titx = trim(str_replace(")", "", $titx))*1;
                            $mmarcas[$consecm][4]=$titx;
                            $tact = "VERNIZA";
                        }
                    } 
//                    echo $tant.">".$tact.": ".$tit."<BR>";
                }else{
                    $tact = $tant;
                }                
            }
            if(trim($texto)!=""){
                $letini = substr(trim($texto),0,1);
                if($letini=="("){
                    if($tant=="VERNIZA" || $tant=="PRODSERV"){
                        $tact = "PRODSERV";
                        $clasex = substr(trim($texto),1,2)*1;
                        if($clasex>0){
                            $textox=trim(str_replace("(".$clasex.")", "", $texto));
                            $prodservx = $textox;
                            if($tant=="PRODSERV"){
                                $consecm++;
                                $mmarcas[$consecm]=$mmarcas[$consecm-1];
                            }
                            $mmarcas[$consecm][9]=$clasex;
                            $mmarcas[$consecm][10]=$prodservx;                            
                        }else{
                            $mmarcas[$consecm][10]=$mmarcas[$consecm][10]." ".$texto;
                        }
                        
                    }else{
                        $fraini = substr(trim($texto),0,4);
                        if($fraini=="(73)"){
                            $tact = "TITULAR";
                            $textox=trim(str_replace("(73)", "", $texto));
                            $mmarcas[$consecm][6]=$textox;
                        }elseif($fraini=="(74)"){
                            $tact = "APODERADO";
                            $textox=trim(str_replace("(74)", "", $texto));
                            $mmarcas[$consecm][7]=$textox;
                        }elseif($fraini=="(75)"){
                            $tact = "DOMICILIO";
                            $textox=trim(str_replace("(75)", "", $texto));
                            $mmarcas[$consecm][8]=$textox;
                        }else{
                            //echo $texto."<BR>";                            
                        }                        
                    }
                }else{
                    $fraini = substr($texto,0,5);
                    if($fraini=="TIPO " and $tant!="PRODSERV"){
                        $textox = trim(str_replace("TIPO ", "", $texto));
                        $mmarcas[$consecm][5]=$textox;
                        $tact = "TIPOMARCA";
                    }else{
                        if($tant=="PRODSERV"){
                            $tact = "PRODSERV";
                            $mmarcas[$consecm][10]=$mmarcas[$consecm][10]." ".$texto;
                        }else{
//echo $tant . ">" . $tact . ": " . $texto . "<BR>";
                            switch ($tant) {
                                case "TITULAR":
                                    $mmarcas[$consecm][6]=$mmarcas[$consecm][6]." ".$texto;
                                    break;
                                case "APODERADO":
                                    $mmarcas[$consecm][7]=$mmarcas[$consecm][7]." ".$texto;
                                    break;
                                case "DOMICILIO":
                                    $mmarcas[$consecm][8]=$mmarcas[$consecm][8]." ".$texto;
                                    break;                                
                            }
                        }
                    }                       
                } 
//                echo $tant.">".$tact.": ".$texto."<BR>";                
            }                
        }
    }
//    echo "<BR>";
//    print_r($mmarcas);
    return $mmarcas;    
}

function ret_mmarcas_solweb2013($anorad, $numrad, $carpeta, $control = "") {
    $datos["ano"] = $anorad;   //Año
    $datos["exp"] = $numrad;   //Expediente 
    $datos["ctl"] = $control;  //Control de Expediente
    
    $dirsolweb = ret_linkencripconsulta($datos);
    echo $dirsolweb."<br>";
    
    if ($dirsolweb != "") {
        //Contenido de la página        
        $cadsw = ret_txtfile2($dirsolweb);       
        $cadsw = reemplazaracentoshtml($cadsw);
        $cadsw = corregir_otrosacentos($cadsw);
        $cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); //Elimina Contenido del HEAD
        $cadsw = str_replace("<?", "", $cadsw);
        $cadsw = str_replace("?>", "", $cadsw);
        //Recoge el Signo    
        preg_match("/&codi_sign\=[^\"]*\"/si", $cadsw, $coincsigno, PREG_OFFSET_CAPTURE);
        //Recoge enlace imagen
        preg_match("/SignosDistintivos\/Etiquetas[^\"]*jpg/si", $cadsw, $coinchref, PREG_OFFSET_CAPTURE);
        //Recoge enlace mp3
        preg_match("/Etiquetas[^\"]*mp3/si", $cadsw, $coinchrefmp3, PREG_OFFSET_CAPTURE);

        //Retornar Archivo
        $txtfile = $anorad . "_" . $numrad . "_" . $control . ".txt";
        $txtfile2 = $anorad . "_" . $numrad . "_" . $control . ".xml";
        save_txtinfile($carpeta . "/" . $txtfile, utf8_encode($cadsw));
        //Limpia
        $gestor = @fopen($carpeta . "/" . $txtfile, "r");
        if ($gestor) {
            while (!feof($gestor)) {
                $bufer.= fgetss($gestor, 4096, "<td>");
            }
            fclose($gestor);
        }
        $bufer = preg_replace('/[\s\t\n\r\f\0]/', ' ', $bufer);
        $bufer = trim($bufer);
        $bufer = str_replace("  ", " ", $bufer);        
        $arrayareemplazar = array("&nbsp;", "&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&bull;");
        $arrayareemplazos = array("", "á", "e", "í", "ó", "ú", "");
        $bufer = str_replace($arrayareemplazar, $arrayareemplazos, $bufer);        
        $bufer = preg_replace('/\s+/', ' ', $bufer);
        $bufer = preg_replace("/<a[[:space:]]*[^>]*>/", "<a>", $bufer);
        $bufer = preg_replace("/<a>[[:space:]]*([^>])*<\/a>/", "", $bufer);
        $bufer = preg_replace("/<td[[:space:]]*([^>]*>)/", "<td>", $bufer);        
        $bufer = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $bufer);
        $bufer = preg_replace("/^[[:space:]]*([^<]*<)/", "<", $bufer);
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);        
        $bufer = preg_replace("/<td>[[:space:]]*([^<td>]*<td>)/", "<nivel2><td>", $bufer);
        $bufer = preg_replace("/<\/td>[[:space:]]*([^<\/td>]*<\/td>)/", "</td></nivel2>", $bufer);
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);
        $bufer = str_replace("PROPIEDAD INDUSTRIAL", "", $bufer);
        $bufer = corregir_otrosacentos($bufer);
        $bufer = reemplazaracentoshtml($bufer);
        $bufer = str_replace("INFORMACIÓN ACTUAL DEL REGISTRO","", $bufer);
        $bufer = str_replace("[ Ver certificación ]","", $bufer);
        $bufer = str_replace("Datos de la Marca","", $bufer);
        $bufer = str_replace("Ver Gaceta","", $bufer);
        $bufer = str_replace("<td>Tipo de expediente</td>","</td><td>Tipo de expediente</td>", $bufer);
        $bufer = str_replace("Ver Actuaciones", "</nivel2><nivel2>", $bufer);
        $bufer = str_replace("Personas Jurídicas / Naturales", "</nivel2><nivel2>", $bufer);
        $bufer = str_replace("Reproducir Sonido", "", $bufer); 
        $bufer = str_replace("<td> ", "<td>", $bufer);  
        $bufer = str_replace(" </td>", "</td>", $bufer);
        $bufer = str_replace("</td> ", "</td>", $bufer);
        $bufer = str_replace("<td></td>", "", $bufer);
        $bufer = str_replace("<td> </td>", "", $bufer);
        $bufer = str_replace("</td> <td>", "</td><td>", $bufer);
        
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);        
        $bufer = str_replace("[ Ver Título ]", "", $bufer);
        $bufer = str_replace("[Ver Título]", "", $bufer);        
       
        $bufer = corregir_otrosacentos($bufer);
        $bufer = reemplazaracentoshtml($bufer);
        $bufer = str_replace("&", "[yamp]", $bufer);        
        $bufer = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><pag>" . $bufer . "</nivel2></pag>"; 

        save_txtinfile($carpeta . "/" . $txtfile2, $bufer);
        unlink($carpeta . "/" . $txtfile);  
        
        //Contenido del archivo
        $xmlstr = ret_txtfile1($carpeta . "/" . $txtfile2);
        //Lectura de XML
        $ant = $act = "";
        $xml = new SimpleXMLElement($xmlstr);
        $minfomarca = array();
        $datosbasicos=array();
        foreach ($xml->td as $td) {
            $ant=$act;            
            $act="";
            switch (trim($td)) {
                case "Tipo de expediente":
                    $act="tipoexp";
                    break;
                case "Fecha de radicación":
                    $act="fecharad";
                    break;
                case "Hora":
                    $act="horarad";
                    break;
                case "Estado del trámite":
                    $act="esttra";
                    break;
                case "Certificado":
                    $act="certif";
                    break;
                case "Vigencia Hasta:":
                    $act="vighasta";
                    break;  
                case "Tipo de solicitud":
                    $act="tiposol";
                    break; 
                case "Fecha de radicación":
                    $act="fechrad";
                    break; 
                case "Fecha de presentación":
                    $act="fechpres";
                    break; 
                case "Denominación":
                    $act="denomi";
                    break;     
                case "Tipo":
                    $act="tipomarca";
                    break;                
                default:
                    if($act=="" & $ant!=""){
                        $datosbasicos[$ant]=trim($td);
                    }
                    break;
            }
        }                    

        $in2=0;
        foreach ($xml->nivel2 as $nivel2) {
            switch ($in2) {
                case 0://Publicación                    
                    $datosbasicos["gaceta"] = trim($nivel2->td[3]);
                    $datosbasicos["numpub"] = trim($nivel2->td[4]);
                    $datosbasicos["fecgac"] = trim($nivel2->td[5]);
                    break;
                case 1://Clasificación y Prioridad
                    $act="";
                    $ncl=0;
                    $mclasif=array(); 
                    $mprioridad=array();
                    $antpri="";
                    foreach ($nivel2->td as $td) {
                        $ant = $act;
                        $act = "";
                        if(substr(trim($td), 0,1)=="(" & substr(trim($td), -1,1)==")"){                            
                            $cl=  str_replace("(", "", trim($td));
                            $cl=  str_replace(")", "", trim($cl));
                            $cl=  $cl*1;                            
                            if($cl>=1 & $cl<=45){                                
                                $act=$cl;
                            }
                        }else{                            
                            if($act=="" & $ant!=""){
                                $mclasif[$ncl][0]=$ant;
                                $mclasif[$ncl][1]=trim($td);
                                $ncl++;
                            }else{
                                if(trim($td)=="Prioridad"){
                                    $antpri="1";
                                }
                                if($antpri=="1"){
                                    foreach($nivel2->nivel2 as $obpri){
                                        $mprioridad["pais"]   = trim($obpri->td[3]);
                                        $mprioridad["docu"]   = trim($obpri->td[4]);
                                        $mprioridad["fecpri"] = trim($obpri->td[5]);
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 2://Titular y Apoderado
                    $mpersonas=array(); 
                    $act="";  
                    $filaper=0;                    
                    foreach ($nivel2->td as $td) {
                        $ant=$act;
                        $act="";
                        switch (trim($td)) {
                            case "Titular":
                                $act="titular";
                                $iper=1;                                                            
                                break;
                            case "Apoderado":
                                $act="apoderado";
                                $iper=1;
                                $filaper++;
                                break;
                            case "Domicilio":
                            case "Dirección":
                                $act=$ant;
                                break;                             
                            default:
                                $mpersonas[$filaper][0]=$ant;
                                if($act=="" & $ant!=""){
                                    if($iper>=4){
                                        $iper=1;
                                        $filaper++;
                                    }                                    
                                    $mpersonas[$filaper][$iper]=trim($td);
                                    $iper++;
                                    $act = $ant;
//                                    print_r($mpersonas);
//                                    echo "<BR>";
                                }
                                break;                            
                        }
                    }
                    break; 
                case 3://Actuaciones
                    break;                 
            }
            $in2++;            
        }
        
        if(trim($coincsigno[0]!="")){
            $signo = str_replace('"', '', trim($coincsigno[0][0]));
            $signo = str_replace('&codi_sign=', '', $signo);
        }
        if(trim($coinchref[0][0])!=""){
            $nomimg = descargaimagen2(trim($coinchref[0][0]));
        }
        if(trim($coinchrefmp3[0][0])!=""){
            $nommp3 = descargamp3(trim($coinchrefmp3[0][0]));
        } 
        
        $datosbasicos["signo"]=$signo;
        $datosbasicos["imagen"]=$nomimg;
        $datosbasicos["mp3"]=$nommp3;
        if($datosbasicos["signo"]*1==0){
            $mdsig=  explode(".", $nomimg);
            $datosbasicos["signo"]=$mdsig[0];
        }
      
        //Corrige Fechas
        //--Fecha de Presentación
        $datosbasicos["fecharad"];
        //--Fecha de Vigencia
        $datosbasicos["vighasta"]=corregirfecha($datosbasicos["vighasta"]);
        //--Fecha de Gaceta
//        $mfecha = explode('//',$datosbasicos["fecgac"]); 
//        $datosbasicos["fecgac"]=$mfecha[2]."-".$mfecha[1]."-".$mfecha[0];
        $datosbasicos["fecgac"]=  corregirfecha($datosbasicos["fecgac"]);
        //Tipo de Denominación
        $tipodenomi = "0";
        switch (strtoupper($datosbasicos["tiposol"])) {
            case "NOMBRES COMERCIALES"://Nombre Comercial
                $tipodenomi = "1";
                break;
            case "MARCAS"://Marca
                $tipodenomi = "2";
                break;
            case "ENSENAS COMERCIALES"://Enseña Comercial
                $tipodenomi = "3";
                break;
            case "LEMAS COMERCIALES"://Lema Comercial
                $tipodenomi = "4";
                break;
            case "DENOMINACION DE ORIGEN"://Denominación de Origen
                $tipodenomi = "5";
                break;
            case "MARCAS COLECTIVAS"://Marcas Colectivas
                $tipodenomi = "6";
                break;
            case "MARCAS DE CERTIFICACION"://Marcas de Certificación
                $tipodenomi = "7";
                break;
            case "MARCA EXTENSION TERRITORIAL"://Marca Extensión Territorial
                $tipodenomi = "8";
                break;
        }  
        
        //Tipo Marca
        switch (strtoupper($datosbasicos["tipomarca"])) {
            case "NOMINATIVA":
                $tipomarcax = "1";
                break;
            case "FIGURATIVA":
                $tipomarcax = "2";
                $deno = "";
                break;
            case "MIXTA":
                $tipomarcax = "3";
                break;
            case "OLFATIVA":
                $tipomarcax = "4";
                $deno = "";
                break;
            case "SONORA":
                $tipomarcax = "5";
                $deno = "";
                break;
            case "TRIDIMENSIONAL":
                $tipomarcax = "6";
                $deno = "";
                break;
        }
        
        //Ingresa en Tabla de Precarga
        global $ipserver, $userdb, $pwduserdb, $db1;
        $conn = conectar($ipserver, $userdb, $pwduserdb);
        selectdb($db1, $conn);        
        if (sizeof($mclasif) == 1){
            $sql = "INSERT INTO `sim_precargasolweb` 
                    (`ano`, `expediente`, `control`, `signo`, `fecha_solicitud`, 
                    `tipo_denominacion`, `denominacion`, `tipomarca`, `certificado`, 
                    `vigencia`, `clase`, `version`, `cobertura`, `productos_servicios`, 
                    `titular`, `domicilio`, `direccion`, `apoderado`, `gaceta`, 
                    `publicacion`, `fecha_pubicacion`, `prioridad`, `denomi2`) 
                VALUES ('$anorad', '$numrad', '$control','".$datosbasicos["signo"]."', 
                    '".$datosbasicos["fecharad"]."', '$tipodenomi', '".$datosbasicos["denomi"]."',
                    '".$tipomarcax."', '".$datosbasicos["certif"]."', '".$datosbasicos["vighasta"]."', 
                    '".$mclasif[0][0]."', '$versioncl', '$cobertura', '".addslashes($mclasif[0][1])."', 
                    '".addslashes($mpersonas[0][1])."', '".addslashes($mpersonas[0][2])."', 
                    '".addslashes($mpersonas[0][3])."', '".addslashes($mpersonas[1][1])."',
                    '".$datosbasicos["gaceta"]."', '".$datosbasicos["numpub"]."', 
                    '".$datosbasicos["fecgac"]."', 
                    '".trim(addslashes($mprioridad["pais"]." ".$mprioridad["docu"]." ".$mprioridad["fecpri"]))."',
                    '".$datosbasicos["denomi"]."');";
            echo $sql."<BR><BR>";
            $sql = str_replace("[yamp]", "&", $sql);
            mysql_query(utf8_decode($sql));
        } else {
            for ($i = 0; $i < sizeof($mclasif); $i++) {
                $sql = "INSERT INTO `sim_precargasolweb` 
                        (`ano`, `expediente`, `control`, `signo`, `fecha_solicitud`, 
                        `tipo_denominacion`, `denominacion`, `tipomarca`, `certificado`, 
                        `vigencia`, `clase`, `version`, `cobertura`, `productos_servicios`, 
                        `titular`, `domicilio`, `direccion`, `apoderado`, `gaceta`, 
                        `publicacion`, `fecha_pubicacion`, `prioridad`, `denomi2`) 
                    VALUES ('$anorad', '$numrad', '".$mclasif[$i][0]."','".$datosbasicos["signo"]."', 
                        '".$datosbasicos["fecharad"]."', '$tipodenomi', '".$datosbasicos["denomi"]."',
                        '".$tipomarcax."', '".$datosbasicos["certif"]."', '".$datosbasicos["vighasta"]."', 
                        '".$mclasif[$i][0]."', '$versioncl', '$cobertura', '".addslashes($mclasif[$i][1])."', 
                        '".addslashes($mpersonas[0][1])."', '".addslashes($mpersonas[0][2])."', 
                        '".addslashes($mpersonas[0][3])."', '".addslashes($mpersonas[1][1])."',
                        '".$datosbasicos["gaceta"]."', '".$datosbasicos["numpub"]."', 
                        '".$datosbasicos["fecgac"]."', 
                        '".trim(addslashes($mprioridad["pais"]." ".$mprioridad["docu"]." ".$mprioridad["fecpri"]))."',
                        '".$datosbasicos["denomi"]."');";
                echo $sql."<BR><BR>";
                $sql = str_replace("[yamp]", "&", $sql);
                mysql_query(utf8_decode($sql));
            }
        }
        unlink($carpeta."/".$txtfile2);
    }
}

function ret_mmarcas_solweb($anorad, $numrad, $carpeta, $control = "") {
//    $dirsolweb="http://serviciospub.sic.gov.co/Sic/ConsultaEnLinea/RegistroSignos.php?ano_radi=".$anorad."&nume_radi=".$numrad;
//    if(trim($control)!=""){
//        $dirsolweb.="&cont_radi=".$control;
//    }

    $datos["ano"] = $anorad;   //Año
    $datos["exp"] = $numrad;   //Expediente 
    $datos["ctl"] = $control;  //Control de Expediente

    $dirsolweb = ret_linkencripconsulta($datos);

    if ($dirsolweb != "") {
        echo $dirsolweb."<br>";
        //Contenido de la página
        $cadsw = ret_txtfile2($dirsolweb);
        $cadsw = reemplazaracentoshtml($cadsw);
        $cadsw = corregir_otrosacentos($cadsw);
        $cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); //Elimina Contenido del HEAD
        $cadsw = str_replace("<?", "", $cadsw);
        $cadsw = str_replace("?>", "", $cadsw);

        //Recoge el Signo    
        preg_match("/codi_sign\=[^\']*\'/si", $cadsw, $coincsigno, PREG_OFFSET_CAPTURE);
//    print_r($coincsigno);  
//    echo "<BR>";
        //Recoge enlace imagen
        preg_match("/SignosDistintivos\/Etiquetas[^\"]*jpg/si", $cadsw, $coinchref, PREG_OFFSET_CAPTURE);
        //print_r($coinchref);
        //echo "<BR>";
        //Recoge enlace mp3
        preg_match("/Etiquetas[^\"]*mp3/si", $cadsw, $coinchrefmp3, PREG_OFFSET_CAPTURE);
//    print_r($coinchrefmp3);
//    echo "<BR>";    
        //Retorn Archivo
        $txtfile = $anorad . "_" . $numrad . "_" . $control . ".txt";
        $txtfile2 = $anorad . "_" . $numrad . "_" . $control . ".xml";
        save_txtinfile($carpeta . "/" . $txtfile, utf8_encode($cadsw));
        //Limpia
        $gestor = @fopen($carpeta . "/" . $txtfile, "r");
        if ($gestor) {
            while (!feof($gestor)) {
                $bufer.= fgetss($gestor, 4096, "<td>");
            }
            fclose($gestor);
        }
        $bufer = preg_replace('/[\s\t\n\r\f\0]/', ' ', $bufer);
        $bufer = trim($bufer);
        $bufer = str_replace("  ", " ", $bufer);
        $arrayareemplazar = array("&nbsp;", "&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&bull;");
        $arrayareemplazos = array("", "á", "e", "í", "ó", "ú", "");
        $bufer = str_replace($arrayareemplazar, $arrayareemplazos, $bufer);
        $bufer = preg_replace('/\s+/', ' ', $bufer);
        $bufer = preg_replace("/<a[[:space:]]*[^>]*>/", "<a>", $bufer);
        $bufer = preg_replace("/<a>[[:space:]]*([^>])*<\/a>/", "", $bufer);
        $bufer = preg_replace("/<td[[:space:]]*([^>]*>)/", "<td>", $bufer);
        $bufer = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $bufer);
        $bufer = preg_replace("/^[[:space:]]*([^<]*<)/", "<", $bufer);
        $bufer = preg_replace("/<td>[[:space:]]*([^<td>]*<td>)/", "<nivel2><td>", $bufer);
        $bufer = preg_replace("/<\/td>[[:space:]]*([^<\/td>]*<\/td>)/", "</td></nivel2>", $bufer);
        $bufer = str_replace("[ Ver Título ]", "", $bufer);
        $bufer = str_replace("[Ver Título]", "", $bufer);
        $posinifin = strpos($bufer, "INICIAL DEL REGISTRO</td>");
        if ($posinifin > 0) {
            $bufer = substr($bufer, 0, $posinifin) . "INICIAL DEL REGISTRO</td>";
        } else {
            $bufer = substr($bufer, 0, $posinifin);
        }
        $posinifin2 = strpos($bufer, "<td>Expediente");
        $bufer = substr($bufer, $posinifin2);
        $bufer = corregir_otrosacentos($bufer);
        $bufer = reemplazaracentoshtml($bufer);
        $bufer = str_replace("&", "[yamp]", $bufer);
        $bufer = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><pag>" . $bufer . "</pag>";
        save_txtinfile($carpeta . "/" . $txtfile2, $bufer);
        unlink($carpeta . "/" . $txtfile);

        //Contenido del archivo
        $xmlstr = ret_txtfile1($carpeta . "/" . $txtfile2);
        //Lectura de XML
        $ant = $act = "";
        $xml = new SimpleXMLElement($xmlstr);
        $id9 = 0;
        $id14 = 0;
        $minfomarca = array();
        foreach ($xml->td as $td) {
            $ant = $act;
            $act = "-1";
            $valx = trim($td);
            $valx = trim(str_replace(":", "", $valx));
            switch ($valx) {
                case "Expediente Nro":
                case "Expediente Número":
                    $act = 0;
                    break;
                case "Fecha Radicación":
                    $act = 1;
                    break;
                case "Fecha Presentación":
                    $act = 2;
                    break;
                case "Marca":
                case "Lema Comercial":
                case "Nombre Comercial":
                case "Denominación de Origen":
                case "Marcas Colectivas":
                case "Enseña Comercial":
                case "Marcas de Certificación":
                case "Marca Extensión Territorial":
                    $act = 3;
                    $minfomarca[0][9] = $valx;
                    break;
                case "Certificado":
                    $act = 4;
                    break;
                case "Descripción":
                    $act = 13;
                    break;
                case "Vigencia Hasta";
                    $act = 5;
                    break;
                case "Cobertura";
                    $act = 6;
                    break;
                case "Versión":
                    $act = 7;
                    break;
                case "Apoderado":
                    $act = 8;
                    break;
                case "Publicación":
                case "Nro. Gaceta";
                case "Número Gaceta";
                case "Nro. Publicación":
                case "Número Publicación";
                    $act = 9;
                    break;
                case "Prioridad":
                case "País";
                case "Documento";
                    $act = 14;
                    break;
                case "Fecha";
                    if ($ant == 9) {
                        $act = 9;
                    } elseif ($ant == 14) {
                        $act = 14;
                    }
                    break;
                case "PROPIEDAD INDUSTRIAL":
                case "INFORMACIÓN ACTUAL DEL REGISTRO":
                case "Caracteres utilizados":
                case "Revindicación de color":
                case "Traducción":
                case "Transliteración":
                case "Clasificación":
                case "Productos/Servicios":
                case "Titulares Actuales":
                case "Estado Trámite":
                case "PUBLICACION":
                case "Actos Administrativos":
                case "Estado Trámite";
                    $act = "-2";
                    break;
            }
            if ($ant == 9) {
                if (trim($td) != "Nro. Gaceta" and trim($td) != "Publicación" and trim($td) != "Nro. Publicación" and trim($td) != "Fecha" and trim($td) != "Estado Trámite:") {
                    $id9 = $id9 + 1;
                }
            }
            if ($ant == 14) {
                if (trim($td) != "Prioridad" and trim($td) != "País" and trim($td) != "Documento" and trim($td) != "Fecha" and trim($td) != "Estado Trámite:") {
                    $id14 = $id14 + 1;
                }
            }
            if ($act == "-1" and $ant != "-1" and trim($td) != "") {
                if ($ant == 9) {
                    $act = 9;
                    $npos = $ant + $id9;
                    if (trim($td) != "Nro. Gaceta" and trim($td) != "Publicación" and trim($td) != "Nro. Publicación" and trim($td) != "Fecha" and trim($td) != "Estado Trámite:") {
//echo $npos.">".$td."<BR>";
                        $minfomarca[0][$npos] = trim($td);
                    }
                } elseif ($ant == 14) {
                    $act = 14;
                    $npos = $ant + $id14;
                    if (trim($td) != "Estado Trámite" and trim($td) != "PUBLICACION" and trim($td) != "Documento" and trim($td) != "Publicación" and trim($td) != "Nro. Publicación") {
                        //echo $npos.">".$td."<BR>";
                        $minfomarca[0][$npos] = trim($td);
                    }
                } elseif ($ant == 33) {
                    //echo "33>".trim($td)."<BR>";
                    $act = 34;
                    $minfomarca[0][$act] = trim($td);
                } elseif ($ant == 34) {
                    //echo "34>".trim($td)."<BR>";
                    $act = 35;
//                    $npos=$ant+$id14;
//                    if(trim($td)!="Estado Trámite" and trim($td)!="PUBLICACION" and trim($td)!="Documento" and trim($td)!="Publicación" and trim($td)!="Nro. Publicación"){
//                        //echo $npos.">".$td."<BR>";
//                        $minfomarca[0][$npos]=trim($td);
//                    }                       
                } else {
                    if ($ant >= 0) {
                        //echo $ant.">".$td."<BR>";
                        $minfomarca[0][$ant] = trim($td);
                        if ($ant == "3") {
                            $act = 33;
                        }
                    }
                }
            } elseif ($act == "-1" and $ant == "9") {
                $act = 9;
                $npos = $ant + $id9;
                //echo $npos.">".$td."<BR>";
                $minfomarca[0][$npos] = trim($td);
            } elseif ($act == "-1" and $ant == "14") {
                $act = 14;
                $npos = $ant + $id14;
                //echo $npos.">".$td."<BR>";
                $minfomarca[0][$npos] = trim($td);
            } elseif ($act == "-1" and $ant == "3") {
                $act = 33;
                $npos = $act + $id14;
                $minfomarca[0][$npos] = trim($td);
            } elseif ($ant == "33") {
//echo "td->".$td."<BR>";
                $act = 34;
                $npos = $act;
                $minfomarca[0][$npos] = trim($td);
            } else {
                //echo "[".$td."]<BR>";
            }
        }
//print_r($minfomarca);
//echo "<BR>";
        $itersn = 1;
        $cel2 = "0";
        foreach ($xml->nivel2 as $nivel2) {
            if ($itersn >= 1) {
                $itertdn2 = 0;
                foreach ($nivel2->td as $td) {
                    //echo $td."<BR>";
                    if ($itersn == 1) {
                        $clasif[$itertdn2][$cel2] = trim($td);
                        $cel2++;
                        if ($cel2 > 1) {
                            $cel2 = 0;
                            $itertdn2++;
                        }
                    } elseif ($itersn == 2) {
                        $valy = trim($td);
                        $ant = $act;
                        $act = -1;
                        $valy1 = trim(str_replace(":", "", $valy));
                        switch ($valy1) {
                            case "Nombre o Razón Social":
                                $act = 0;
                                break;
                            case "Domicilio":
                                $act = 1;
                                break;
                            case "Dirección":
                                $act = 2;
                                break;
                        }
                        if ($act == -1 and $ant >= 0) {
                            $mtitulares[$itertdn2][$ant] = $valy;
                        }
                        if ($ant == 2) {
                            $itertdn2++;
                        }
                    }
                }
            }
            /* elseif($itersn==1){//Ya No funciona
              $itertdn2=0;
              foreach ($nivel2->td as $td){
              $itertdn2++;
              if($itertdn2==5){
              $versioncl = $td*1;
              }
              }
              } */
            $itersn++;
        }
        //Generar SQL
        $codsign = str_replace("codi_sign=", "", $coincsigno[0][0]) * 1;
        $mcodsign=explode("/",$coinchref[0][0]);
        $codsign=$mcodsign[sizeof($mcodsign)-1];
        $codsign=str_replace(".jpg","",$codsign);
        echo "<BR>".$codsign."<BR>";        
        $anoexpctl = $minfomarca[0][0];
        $manoexpctl = explode(" ", $anoexpctl);
        $tipodenomi = "0";
        switch (str_replace(":", "", $minfomarca[0][9])) {
            case "Nombre Comercial"://Nombre Comercial
                $tipodenomi = "1";
                break;
            case "Marca"://Marca
                $tipodenomi = "2";
                break;
            case "Enseña"://Enseña Comercial
                $tipodenomi = "3";
                break;
            case "Lema"://Lema Comercial
            case "Lema Comercial";
                $tipodenomi = "4";
                break;
            case "Denominación de Origen"://Denominación de Origen
                $tipodenomi = "5";
                break;
            case "Marcas Colectivas"://Marcas Colectivas
                $tipodenomi = "6";
                break;
            case "Marcas de Certificación"://Marcas de Certificación
                $tipodenomi = "7";
                break;
            case "Marca Extensión Territorial"://Marca Extensión Territorial
                $tipodenomi = "8";
                break;
        }

        //$mdeno  = explode(" ", $minfomarca[0][3]);
        $mdeno[0] = $minfomarca[0][3];
        $deno = trim($mdeno[0]);
        //$tipomarca = trim(str_replace(" ", "", $mdeno[1]));
        $tipomarca = trim($minfomarca[0][34]);

        switch ($tipomarca) {
            case "NOMINATIVA":
                $tipomarcax = "1";
                break;
            case "FIGURATIVA":
                $tipomarcax = "2";
                $deno = "";
                break;
            case "MIXTA":
                $tipomarcax = "3";
                break;
            case "OLFATIVA":
                $tipomarcax = "4";
                $deno = "";
                break;
            case "SONORA":
                $tipomarcax = "5";
                $deno = "";
                break;
            case "TRIDIMENSIONAL":
                $tipomarcax = "6";
                $deno = "";
                break;
        }
        switch ($minfomarca[0][6]) {
            case "TOTAL":
                $cobertura = "1";
                break;
            case "RESTRINGIDA":
                $cobertura = "2";
                break;
        }

        $quitarcl = array("(", ")");
        $ponercl = array("", "");

        global $ipserver, $userdb, $pwduserdb, $db1;
        $conn = conectar($ipserver, $userdb, $pwduserdb);
        selectdb($db1, $conn);

        $fecha_solicitud = corregirfecha($minfomarca[0][1]);
        $fecha_vigencia = corregirfecha($minfomarca[0][5]);
        $fecha_pubicaci = corregirfecha($minfomarca[0][12]);

        $versioncl = $minfomarca[0][7];

        if (sizeof($clasif) == 1) {
            $clase = trim(str_replace($quitarcl, $ponercl, $clasif[0][0]));
            $sql = "INSERT INTO `sim_precargasolweb` 
                    (`ano`, `expediente`, `control`, `signo`, `fecha_solicitud`, 
                    `tipo_denominacion`, `denominacion`, `tipomarca`, `certificado`, 
                    `vigencia`, `clase`, `version`, `cobertura`, `productos_servicios`, 
                    `titular`, `domicilio`, `direccion`, `apoderado`, `gaceta`, 
                    `publicacion`, `fecha_pubicacion`, `prioridad`, `denomi2`) 
                VALUES ('" . $manoexpctl[0] . "', '" . $manoexpctl[1] . "', '" . $manoexpctl[2] . "', 
                    '" . $codsign . "', '" . $fecha_solicitud . "', '" . $tipodenomi . "', 
                    '" . $deno . "','" . $tipomarcax . "', '" . $minfomarca[0][4] . "', '" . $fecha_vigencia . "', 
                    '" . $clase . "', '" . $versioncl . "', '" . $cobertura . "', '" . addslashes($clasif[0][1]) . "', 
                    '" . addslashes($mtitulares[0][0]) . "', '" . addslashes($mtitulares[0][1]) . "', 
                    '" . addslashes($mtitulares[0][2]) . "', '" . addslashes($minfomarca[0][8]) . "', 
                    '" . $minfomarca[0][10] . "', '" . $minfomarca[0][11] . "', '" . $fecha_pubicaci . "', 
                    '" . trim(addslashes($minfomarca[0][20] . " " . $minfomarca[0][21] . " " . $minfomarca[0][22])) . "', '" . $deno . "');";
            echo $sql."<BR><BR>";
            $sql = str_replace("[yamp]", "&", $sql);
            mysql_query(utf8_decode($sql));
            //query($sql);
        } else {
            for ($i = 0; $i < sizeof($clasif); $i++) {
                $clase = trim(str_replace($quitarcl, $ponercl, $clasif[$i][0]));
                $sql = "INSERT INTO `sim_precargasolweb` 
                    (`ano`, `expediente`, `control`, `signo`, `fecha_solicitud`, 
                    `tipo_denominacion`, `denominacion`, `tipomarca`, `certificado`, 
                    `vigencia`, `clase`, `version`, `cobertura`, `productos_servicios`, 
                    `titular`, `domicilio`, `direccion`, `apoderado`, `gaceta`, 
                    `publicacion`, `fecha_pubicacion`, `prioridad`, `denomi2`) 
                VALUES ('" . $manoexpctl[0] . "', '" . $manoexpctl[1] . "', '" . $clase . "', 
                    '" . $codsign . "', '" . $fecha_solicitud . "', '" . $tipodenomi . "', 
                    '" . $deno . "','" . $tipomarcax . "', '" . $minfomarca[0][4] . "', '" . $fecha_vigencia . "', 
                    '" . $clase . "', '" . $versioncl . "', '" . $cobertura . "', '" . addslashes($clasif[$i][1]) . "', 
                    '" . addslashes($mtitulares[0][0]) . "', '" . addslashes($mtitulares[0][1]) . "', 
                    '" . addslashes($mtitulares[0][2]) . "', '" . addslashes($minfomarca[0][8]) . "', 
                    '" . trim(str_replace("Ver Gaceta", "", $minfomarca[0][12])) . "', '" . $minfomarca[0][13] . "', '" . $fecha_pubicaci . "', 
                    '" . trim(addslashes($minfomarca[0][20] . " " . $minfomarca[0][21] . " " . $minfomarca[0][22])) . "', '" . $deno . "');";
                echo $sql."<BR><BR>";
                $sql = str_replace("[yamp]", "&", $sql);
                mysql_query(utf8_decode($sql));
            }
        }
        //Descarga Imágen
        if (sizeof($coinchref) > 0) {
            descargaimagen2($coinchref[0][0]);
        }
        unlink($carpeta . "/" . $txtfile2);
    }
}

function reemplazaracentoshtml($cad){
    $mtexto   = array("Á","É","Í","Ó","Ú","á","é","í","ó","ú","Ñ","ñ");
    $mhtml=array("&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Ntilde;","&ntilde;");
    return str_replace($mhtml, $mtexto, $cad);
}

function corregir_otrosacentos($cad){
    $macentos = array("Ã","Ã³","Ãº");
    //$macentos = array("","","","Ã","","","","","Ã³","Ãº","","");
    $mtexto = array("Ó","ó","ú");
    //$mtexto = array("Á","É","Í","Ó","Ú","á","é","í","ó","ú","Ñ","ñ");
    return str_replace($macentos, $mtexto, $cad);    
}

function ret_mmarcas_xmlgac2($xmlfile) {
    //Contenido del archivo
    $xmlstr = ret_txtfile1($xmlfile);
    //Lectura de XML
    $xml = new SimpleXMLElement($xmlstr);
    $tant = "";
    $consecm = -1;
    $mmarcas = array();
    foreach ($xml->page as $pagina) {        
        foreach ($pagina->text as $texto) {
            $atribx = $texto->attributes();
            $tfuente = $atribx["font"];
            $tit = trim($texto);
            $titx = str_replace("SUPERINTENDENCIA DE INDUSTRIA Y COMERCIO", "", $tit);
            $titx = str_replace("GACETA No", "", $titx);
            $titx = str_replace("SOLICITUDES DE MARCA", "", $titx);
            $titx = str_replace("Página:", "", $titx);
            $titx = str_replace("SOLICITUDES DE LEMAS", "", $titx);
            if (trim($texto) != "" and trim($texto) == $titx) {
                $tant = $tact;
                $tact = "";                
                $texto = trim($texto);
                $fraini = substr(trim($texto), 0, 4);
                if ($fraini == "(73)") {//Titular
                    $tact = "TITULAR";
                    $textox = trim(str_replace("(73)", "", $texto));
                    $mmarcas[$consecm][6] = $textox;
                } elseif ($fraini == "(74)") {//Apoderado
                    $tact = "APODERADO";
                    $textox = trim(str_replace("(74)", "", $texto));
                    $mmarcas[$consecm][7] = $textox;
                } elseif ($fraini == "(75)") {//Domicilio
                    $tact = "DOMICILIO";
                    $textox = trim(str_replace("(75)", "", $texto));
                    $mmarcas[$consecm][8] = $textox;
                } elseif ($fraini == "(51)") {
                    $titx = str_replace("(51)", "", $tit);
                    $titx = str_replace("NIZA.Ver.", "", $titx);
                    $titx = str_replace("PRODUCTOS/SERVICIOS", "", $titx);
                    $titx = str_replace("(", "", $titx);
                    $titx = trim(str_replace(")", "", $titx)) * 1;
                    $mmarcas[$consecm][4] = $titx;
                    $tact = "VERNIZA";
                } elseif ($fraini == "(22)") {
                    if ($tfuente == 2) {//Nueva Fecha de Publicación
                        if ($tant != "DENOMINACION") {
                            $consecm++;
                        }
                        $titx = str_replace("(22)", "", $texto);
                        $msol = explode("Exp:", $titx);
                        $fechasol = trim($msol[0]);
                        $anoexp = trim($msol[1]);
                        $mmarcas[$consecm][1] = $fechasol;
                        $mmarcas[$consecm][2] = $anoexp;
                        $tact = "FECHSOLEXP";
                    } else {//Otro Producto o servicio (Clase 22)
                        $textox = trim(substr($texto, 4));
                        if ($tant == "PRODSERV") {
                            $consecm++;
                            $mmarcas[$consecm] = $mmarcas[$consecm - 1];
                        }
                        $tact = "PRODSERV";
                        $mmarcas[$consecm][9] = 22;
                        $mmarcas[$consecm][10] = $textox;
                    }
                } else {
                    $parini = substr(trim($texto), 0, 1);
                    $clasex = substr(trim($texto), 1, 2) * 1;
                    $parfin = substr(trim($texto), 3, 1);
                    if ($clasex > 0 and $parini == "(" and $parfin == ")") {//Nuevo Producto y/o Servicio
                        $textox = trim(substr($texto, 4));
                        if ($tant == "PRODSERV") {
                            $consecm++;
                            $mmarcas[$consecm] = $mmarcas[$consecm - 1];
                        }
                        $tact = "PRODSERV";
                        $mmarcas[$consecm][9] = $clasex;
                        $mmarcas[$consecm][10] = $textox;
                    } else {
                        $parini = substr(trim($texto), 0, 1);
                        $clasex = substr(trim($texto), 1, 1) * 1;
                        $parfin = substr(trim($texto), 2, 1);
                        if ($clasex > 0 and $parini == "(" and $parfin == ")") {//Nuevo Producto y/o Servicio
                            $textox = trim(substr($texto, 3));
                            if ($tant == "PRODSERV") {
                                $consecm++;
                                $mmarcas[$consecm] = $mmarcas[$consecm - 1];
                            }
                            $tact = "PRODSERV";
                            $mmarcas[$consecm][9] = $clasex;
                            $mmarcas[$consecm][10] = $textox;
                        }else{                        
                            $fraini = substr($texto, 0, 5);
                            if ($fraini == "TIPO " and $tant != "PRODSERV") {//Tipo Marca
                                $textox = trim(str_replace("TIPO ", "", $texto));
                                $mmarcas[$consecm][5] = $textox;
                                $tact = "TIPOMARCA";
                            } else {
                                $fraini = substr($tit, 0, 3);
                                if ($fraini == "NP ") {//número de Publicación
                                    $mmarcas[$consecm][3] = trim(str_replace("NP ", "", $titx)) * 1;
                                    $tact = "NUMPUB";
                                } else {
                                    $fraini = substr($tit, 0, 9);
                                    if($fraini=="PRIORIDAD" and $tant!="PRODSERV"){//Prioridad
                                        $tact="PRIORIDAD";
                                        $mmarcas[$consecm][11] = trim(substr($tit,9));
                                    }else{
                                        if ($tfuente == 2) {//Denominación
                                            if($tant=="TITULAR"){
                                                $tact = $tant;
                                                $mmarcas[$consecm][6] .= " " . $texto;
                                            }elseif($tant=="APODERADO"){
                                                $tact = $tant;
                                                $mmarcas[$consecm][7] .= " " . $texto;                                                
                                            }elseif($tant=="DOMICILIO"){
                                                $tact = $tant;
                                                $mmarcas[$consecm][8] .= " " . $texto;                                                
                                            }elseif($tant=="PRIORIDAD"){
                                                $tact = $tant;
                                                $mmarcas[$consecm][11] .= " " . $texto;
                                            }elseif($tant=="DENOMINACION"){
                                                $tact = $tant;
                                                $mmarcas[$consecm][0] .= " " . $texto;
                                            }else{
                                                $tact = "DENOMINACION";
                                                $consecm++;
                                                $mmarcas[$consecm][0]=$texto;
                                            }                                                                                 
                                        }else{//Productos y Servicios
                                            $tact = "PRODSERV";
                                            $mmarcas[$consecm][10] .= " " . $texto;
                                        }                                        
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $mmarcas;
}

function corregirfecha($fecha, $caso="1"){
    //$delim="-";
    $fecha = trim($fecha);
    for($i=0; $i<strlen($fecha); $i++){
        if(!is_numeric($fecha[$i])){
            $delim=$fecha[$i];
        }
    }
    $nummeses=array("01","02","03","04","05","06","07","08","09","10","11","12");
    $nommeses=array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
    
    if($delim!=""){
        $mfecha=explode($delim, $fecha);
        switch ($caso) {
            case 1://Entra en Formato dia, mes año
                    $dia=$mfecha[0];
                    $mes=$mfecha[1];
                    $ano=$mfecha[2];
                break;
        }
        $mes = strtolower($mes);
        $mes=str_replace($nommeses, $nummeses, $mes);
        $nuevafecha = $ano."-".$mes."-".$dia;
        if(trim($nuevafecha)=="--"){
            $nuevafecha="";
        }
        return $nuevafecha;
    }
}

function xmlgactit2txt($archivo, $destino){
    //Abre Archivo para Lectura
    $gestor = fopen($archivo, "rb");
    $contenido = stream_get_contents($gestor);
    fclose($gestor); 
    
    //Expresiones Regulares 1 (limpia Texto)
    $patrones[0] = "/<page(.*?)>/";
    $patrones[1] = "#</page>#";
    $patrones[2] = "/<fontspec(.*?)>/";
    $patrones[3] = "/<text (.*?)>/";
    $patrones[4] = "/<text>SUPERINTENDENCIA (.*?)>/";
    $patrones[5] = "/<text>GACETA No.(.*?)>/";
    $patrones[6] = "/<text>SOLICITUDES DE (.*?)>/";
    $patrones[7] = "/<text>P(.*?)gina:(.*?)>/";    
    $patrones[8] = "/\s+/";//Quita Doble espacio
    $patrones[9] = "#> <#";
    $patrones[10]= "/> \(/";
    $patrones[11]= "# </text>#";
    $patrones[12] = "/<text> /";
    $patrones[13] ="/<text>TITULOS DE MARCAS(.*?)>/";
    array_push($patrones, "/\s+/","#> <#");
    $reemplazos=array("","","","<text>","","","",""," ",">\r<",">(","</text>","<text>","");
    array_push($reemplazos, " ",">\r<");    
    $contenido = preg_replace($patrones, $reemplazos, $contenido);
    
    //Expresiones regulares 2 (Organiza Texto y termina Limpieza)
    $mcods=array("11","21","45","73","75","51","NP");
    //$contenido = preg_replace("/>\((.*?)\) /", " tipo=\"titulo\">$1</text><text>", $contenido);
    for($i=0; $i<sizeof($mcods)-2; $i++){
        $patronx="/>\((".$mcods[$i].")\) /";
        $contenido = preg_replace($patronx, " tipo=\"titulo\">$1</text><text>", $contenido);        
    }
    $contenido = preg_replace("/\(51\) /", "</text><text tipo='titulo'>51</text><text>", $contenido);    
    $contenido = preg_replace("/ NP /", "</text><text tipo='titulo'>NP</text><text>", $contenido);
    $contenido = preg_replace("/></", ">\r<", $contenido);
    
    //Crea Archivo temporal (Solo para desarrollo)
    /*
    $gestor = fopen("tmp.xml", "w");
    fwrite($gestor, $contenido);
    fclose($gestor);    
    */
    
    //Lee XML
    $xml = new SimpleXMLElement($contenido);    
    //Inicia Variables
    $id=$idant="";
    $txtant2=$txtant="";
    $i=-1;
    foreach ($xml->text as $texto){        
        $atribx = $texto->attributes();
        $tipo = $atribx["tipo"];       
        $texto=trim($texto); 
        if($tipo=="titulo" and in_array($texto, $mcods)){
            $id=trim($texto);
            if($id=="11"){
                $i++;
            }
        }else{
            if($id==""){//Para denominación Inicial
                $txtant=trim($texto);
            }
            if($id!="75"){
                //$mtitulos[$id].=trim($texto);
                if($id=="45"){
                    $mfsoltip1=  explode(" ", trim($texto));
                    $mtitulos[$i][$id]=trim($mfsoltip1[0]);
                    $mtitulos[$i]["tipo"]=trim($mfsoltip1[1]);
                    if($mtitulos[$i]["tipo"]!="NOMINATIVA" and $mtitulos[$i]["tipo"]!="MIXTA"){
                        if($i-1>=0){                            
                            $mtitulos[$i-1]["75"]=$txtant2.$txtant;
                        }
                        $mtitulos[$i]["deno"]="";
                    }else{                    
                        if($i-1>=0){
                            $mtitulos[$i-1]["75"]=$txtant2;
                        }
                        $mtitulos[$i]["deno"]="$txtant";
                    }
                    $txtant2=$txtant="";
                }else{
                    if(trim($id!="")and $i>=0){
                        $mtitulos[$i][$id].=trim($texto);
                    }
                }
            }else{
                $txtant2.=" ".trim($txtant);
                $txtant  = trim($texto);
            }
            $idant=$id;
        }
    }
    
    //Muestra algunos resultados en Pantalla
//    for($i=0; $i<sizeof($mtitulos); $i++){
//        if(trim($mtitulos[$i]["541"])!=""){
//            //echo $mmarcas[$i]["541"]."->".$mmarcas[$i][deno]."->".$mmarcas[$i]["732"]."<br>";
//        }else{
//            //echo $i;
//        }
//        print_r($mtitulos[$i]);
//        echo "<hr><br>";
//    }
    
    $cont="";
    $sl="\n";    
    for($i=0; $i<sizeof($mtitulos); $i++){
        if(is_array($mtitulos)){
            $clasesx = $mtitulos[$i]["51"];
            $mclases=  explode(",", $clasesx);            
            for($j=0; $j<sizeof($mclases); $j++){
                $sl2=$sl;
                if($i==0 and $j==0){
                    $sl2="";
                }
                $clasex=$mclases[$j];
                $cont.= $sl2."-----------------------------".$sl;
                //Denominación (Si Existe)
                if(trim($mtitulos[$i]["deno"])!=""){
                    $cont.= "-denomi: ".$mtitulos[$i]["deno"].$sl;
                }  
                //Certificado
                $cont.= "-certi: ".$mtitulos[$i]["11"].$sl;
                //Clase
                $cont.= "-clase: ".$clasex.$sl;
                //Año Expediente y control
                $ctl="";
                if(sizeof($mclases)>1){
                    $ctl=" ".$clasex;
                }
                $cont.= "-anoexp: ".$mtitulos[$i]["21"].$ctl.$sl;
                //Numero Publicación
                $cont.= "-numpub: ".$mtitulos[$i]["NP"].$sl;
                //Vigencia
                $cont.= "-vigen: ".$mtitulos[$i]["45"].$sl;
                //Tipo Denominación
                $cont.= "-tipo: ".$mtitulos[$i]["tipo"].$sl;
                //Solicitante
                $cont.= "-solic: ".$mtitulos[$i]["73"].$sl;
                //Domicilio
                $cont.= "-domic: ".$mtitulos[$i]["75"];
            }
        }
    }    
    
    //Crea Archivo Plano
    if (trim($destino)!="") {
        $g1 = fopen($destino, 'w');
        fwrite($g1, $cont);
        fclose($g1);

    }    
}

function xmlgacmadrid2txt($archivo, $destino){
    //Abre Archivo para Lectura
    $gestor = fopen($archivo, "rb");
    $contenido = stream_get_contents($gestor);
    fclose($gestor); 
    
    //Expresiones Regulares 1 (limpia Texto)
    $patrones[0] = "/<page(.*?)>/";
    $patrones[1] = "#</page>#";
    $patrones[2] = "/<fontspec(.*?)>/";
    $patrones[3] = "/<text (.*?)>/";
    $patrones[4] = "/<text>SUPERINTENDENCIA (.*?)>/";
    $patrones[5] = "/<text>GACETA No.(.*?)>/";
    $patrones[6] = "/<text>SOLICITUDES DE (.*?)>/";
    $patrones[7] = "/<text>Pagina:(.*?)>/";
    $patrones[8] = "/\s+/";//Quita Doble espacio
    $patrones[9] = "#> <#";
    $patrones[10]= "/> \(/";
    $patrones[11]= "# </text>#";
    $patrones[12] = "/<text> /";
    $reemplazos=array("","","","<text>","","","",""," ",">\r<",">(","</text>","<text>");
    $contenido = preg_replace($patrones, $reemplazos, $contenido);
    
    //Ecpresiones regulares 2 (Organiza Texto y termina Limpieza)
    $mcods=array("151","891","22","732","541","542","511","NP");
    //$contenido = preg_replace("/>\((.*?)\) /", " tipo=\"titulo\">$1</text><text>", $contenido);
    for($i=0; $i<sizeof($mcods)-1; $i++){
        $patronx="/>\((".$mcods[$i].")\) /";
        $contenido = preg_replace($patronx, " tipo=\"titulo\">$1</text><text>", $contenido);        
    }
    $contenido = preg_replace("/>NP /", " tipo='titulo'>NP</text><text>", $contenido);
    $contenido = preg_replace("/></", ">\r<", $contenido);
    $contenido = preg_replace("#<text>PRODUCTOS/SERVICIOS</text>#","",$contenido);
    
    //Crea Archivo temporal (Solo para desarrollo)
    /*$gestor = fopen("tmp.xml", "w");
    fwrite($gestor, $contenido);
    fclose($gestor);*/     
    
    //Lee XML
    $xml = new SimpleXMLElement($contenido);
    
    $i = -1;
    $mmarcas=array();
    $txtant1 = "";
    $id="";
    $claseant="";
   
    foreach ($xml->text as $texto){        
        $atribx = $texto->attributes();
        $tipo = $atribx["tipo"];       
        $texto=trim($texto); 
        if($tipo=="titulo" and in_array($texto, $mcods)){
            $idant = $id;
            if($texto!="151" and $idant=="511"){
                $claseant = $clase;
                $clase=$texto;
                
                if($claseant>0){
                    $con=" ";
                    $mprodserv[$claseant].=$con.$txtant1;
                }
            }else{
                $id=$texto;            
            }
            if($id=="151"){
                $claseant = $clase;
                $clase="";
                $dnotmp = $txtant1;
                $i++;
            }
            if($id=="541"){
                //echo $mmarcas[$i]["732"]."<br>";
                $mmarcas[$i]["732"] = str_replace($txtant1, " | ".$txtant1, $mmarcas[$i]["732"]);
            }            
            $txtant1="";
        }else{
            if(trim($id)!=""){
                if($id=="541" || $id=="542"){
                    $texto=str_replace("TIPO ","",$texto);
                    if($texto=="MIXTA" || $texto=="NOMINATIVA"){
                        $mmarcas[$i]["deno"]=$dnotmp;
                    }else{
                        $mprodserv[$claseant].=" ".$dnotmp;
                    }
                    $dnotmp="";
                    $mmarcas[$i-1]["511"]=$mprodserv;
                    $mprodserv=array();
                }elseif($id=="511"){//PyS x Clase
                    $txttmpps=  trim(substr($texto, 0, 4));
                    $initxttmpps=substr($txttmpps, 0, 1);
                    $fintxttmpps=substr($txttmpps, -1, 1);
                    if($initxttmpps=="(" and $fintxttmpps==")"){
                        if($clase*1>0){
                            $claseant=$clase;
                            $con=" ";
                            $mprodserv[$claseant].=$con.$txtant1;   
                            $txtant1="";
                        }
                        $clase = substr($txttmpps, "1",  strlen($txttmpps)-2);
                        $texto=trim(substr($texto, strlen($clase)+2));
                        //echo $clase."<br>".$texto."<br>";                        
                    }else{
                        if($clase*1>0){
                            $con=" ";
                            $mprodserv[$clase].=$con.$txtant1;
                        }                        
                    }

                }
                $con=" ";            
                $mmarcas[$i][$id].= $con.$texto;
                $mmarcas[$i][$id]=trim($mmarcas[$i][$id]);               
            }
            $txtant1=$texto;  
        }
    }
    //Muestra algunos resultados en Pantalla
    for($i=0; $i<sizeof($mmarcas); $i++){
        if(trim($mmarcas[$i]["541"])!=""){
            //echo $mmarcas[$i]["541"]."->".$mmarcas[$i][deno]."->".$mmarcas[$i]["732"]."<br>";
        }else{
            //echo $i;
        }
    }
    $cont="";
    $sl="\n";    
    for($i=0; $i<sizeof($mmarcas); $i++){
        $mps=$mmarcas[$i]["511"];
        if(is_array($mps)){
            $mclases=array_keys($mps);
            for($j=0; $j<sizeof($mclases); $j++){
                $sl2=$sl;
                if($i==0 and $j==0){
                    $sl2="";
                }
                $mclases=array_keys($mps);
                $clasex=$mclases[$j];
                $cont.= $sl2."----------------------->".$sl;
                //Denominación (Si Existe)
                if(trim($mmarcas[$i]["deno"])!=""){
                    $cont.= "-denomi: ".$mmarcas[$i]["deno"].$sl;
                }  
                //Clase
                $cont.="-clase: ".$clasex.$sl;
                //Fecha de Publicación (no esta pero deberia ser la de la gaceta)
                $cont.="-fechap: ".$sl;
                //Numero de Publicación
                $cont.="-numpub: ".$mmarcas[$i]["NP"].$sl;
                //Año - Expediente - Control
                $mexp=  explode("Exp:", $mmarcas[$i]["22"]);
                $ctl="";
                if(sizeof($mclases)>1){
                    $ctl=$clasex;
                }
                $cont.="-anoexp: ".trim(str_replace("-"," ",$mexp[1])." ".$ctl).$sl;
                //Solicitante y Domicilio
                $soldoc=  explode("|", $mmarcas[$i]["732"]);
                $cont.="-solic: ".trim($soldoc[0]).$sl;
                $cont.="-domic: ".trim($soldoc[1]).$sl;
                //Apoderado (No esta)
                $cont.="-apod: ".$sl;
                //Tipo Denomi
                $cont.="-tipo: ".$mmarcas[$i]["541"].$sl;
                //Prioridad
                $fechapubpri=trim($mmarcas[$i]["891"]);
                $mprio2 = explode(" ", $mmarcas[$i]["151"]);
                $cont.="-priorid: ".$fechapubpri." | ".trim($mprio2[0])." (".$mprio2[1].") | PROTOCOLO DE MADRID".$sl;
                //Productos y Servicios
                $cont.="-Prodserv: ".trim($mps[$clasex]);
            }
            print_r($mmarcas[$i]["151"]);
            echo "<br><br>";
        }
    }    
    
    //Crea Archivo Plano
    if (trim($destino)!="") {
        $g1 = fopen($destino, 'w');
        fwrite($g1, $cont);
        fclose($g1);

    }    
}

function ret_idxnumtitgac($numtitgac){
    switch ($numtitgac) {
        case 151://expediente fecha (Registro Internacional)
            $id = "exp";
            break;
        case 891://Fecha designacion Posterior
            $id = "fdp";
            break; 
        case 22://Fecha Radicación Nacional
            $id = "frn";
            break;  
        case 732://Solicitante
            $id = "soli";
            break;
        case 541://Tipo MArca
        case 542://Tipo MArca
            $id = "Tipomarca";
            break; 
        case 511://productos y servicios
            $id = "pys";
            break;          
    }
    return $id;
}

function ret_mmarcas_solwebxsigno($signo, $carpeta, $control = "") {
    $signoencript = encriptarsigno($signo);
    //echo $signo."<br>";
    //echo $signoencript."<br>";
    //$dirsolweb="http://serviciospub.sic.gov.co/Sic/ConsultaEnLinea/2013/RegistroSignos.php?zaqwscersderwerrteyr=pol%F1mkjuiutdrsesdfrcdfds&qwx=ltjS0sLc2L2gbWV".str_pad($signoencript, 10 , "=");
	
	echo $dirsolweb."<br>";
    if (trim($signoencript) != "") {
        //Contenido de la página        
        $cadsw = ret_txtfile2($dirsolweb);       
        $cadsw = reemplazaracentoshtml($cadsw);
        $cadsw = corregir_otrosacentos($cadsw);
        $cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); //Elimina Contenido del HEAD
        $cadsw = str_replace("<?", "", $cadsw);
        $cadsw = str_replace("?>", "", $cadsw);
        //Recoge el Signo    
        preg_match("/&codi_sign\=[^\"]*\"/si", $cadsw, $coincsigno, PREG_OFFSET_CAPTURE);
        //Recoge enlace imagen
        preg_match("/SignosDistintivos\/Etiquetas[^\"]*jpg/si", $cadsw, $coinchref, PREG_OFFSET_CAPTURE);
        //Recoge enlace mp3
        preg_match("/Etiquetas[^\"]*mp3/si", $cadsw, $coinchrefmp3, PREG_OFFSET_CAPTURE);

        //Retornar Archivo
        $txtfile  = $signo.".txt";
        $txtfile2 = $signo.".xml";
        save_txtinfile($carpeta . "/" . $txtfile, utf8_encode($cadsw));
        //Limpia
        $gestor = @fopen($carpeta . "/" . $txtfile, "r");
        if ($gestor) {
            while (!feof($gestor)) {
                $bufer.= fgetss($gestor, 4096, "<td>");
            }
            fclose($gestor);
        }
        $bufer = preg_replace('/[\s\t\n\r\f\0]/', ' ', $bufer);
        $bufer = trim($bufer);
        $bufer = str_replace("  ", " ", $bufer);        
        $arrayareemplazar = array("&nbsp;", "&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&bull;");
        $arrayareemplazos = array("", "á", "e", "í", "ó", "ú", "");
        $bufer = str_replace($arrayareemplazar, $arrayareemplazos, $bufer);        
        $bufer = preg_replace('/\s+/', ' ', $bufer);
        $bufer = preg_replace("/<a[[:space:]]*[^>]*>/", "<a>", $bufer);
        $bufer = preg_replace("/<a>[[:space:]]*([^>])*<\/a>/", "", $bufer);
        $bufer = preg_replace("/<td[[:space:]]*([^>]*>)/", "<td>", $bufer);        
        $bufer = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $bufer);
        $bufer = preg_replace("/^[[:space:]]*([^<]*<)/", "<", $bufer);
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);        
        $bufer = preg_replace("/<td>[[:space:]]*([^<td>]*<td>)/", "<nivel2><td>", $bufer);
        $bufer = preg_replace("/<\/td>[[:space:]]*([^<\/td>]*<\/td>)/", "</td></nivel2>", $bufer);
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);
        $bufer = str_replace("PROPIEDAD INDUSTRIAL", "", $bufer);
        $bufer = corregir_otrosacentos($bufer);
        $bufer = reemplazaracentoshtml($bufer);
        $bufer = str_replace("INFORMACIÓN ACTUAL DEL REGISTRO","", $bufer);
        $bufer = str_replace("[ Ver certificación ]","", $bufer);
        $bufer = str_replace("Datos de la Marca","", $bufer);
        $bufer = str_replace("Ver Gaceta","", $bufer);
        $bufer = str_replace("<td>Tipo de expediente</td>","</td><td>Tipo de expediente</td>", $bufer);
        $bufer = str_replace("Ver Actuaciones", "</nivel2><nivel2>", $bufer);
        $bufer = str_replace("Personas Jurídicas / Naturales", "</nivel2><nivel2>", $bufer);
        $bufer = str_replace("Reproducir Sonido", "", $bufer); 
        $bufer = str_replace("<td> ", "<td>", $bufer);  
        $bufer = str_replace(" </td>", "</td>", $bufer);
        $bufer = str_replace("</td> ", "</td>", $bufer);
        $bufer = str_replace("<td></td>", "", $bufer);
        $bufer = str_replace("<td> </td>", "", $bufer);
        $bufer = str_replace("</td> <td>", "</td><td>", $bufer);
        
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);        
        $bufer = str_replace("[ Ver Título ]", "", $bufer);
        $bufer = str_replace("[Ver Título]", "", $bufer);  
        $bufer = str_replace(" Nro : ", "</td><td>", $bufer);  
       
        $bufer = corregir_otrosacentos($bufer);
        $bufer = reemplazaracentoshtml($bufer);
        $bufer = str_replace("&", "[yamp]", $bufer);        
        $bufer = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><pag>" . $bufer . "</nivel2></pag>"; 

        save_txtinfile($carpeta . "/" . $txtfile2, $bufer);
        unlink($carpeta . "/" . $txtfile);  
        
        //Contenido del archivo
        $xmlstr = ret_txtfile1($carpeta . "/" . $txtfile2);
        //Lectura de XML
        $ant = $act = "";
        $xml = new SimpleXMLElement($xmlstr);
        $minfomarca = array();
        $datosbasicos=array();
        foreach ($xml->td as $td) {
            $ant=$act;            
            $act="";
            switch (trim($td)) {
                case "Expediente":
                    $act="numexp";
                    break;                
                case "Tipo de expediente":
                    $act="tipoexp";
                    break;
                case "Fecha de radicación":
                    $act="fecharad";
                    break;
                case "Hora":
                    $act="horarad";
                    break;
                case "Estado del trámite":
                    $act="esttra";
                    break;
                case "Certificado":
                    $act="certif";
                    break;
                case "Vigencia Hasta:":
                    $act="vighasta";
                    break;  
                case "Tipo de solicitud":
                    $act="tiposol";
                    break; 
                case "Fecha de radicación":
                    $act="fechrad";
                    break; 
                case "Fecha de presentación":
                    $act="fechpres";
                    break; 
                case "Denominación":
                    $act="denomi";
                    break;     
                case "Tipo":
                    $act="tipomarca";
                    break;                
                default:
                    if($act=="" & $ant!=""){
                        $datosbasicos[$ant]=trim($td);
                    }
                    break;
            }
        }                    
//print_r($datosbasicos);
        $in2=0;
        foreach ($xml->nivel2 as $nivel2) {
            switch ($in2) {
                case 0://Publicación                    
                    $datosbasicos["gaceta"] = trim($nivel2->td[3]);
                    $datosbasicos["numpub"] = trim($nivel2->td[4]);
                    $datosbasicos["fecgac"] = trim($nivel2->td[5]);
                    break;
                case 1://Clasificación y Prioridad
                    $act="";
                    $ncl=0;
                    $mclasif=array(); 
                    $mprioridad=array();
                    $antpri="";
                    foreach ($nivel2->td as $td) {
                        $ant = $act;
                        $act = "";
                        if(substr(trim($td), 0,1)=="(" & substr(trim($td), -1,1)==")"){                            
                            $cl=  str_replace("(", "", trim($td));
                            $cl=  str_replace(")", "", trim($cl));
                            $cl=  $cl*1;                            
                            if($cl>=1 & $cl<=45){                                
                                $act=$cl;
                            }
                        }else{                            
                            if($act=="" & $ant!=""){
                                $mclasif[$ncl][0]=$ant;
                                $mclasif[$ncl][1]=trim($td);
                                $ncl++;
                            }else{
                                if(trim($td)=="Prioridad"){
                                    $antpri="1";
                                }
                                if($antpri=="1"){
                                    foreach($nivel2->nivel2 as $obpri){
                                        $mprioridad["pais"]   = trim($obpri->td[3]);
                                        $mprioridad["docu"]   = trim($obpri->td[4]);
                                        $mprioridad["fecpri"] = trim($obpri->td[5]);
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 2://Titular y Apoderado
                    $mpersonas=array(); 
                    $act="";  
                    $filaper=0;                    
                    foreach ($nivel2->td as $td) {
                        $ant=$act;
                        $act="";
                        switch (trim($td)) {
                            case "Titular":
                                $act="titular";
                                $iper=1;                                                            
                                break;
                            case "Apoderado":
                                $act="apoderado";
                                $iper=1;
                                $filaper++;
                                break;
                            case "Domicilio":
                            case "Dirección":
                                $act=$ant;
                                break;                             
                            default:
                                $mpersonas[$filaper][0]=$ant;
                                if($act=="" & $ant!=""){
                                    if($iper>=4){
                                        $iper=1;
                                        $filaper++;
                                    }                                    
                                    $mpersonas[$filaper][$iper]=trim($td);
                                    $iper++;
                                    $act = $ant;
//                                    print_r($mpersonas);
//                                    echo "<BR>";
                                }
                                break;                            
                        }
                    }
                    break; 
                case 3://Actuaciones
                    break;                 
            }
            $in2++;            
        }
        
        if(trim($coincsigno[0]!="")){
            $signo = str_replace('"', '', trim($coincsigno[0][0]));
            $signo = str_replace('&codi_sign=', '', $signo);
        }
        if(trim($coinchref[0][0])!=""){
            $nomimg = descargaimagen2(trim($coinchref[0][0]));
        }
        if(trim($coinchrefmp3[0][0])!=""){
            $nommp3 = descargamp3(trim($coinchrefmp3[0][0]));
        } 
        
        $datosbasicos["signo"]=$signo;
        $datosbasicos["imagen"]=$nomimg;
        $datosbasicos["mp3"]=$nommp3;
        if($datosbasicos["signo"]*1==0){
            $mdsig=  explode(".", $nomimg);
            $datosbasicos["signo"]=$mdsig[0];
        }
      
        //Corrige Fechas
        //--Fecha de Presentación
        $datosbasicos["fechpres"];
        //--Fecha de Vigencia
        $datosbasicos["vighasta"]=corregirfecha($datosbasicos["vighasta"]);
        //--Fecha de Gaceta
//        $mfecha = explode('//',$datosbasicos["fecgac"]); 
//        $datosbasicos["fecgac"]=$mfecha[2]."-".$mfecha[1]."-".$mfecha[0];
        $datosbasicos["fecgac"]=  corregirfecha($datosbasicos["fecgac"]);
        //Tipo de Denominación
        $tipodenomi = "0";
        switch (strtoupper($datosbasicos["tiposol"])) {
            case "NOMBRES COMERCIALES"://Nombre Comercial
                $tipodenomi = "1";
                break;
            case "MARCAS"://Marca
                $tipodenomi = "2";
                break;
            case "ENSENAS COMERCIALES"://Enseña Comercial
                $tipodenomi = "3";
                break;
            case "LEMAS COMERCIALES"://Lema Comercial
                $tipodenomi = "4";
                break;
            case "DENOMINACION DE ORIGEN"://Denominación de Origen
                $tipodenomi = "5";
                break;
            case "MARCAS COLECTIVAS"://Marcas Colectivas
                $tipodenomi = "6";
                break;
            case "MARCAS DE CERTIFICACION"://Marcas de Certificación
                $tipodenomi = "7";
                break;
            case "MARCA EXTENSION TERRITORIAL"://Marca Extensión Territorial
                $tipodenomi = "8";
                break;
        }  
        
        //Tipo Marca
        switch (strtoupper($datosbasicos["tipomarca"])) {
            case "NOMINATIVA":
                $tipomarcax = "1";
                break;
            case "FIGURATIVA":
                $tipomarcax = "2";
                $deno = "";
                break;
            case "MIXTA":
                $tipomarcax = "3";
                break;
            case "OLFATIVA":
                $tipomarcax = "4";
                $deno = "";
                break;
            case "SONORA":
                $tipomarcax = "5";
                $deno = "";
                break;
            case "TRIDIMENSIONAL":
                $tipomarcax = "6";
                $deno = "";
                break;
        }
        $numexped = $datosbasicos["numexp"];
        $mnumexp = explode(" ", $numexped);
        $anorad = $mnumexp[0];
        $numrad = $mnumexp[1];
        //Ingresa en Tabla de Precarga
        global $ipserver, $userdb, $pwduserdb, $db1;
        $conn = conectar($ipserver, $userdb, $pwduserdb);
        selectdb($db1, $conn);        
        if (sizeof($mclasif) == 1){
            $sql = "INSERT INTO `sim_precargasolweb` 
                    (`ano`, `expediente`, `control`, `signo`, `fecha_solicitud`, 
                    `tipo_denominacion`, `denominacion`, `tipomarca`, `certificado`, 
                    `vigencia`, `clase`, `version`, `cobertura`, `productos_servicios`, 
                    `titular`, `domicilio`, `direccion`, `apoderado`, `gaceta`, 
                    `publicacion`, `fecha_pubicacion`, `prioridad`, `denomi2`) 
                VALUES ('$anorad', '$numrad', '$control','".$datosbasicos["signo"]."', 
                    '".$datosbasicos["fechpres"]."', '$tipodenomi', '".$datosbasicos["denomi"]."',
                    '".$tipomarcax."', '".$datosbasicos["certif"]."', '".$datosbasicos["vighasta"]."', 
                    '".$mclasif[0][0]."', '$versioncl', '$cobertura', '".addslashes($mclasif[0][1])."', 
                    '".addslashes($mpersonas[0][1])."', '".addslashes($mpersonas[0][2])."', 
                    '".addslashes($mpersonas[0][3])."', '".addslashes($mpersonas[1][1])."',
                    '".$datosbasicos["gaceta"]."', '".$datosbasicos["numpub"]."', 
                    '".$datosbasicos["fecgac"]."', 
                    '".trim(addslashes($mprioridad["pais"]." ".$mprioridad["docu"]." ".$mprioridad["fecpri"]))."',
                    '".$datosbasicos["denomi"]."');";
            //echo $sql."<BR><BR>";
            $sql = str_replace("[yamp]", "&", $sql);
            mysql_query(utf8_decode($sql));
        } else {
            for ($i = 0; $i < sizeof($mclasif); $i++) {
                $sql = "INSERT INTO `sim_precargasolweb` 
                        (`ano`, `expediente`, `control`, `signo`, `fecha_solicitud`, 
                        `tipo_denominacion`, `denominacion`, `tipomarca`, `certificado`, 
                        `vigencia`, `clase`, `version`, `cobertura`, `productos_servicios`, 
                        `titular`, `domicilio`, `direccion`, `apoderado`, `gaceta`, 
                        `publicacion`, `fecha_pubicacion`, `prioridad`, `denomi2`) 
                    VALUES ('$anorad', '$numrad', '".$mclasif[$i][0]."','".$datosbasicos["signo"]."', 
                        '".$datosbasicos["fechpres"]."', '$tipodenomi', '".$datosbasicos["denomi"]."',
                        '".$tipomarcax."', '".$datosbasicos["certif"]."', '".$datosbasicos["vighasta"]."', 
                        '".$mclasif[$i][0]."', '$versioncl', '$cobertura', '".addslashes($mclasif[$i][1])."', 
                        '".addslashes($mpersonas[0][1])."', '".addslashes($mpersonas[0][2])."', 
                        '".addslashes($mpersonas[0][3])."', '".addslashes($mpersonas[1][1])."',
                        '".$datosbasicos["gaceta"]."', '".$datosbasicos["numpub"]."', 
                        '".$datosbasicos["fecgac"]."', 
                        '".trim(addslashes($mprioridad["pais"]." ".$mprioridad["docu"]." ".$mprioridad["fecpri"]))."',
                        '".$datosbasicos["denomi"]."');";
                //echo $sql."<BR><BR>";
                $sql = str_replace("[yamp]", "&", $sql);
                mysql_query(utf8_decode($sql));
            }
        }
        unlink($carpeta."/".$txtfile2);
    }
}

function ret_mmarcas_solwebxsigno_p($signo, $carpeta, $control = "") {

    $signoencript = encriptarsigno($signo); 
	$dirsolweb="http://serviciospub.sic.gov.co/Sic/ConsultaEnLinea/2013/RegistroSignos.php?zaqwscersderwerrteyr=pol%F1mkjuiutdrsesdfrcdfds&qwx=ltjS0sLc2L2gb".$signoencript;
    //$dirsolweb="http://serviciospub.sic.gov.co/Sic/ConsultaEnLinea/2013/RegistroSignos.php?zaqwscersderwerrteyr=pol%F1mkjuiutdrsesdfrcdfds&qwx=ltjS0sLc2L2gb".str_pad($signoencript, 10 , "=");
    //echo $dirsolweb."<br>";
    
    if (trim($signoencript) != "") {
        //Contenido de la página        
        $cadsw = ret_txtfile2($dirsolweb);       
        $cadsw = reemplazaracentoshtml($cadsw);
        $cadsw = corregir_otrosacentos($cadsw);
        $cadsw = preg_replace("/<head[^>]*?>.*?<\/head>/si", "", $cadsw); //Elimina Contenido del HEAD
        $cadsw = str_replace("<?", "", $cadsw);
        $cadsw = str_replace("?>", "", $cadsw);
				
        //Recoge el Signo    
        preg_match("/&codi_sign\=[^\"]*\"/si", $cadsw, $coincsigno, PREG_OFFSET_CAPTURE);
        //Recoge enlace imagen
        preg_match("/SignosDistintivos\/Etiquetas[^\"]*jpg/si", $cadsw, $coinchref, PREG_OFFSET_CAPTURE);
        //Recoge enlace mp3
        preg_match("/Etiquetas[^\"]*mp3/si", $cadsw, $coinchrefmp3, PREG_OFFSET_CAPTURE);

        //Retornar Archivo
        $txtfile  = $signo.".txt";
        $txtfile2 = $signo.".xml";
        save_txtinfile($carpeta . "/" . $txtfile, utf8_encode($cadsw));
        //Limpia
        $gestor = @fopen($carpeta . "/" . $txtfile, "r");
        if ($gestor) {
            while (!feof($gestor)) {
                $bufer.= fgetss($gestor, 4096, "<td><th>");
            }
            fclose($gestor);
        }
		$bufer = str_replace("´","",$bufer);
        //save_txtinfile($carpeta . "/" . $signo."2.xml", $bufer);    
        $bufer = preg_replace('/[\s\t\n\r\f\0]/', ' ', $bufer);
        $bufer = trim($bufer);
        $bufer = str_replace("  ", " ", $bufer);        
        $arrayareemplazar = array("&nbsp;", "&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&bull;");
        $arrayareemplazos = array("", "á", "e", "í", "ó", "ú", "");
        $bufer = str_replace($arrayareemplazar, $arrayareemplazos, $bufer); 
        $bufer = str_replace("th>", "td>", $bufer);
        $bufer = str_replace("<th", "<td", $bufer);     

        $bufer = preg_replace('/\s+/', ' ', $bufer);
        $bufer = preg_replace("/<a[[:space:]]*[^>]*>/", "<a>", $bufer);
        $bufer = preg_replace("/<a>[[:space:]]*([^>])*<\/a>/", "", $bufer);
        $bufer = preg_replace("/<td[[:space:]]*([^>]*>)/", "<td>", $bufer);        
        $bufer = preg_replace("/\/\*[[:space:]]*([^\*\/]\*\/)/", "", $bufer);
        $bufer = preg_replace("/^[[:space:]]*([^<]*<)/", "<", $bufer);
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);  
        $bufer = preg_replace("/<td>[[:space:]]*([^<td>]*<td>)/", "<nivel2><td>", $bufer);
		$bufer = preg_replace("/<td>[[:space:]]*([^<th>]*<th>)/", "<nivel2><td>", $bufer);        
        $bufer = preg_replace("/<\/td>[[:space:]]*([^<\/td>]*<\/td>)/", "</td></nivel2>", $bufer);
		$bufer = preg_replace("/<\/th>[[:space:]]*([^<\/td>]*<\/td>)/", "</td></nivel2>", $bufer); 
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);
        $bufer = str_replace("PROPIEDAD INDUSTRIAL", "", $bufer);
        $bufer = corregir_otrosacentos($bufer);
        $bufer = reemplazaracentoshtml($bufer);
        $bufer = str_replace("INFORMACIÓN ACTUAL DEL REGISTRO","", $bufer);
        $bufer = str_replace("[ Ver certificación ]","", $bufer);
        $bufer = str_replace("Datos de la Marca","", $bufer);
        $bufer = str_replace("Ver Gaceta","", $bufer);
        $bufer = str_replace("<td>Tipo de expediente</td>","</td><td>Tipo de expediente</td>", $bufer);
        $bufer = str_replace("Ver Actuaciones", "</nivel2><nivel2>", $bufer);
        $bufer = str_replace("Personas Jurídicas / Naturales", "</nivel2><nivel2>", $bufer);
        $bufer = str_replace("Reproducir Sonido", "", $bufer); 
        $bufer = str_replace("</td> ", "</td>", $bufer);
        $bufer = str_replace("<td></td>", "", $bufer);
		
        $bufer = preg_replace('/\s(?=\s)/', '', $bufer);
        $bufer = preg_replace('/[\n\r\t]/', ' ', $bufer);        
        $bufer = str_replace("[ Ver Título ]", "", $bufer);
        $bufer = str_replace("[Ver Título]", "", $bufer);  
        $bufer = str_replace(" Nro : ", "</td><td>", $bufer); 
		$bufer = str_replace(" Flujo del trÃ¡mite Cerrar ", "", $bufer);
		$bufer = str_replace("RequerimientoVisualizar Requerimiento ", "Requerimiento", $bufer);
		
		$bufer = str_replace("<td> ", "<td>", $bufer);
			
       
        $bufer = corregir_otrosacentos($bufer);
        $bufer = reemplazaracentoshtml($bufer);
        $bufer = str_replace("&", "[yamp]", $bufer); 
        
        
        
        $bufer = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<pag>\n" . $bufer . "\n</pag>"; 
        //echo $bufer."<hr>";
        $bufer=utf8_encode($bufer);
        
        save_txtinfile($carpeta . "/" . $txtfile2, $bufer);
        unlink($carpeta . "/" . $txtfile);  
        
        //Contenido del archivo
        $xmlstr = ret_txtfile1($carpeta . "/" . $txtfile2);
        //Lectura de XML
        $ant = $act = "";

        $xml = new SimpleXMLElement($xmlstr);
        $minfomarca = array();
        $datosbasicos=array();
        foreach ($xml->td as $td) {
            $ant=$act;            
            $act="";
            $td=trim(utf8_decode($td));
            switch ($td) {
                case "Número de expediente":
                    $act="numexp";
                    break;                
                case "Tipo de expediente":
                    $act="tipoexp";
                    break;
                case "Fecha de radicación":
                    $act="fecharad";
                    break;
                case "Hora":
                    $act="horarad";
                    break;
                case "Estado del trámite":
                    $act="esttra";
                    break;
                case "Certificado":
                    $act="certif";
                    break;
                case "Vigencia":
                    $act="vighasta";
                    break;  
                case "Signo":
                    $act="tiposol";
                    break; 
                case "Fecha de radicación":
                    $act="fechrad";
                    break; 
                case "Fecha de presentación":
                    $act="fechpres";
                    break; 
                case "Denominación":
                    $act="denomi";
                    break;     
                case "Tipo de Signo":
                    $act="tipomarca";
                    break;
                case "DATOS DEL SOLICITANTE/TITULAR":
                    $act="nombre";
                    break;
                case "Nombre":
                    $act="nombretit";
                    break;
                case "Domicilio":
                    $act="domiciliotit";
                    break;
                case "Dirección":
                    $act="direcctit";
                    break; 
                case "Representante/Apoderado":
                    $act="apoderado";
                    break; 
                case "Funcionario a cargo";
                    $act="funcionarioSIC";
                    break;
				case "Estado";
                    $act="edotramite";
                    break;  		
                default:
                    if($act=="" & $ant!=""){
                        $datosbasicos[$ant]=trim($td);
                    }
                    break;
            }
        }
//print_r($datosbasicos);
        $in2=0;
        $mclasif=array();
        $mprioridad=array();
        foreach ($xml->nivel2 as $nivel2) {
            switch ($in2) {
                case 1://Publicación
                    $datosbasicos["gaceta"] = trim($nivel2->td[3]);
                    $datosbasicos["numpub"] = trim($nivel2->td[4]);
                    $datosbasicos["fecgac"] = trim($nivel2->td[5]);
                    break;
                case 0://Clasificación y Prioridad
                    $act="";
                    $ncl=0;
                    $antpri="";
                    $mnivel2 = xml2array($nivel2);
                    if((trim($mnivel2["td"][1]))=="Documento"){//Con Prioridad
                        $mprioridad["pais"]   = trim(utf8_decode($mnivel2["td"][3]));
                        $mprioridad["docu"]   = trim(utf8_decode($mnivel2["td"][4]));
                        $mprioridad["fecpri"] = trim(utf8_decode($mnivel2["td"][5]));
                        $in2=$in2-1;
                    }else{
                        foreach ($nivel2->td as $td) {
                            $ant = $act;
                            $act = "";
                            if(substr(trim($td), 0,1)=="(" & substr(trim($td), -1,1)==")"){
                                $cl=  str_replace("(", "", trim($td));
                                $cl=  str_replace(")", "", trim($cl));
                                $cl= $cl*1;
                                if($cl>=1 & $cl<=45){
                                    $act=$cl;
								}
							}else{
                                if($act=="" & $ant!=""){
                                    $mclasif[$ncl][0]=$ant;
									$mclasif[$ncl][1]=trim(utf8_decode($td));
                                    $ncl++;
									
                                }
                            }
                        }
                    }
                    break;
                case 2://Titular y Apoderado
                    $mpersonas=array();
                    $act="";
                    $filaper=0;
                    foreach ($nivel2->td as $td) {
                        $ant=$act;
                        $act="";
                        switch (trim($td)) {
                            case "Titular":
                                $act="titular";
				$iper=1;
                                break;
                            case "Apoderado":
                                $act="apoderado";
                                $iper=1;
                                $filaper++;
                                break;
                            case "Domicilio":
                            case "Dirección":
                                $act=$ant;
                                break;
                            default:
                                $mpersonas[$filaper][0]=$ant;
                                if($act=="" & $ant!=""){
                                    if($iper>=4){
                                        $iper=1;
					$filaper++;
                                    }
				    $mpersonas[$filaper][$iper]=trim($td);
                                    $iper++;
                                    $act = $ant;
                                }
                                break;
                        }
                    }
                    break;
                case 3://Actuaciones
                    break;
            }
            $in2++;
        }           
        
        
        if(trim($coincsigno[0]!="")){
            $signo = str_replace('"', '', trim($coincsigno[0][0]));
            $signo = str_replace('&codi_sign=', '', $signo);
        }
        if(trim($coinchref[0][0])!=""){
            $nomimg = descargaimagen2(trim($coinchref[0][0]));
        }
        if(trim($coinchrefmp3[0][0])!=""){
            $nommp3 = descargamp3(trim($coinchrefmp3[0][0]));
        } 
        
        $datosbasicos["signo"]=$signo;
        $datosbasicos["imagen"]=$nomimg;
        $datosbasicos["mp3"]=$nommp3;
        if($datosbasicos["signo"]*1==0){
            $mdsig=  explode(".", $nomimg);
            $datosbasicos["signo"]=$mdsig[0];
        }
//print_r($datosbasicos);
        //Corrige Fechas
        //--Fecha de Presentación
        $datosbasicos["fecharad"];
        //--Fecha de Vigencia
        $datosbasicos["vighasta"]=corregirfecha($datosbasicos["vighasta"]);
        //--Fecha de Gaceta
        $datosbasicos["fecgac"]=  corregirfecha($datosbasicos["fecgac"]);
        
		//Tipo de Denominación
        $tipodenomi = "0";
        switch (strtoupper($datosbasicos["tiposol"])) {
            case "NOMBRES COMERCIALES"://Nombre Comercial
                $tipodenomi = "1";
                break;
            case "MARCAS"://Marca
                $tipodenomi = "2";
                break;
            case "ENSENAS COMERCIALES"://Enseña Comercial
                $tipodenomi = "3";
                break;
            case "LEMAS COMERCIALES"://Lema Comercial
                $tipodenomi = "4";
                break;
            case "DENOMINACION DE ORIGEN"://Denominación de Origen
                $tipodenomi = "5";
                break;
            case "MARCAS COLECTIVAS"://Marcas Colectivas
                $tipodenomi = "6";
                break;
            case "MARCAS DE CERTIFICACION"://Marcas de Certificación
                $tipodenomi = "7";
                break;
            case "MARCA EXTENSION TERRITORIAL"://Marca Extensión Territorial
                $tipodenomi = "8";
                break;
			case "MARCA INTERNACIONAL"://Marca Extensión Territorial
                $tipodenomi = "9";
                break;	
        }  
        
        //Tipo Marca
        switch (strtoupper($datosbasicos["tipomarca"])) {
            case "NOMINATIVA":
                $tipomarcax = "1";
                break;
            case "FIGURATIVA":
                $tipomarcax = "2";
                $deno = "";
                break;
            case "MIXTA":
                $tipomarcax = "3";
                break;
            case "OLFATIVA":
                $tipomarcax = "4";
                $deno = "";
                break;
            case "SONORA":
                $tipomarcax = "5";
                $deno = "";
                break;
            case "TRIDIMENSIONAL":
                $tipomarcax = "6";
                $deno = "";
                break;
			case "COLOR":
                $tipomarcax = "7";
                $deno = "";
                break;
			case "ANIMADA":
                $tipomarcax = "8";
                $deno = "";
                break;
			case "OTRA":
                $tipomarcax = "9";
                $deno = "";
                break;		
        }
		
		//Estado del Tramite (Flujo SIC)
		switch ($datosbasicos["edotramite"]) {	
            case "Solicitud"://Solicitud//SOLICITUD
                $edo_tramitex = "1";
                break;
            case "Estudio de Forma"://Estudio de Forma//ESTUDIO DE FORMA
                $edo_tramitex = "2";
                break;
            case "Requerimiento"://Requerimiento//REQUERIMIENTO
                $edo_tramitex = "3";
                break;
            case "Publicación"://Publicación//PUBLICACIÓN
                $edo_tramitex = "4";
                break;
            case "Abandono"://Abandono//ABANDONO
                $edo_tramitex = "5";
                break;
            case "Estudio de Fondo"://Estudio de Fondo//ESTUDIO DE FONDO
                $edo_tramitex = "6";
                break;
			case "Oposición"://Oposición//OPOSICIÓN
                $edo_tramitex = "7";
                break;
			case "Concesión"://Concesión//CONCESIÓN
                $edo_tramitex = "8";
                break;
			case "Negación"://Negación//NEGACIÓN
                $edo_tramitex = "9";
                break;		
        }
		
        $numexped = $datosbasicos["numexp"];
        $mnumexp = explode(" ", $numexped);
        $anorad = $mnumexp[0];
        $numrad = $mnumexp[1];
        //Ingresa en Tabla de Precarga
        global $ipserver, $userdb, $pwduserdb, $db1;
        $conn = conectar($ipserver, $userdb, $pwduserdb);
        selectdb($db1, $conn); 
       /* print_r($mclasif);     
        echo "<br>Matriz de Clasificacion:<br>";
        print_r($mclasif);
        echo "<br>Matriz de Datos Basicos:<br>";
        print_r($datosbasicos);
        echo "<br>Prioridad:<br>";
        print_r($mprioridad);
        echo "<br>".$edo_tramitex."<br><br>";*/        
        if (sizeof($mclasif) == 1){
            $sql = "INSERT INTO `sim_precargasolweb` 
                    (`ano`, `expediente`, `control`, `signo`, `fecha_solicitud`, 
                    `tipo_denominacion`, `denominacion`, `tipomarca`, `certificado`, 
                    `vigencia`, `clase`, `version`, `cobertura`, `productos_servicios`, 
                    `titular`, `domicilio`, `direccion`, `apoderado`, `gaceta`, 
                    `publicacion`, `fecha_pubicacion`, `prioridad`, `denomi2`) 
                VALUES ('$anorad', '$numrad', '$control','".$datosbasicos["signo"]."', 
                    '".$datosbasicos["fecharad"]."', '$tipodenomi', '".$datosbasicos["denomi"]."',
                    '".$tipomarcax."', '".$datosbasicos["certif"]."', '".$datosbasicos["vighasta"]."', 
                    '".$mclasif[0][0]."', '$versioncl', '".$edo_tramitex."', '".addslashes($mclasif[0][1])."', 
                    '".addslashes($datosbasicos["nombretit"])."', '".addslashes($datosbasicos["domiciliotit"])."', 
                    '".addslashes($datosbasicos["direcctit"])."', '".addslashes($datosbasicos["apoderado"])."',
                    '".$datosbasicos["gaceta"]."', '".$datosbasicos["numpub"]."', 
                    '".$datosbasicos["fecgac"]."', 
                    '".trim(addslashes($mprioridad["pais"]." ".$mprioridad["docu"]." ".$mprioridad["fecpri"]))."',
                    '".$datosbasicos["denomi"]."');";
//echo $sql."<BR><BR>";
            $sql = str_replace("[yamp]", "&", $sql);
            mysql_query(utf8_decode($sql));
        } else {
            for ($i = 0; $i < sizeof($mclasif); $i++) {
                $sql = "INSERT INTO `sim_precargasolweb` 
                        (`ano`, `expediente`, `control`, `signo`, `fecha_solicitud`, 
                        `tipo_denominacion`, `denominacion`, `tipomarca`, `certificado`, 
                        `vigencia`, `clase`, `version`, `cobertura`, `productos_servicios`, 
                        `titular`, `domicilio`, `direccion`, `apoderado`, `gaceta`, 
                        `publicacion`, `fecha_pubicacion`, `prioridad`, `denomi2`) 
                    VALUES ('$anorad', '$numrad', '".$mclasif[$i][0]."','".$datosbasicos["signo"]."', 
                        '".$datosbasicos["fecharad"]."', '$tipodenomi', '".$datosbasicos["denomi"]."',
                        '".$tipomarcax."', '".$datosbasicos["certif"]."', '".$datosbasicos["vighasta"]."', 
                        '".$mclasif[$i][0]."', '$versioncl', '".$edo_tramitex."', '".addslashes($mclasif[$i][1])."', 
                        '".addslashes($datosbasicos["nombretit"])."', '".addslashes($datosbasicos["domiciliotit"])."', 
                        '".addslashes($datosbasicos["direcctit"])."', '".addslashes($datosbasicos["apoderado"])."',
                        '".$datosbasicos["gaceta"]."', '".$datosbasicos["numpub"]."', 
                        '".$datosbasicos["fecgac"]."', 
                        '".trim(addslashes($mprioridad["pais"]." ".$mprioridad["docu"]." ".$mprioridad["fecpri"]))."',
                        '".$datosbasicos["denomi"]."');";
//echo $sql."<BR><BR>";
                $sql = str_replace("[yamp]", "&", $sql);
                mysql_query(utf8_decode($sql));
            }
        }
       unlink($carpeta."/".$txtfile2);
    }
}

function encriptarsigno($signo){
    global $ipserver, $userdb, $pwduserdb, $db1;
    $conn = conectar($ipserver, $userdb, $pwduserdb);
    selectdb($db1, $conn);      
    $sql="SELECT _orb_func_sic_encriptarsigno('$signo') AS X";
    //echo $sql."<br>";
    $rsql =mysql_query($sql);
    if(mysql_num_rows($rsql)>0){
        return mysql_result($rsql, 0, "X");
    }
}

function ret_sigencripxgac($pkgac){
    global $ipserver, $userdb, $pwduserdb, $db1;
    $conn = conectar($ipserver, $userdb, $pwduserdb);
    selectdb($db1, $conn);      
    $sql="SELECT DISTINCT _orb_func_sic_encriptarsigno(a.imagen)
            FROM sim_tb_extregistro AS a
            WHERE a.imagen >  '0' AND a.fk_gaceta =  '$pkgac'
                AND (a.fk_tipomarca ='2' or a.fk_tipomarca = '3')
            ORDER BY 1 ASC";
    $rsql=mysql_query($sql);
    if(mysql_num_rows($rsql)>0){
        return sql2array($rsql);
    }    
}

function descargarimgxgac($pkgac){
    $mimgenc=ret_sigencripxgac($pkgac);
	$nomdir = "GACCO".$pkgac;
    $nomdirimg = $nomdir."/IMGGACCO".$pkgac;
    if(!is_dir($nomdirimg)){
        mkdir($nomdirimg, 0777, true);
    }
    
    $urliniimg = "http://serviciospub.sic.gov.co/Sic/PropiedadIndustrial/";//"http://serviciospub.sic.gov.co/~lurrego/Sic/PropiedadIndustrial/";
    for($i=0; $i<sizeof($mimgenc); $i++){
        $sigenc=$mimgenc[$i][0];        
        if(trim($sigenc)!=""){
            //$dirsolweb="http://serviciospub.sic.gov.co/Sic/ConsultaEnLinea/RegistroSignos.php?zaqwscersderwerrteyr=polñmkjuiutdrsesdfrcdfds&qwx=0tjS0sLc2NDcpp".str_pad($sigenc, 10 , "=");
            $dirsolweb="http://serviciospub.sic.gov.co/Sic/ConsultaEnLinea/2013/RegistroSignos.php?zaqwscersderwerrteyr=pol%F1mkjuiutdrsesdfrcdfds&qwx=ltjS0sLc2L2gb".str_pad($sigenc, 10 , "=");
            echo $dirsolweb."<br>";
            //Contenido de la página        
            $cadsw = ret_txtfile2($dirsolweb);
            
            preg_match("/SignosDistintivos\/Etiquetas[^\"]*jpg/si", $cadsw, $coinchref, PREG_OFFSET_CAPTURE);
            //Recoge enlace mp3
            //preg_match("/Etiquetas[^\"]*mp3/si", $cadsw, $coinchrefmp3, PREG_OFFSET_CAPTURE);
            $urlimgx = $urliniimg.$coinchref[0][0];
            $numsigno = array_pop(explode("/",$urlimgx));            
            $imgdestino = $nomdirimg.'/'.$numsigno;
            echo $imgdestino."<br>";
            $contents = file_get_contents($urlimgx);
            $savefile = fopen($imgdestino, 'w');
            fwrite($savefile, $contents);
            fclose($savefile);            
        }
    }
}
class SimpleXMLExtended extends SimpleXMLElement {
	  public function addCDATA($cData) {
	    $node = dom_import_simplexml($this);
	    $no = $node->ownerDocument;
	    $node->appendChild($no->createCDATASection($cData));
	  }
}
        
function xml2array ($xmlObject,$out=array ()) {
    foreach ((array) $xmlObject as $index=>$node) 
        $out[$index]=(is_object ($node))?xml2array($node):$node;
    return $out;
}
?>