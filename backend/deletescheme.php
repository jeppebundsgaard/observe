<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

if(!$_SESSION["user_id"]) exit;
$res=array();
$q="delete from obsschemes where id=".$_POST["id"]." and owner=".$_SESSION["user_id"];
unlink("../img/obsschemes/".$_POST["id"].".png");

$result=$mysqli->query($q);

echo json_encode($res);
