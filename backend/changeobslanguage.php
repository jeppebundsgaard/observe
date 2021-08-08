<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();
if($_POST["id"]) {
	$q='update obsschemes set `language`="'.$mysqli->real_escape_string($_POST["newlanguage"]).'" where id='.$_POST["id"];
	$mysqli->query($q);
	$res["log"].=$q;
} 

echo json_encode($res);
