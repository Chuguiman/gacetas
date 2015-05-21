﻿<?PHP
	require_once ('_func_xmlgac_uy.php');

	$ngac = $_POST["ngac"];
	$fechapub = $_POST["fechapub"];
	gacxml2_uy ($ngac, $fechapub);

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
	                url:   'paises/uy/crear_xml.php',
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
	<h3 class="demo-panel-title"># GACUY</h3>
	<div class="row">
        <div class="col-xs-3">
        	<div class="form-group">
	        	<input type="text" id="gaceta" value="" placeholder="# GACUY" class="form-control" />
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