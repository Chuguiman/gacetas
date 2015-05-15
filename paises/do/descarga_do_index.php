<?PHP

	require_once ("../../clases/_func_tranformar_gac_ext.php");
	require_once ('_func_html2xmlgac_do2.php');

	$carp = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'];
    $carpeta = str_replace("descarga_do_index.php", "../tmp/", $carp);
	
	$inicio = $_POST["ngac"];
	$cantidad = 1;
	
	//HTML 
    $final = $inicio + $cantidad;
    for ($numsigno = $inicio; $numsigno < $final; $numsigno++) {
        desc_actaweb_do($numsigno, $carpeta);
    }
		

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
		
	<script>
	function crea_xml(gaceta){
	        var parametros = {
	                "gaceta" : gaceta,
	        };
	        $.ajax({
                data:  parametros,
	                url:   'paises/do/crear_xml.php',
	                type:  'post',
	                beforeSend: function () {
	                        $("#result").html("Procesando, espere por favor...");
	                },
	                success:  function (response) {
	                        $("#result").html(response);
	                }
	        });
	}
	</script>
	
  </head>
  <body>
	<h3 class="demo-panel-title"># GACDO</h3>
	<div class="row">
        <div class="col-xs-3">
        	<div class="form-group">
	        	<input type="text" id="gaceta" value="" placeholder="# GACDO" class="form-control" />
        	</div>          
        </div>
		<div class="col-xs-3">
			<input type="button" href="javascript:;" onclick="crea_xml($('#gaceta').val());return false;" value="Crear XML"  class="btn btn-block btn-lg btn-warning"/>
        </div>
    </div>
	<div id="result">
	</div>

  </body>
 </html>