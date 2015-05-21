<?php
$xmlo = "pdf/597.xml";
$xml = simplexml_load_string($xmlo, '', LIBXML_NOCDATA, ' '); 
print_r $xml;
?>