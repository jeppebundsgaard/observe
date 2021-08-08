<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();
	$q='show columns from obsschemes like "language"';
	$result=$mysqli->query($q);
	$r=$result->fetch_assoc();
	$res["languages"]=explode("','",preg_replace("/enum\('(.*?)'\)/","$1",$r["Type"]));
	$res["obsid"]=$_POST["obsid"];
	$res["log"].=$q;

echo json_encode($res);
