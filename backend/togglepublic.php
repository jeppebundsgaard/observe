<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();
if($_POST["id"]) {
	$q='update obsschemes set public=if(public,0,1) where id='.$_POST["id"];
	$mysqli->query($q);
	$res["log"].=$q;
} 

echo json_encode($res);
