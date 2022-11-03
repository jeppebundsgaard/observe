<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();
if($_POST["id"]) {
	$q='insert into obsschemes (obsscheme,name,description,owner,public) select obsscheme,name,description,'.$_SESSION["user_id"].',0 from obsschemes where id='.$_POST["id"];
 	$re=$mysqli->query($q);
	$res["log"].=$q;
 	$res["id"]=$mysqli->insert_id;
	copy("../img/obsschemes/".$_POST["id"].".png","../img/obsschemes/".$res["id"].".png");
	
	$q='insert into translations (`obsscheme_id`,`language`,`name`,`description`,`reference`,`translation`,`translator`) select '.$res["id"].',`language`,`name`,`description`,`reference`,`translation`,`translator` from translations where obsscheme_id='.$_POST["id"];
 	$re=$mysqli->query($q);
} 

echo json_encode($res);
