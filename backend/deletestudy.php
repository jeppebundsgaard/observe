<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

if(!$_SESSION["user_id"]) exit;
$res=array();
$q="delete from studies where id=".$_POST["id"]." and owner=".$_SESSION["user_id"];

$result=$mysqli->query($q);
#$res["log"].=$q;
echo json_encode($res);
