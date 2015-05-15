<html>
<body>
	<h4>Carga Gaceta de Paraguay</h4>
	<form enctype="multipart/form-data" action="import2.php" method="post">
	  <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
	  <table width="600">
		<tr>
			<td>Seleccione el archivo XML(previo):</td>
			<td><input type="file" name="file" /></td>
			<td><input type="submit" value="Upload" /></td>
		</tr>
	  </table>
	</form>
</body>
</html>