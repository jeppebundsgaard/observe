<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();

parse_str($_POST["translation"],$translation);
$res["log"]=print_r($translation,true);
$q='insert into translations ('.($translation["transid"]?'`id`,':'').'`obsscheme_id`,`language`,`name`,`description`,`reference`,`translation`,`translator`) values ('.($translation["transid"]?$mysqli->real_escape_string($translation["transid"]).',':'').''.$mysqli->real_escape_string($translation["obsid"]).',"'.$mysqli->real_escape_string($translation["language"]).'","'.$mysqli->real_escape_string($translation["name"]).'","'.$mysqli->real_escape_string($translation["description"]).'","'.$mysqli->real_escape_string($translation["reference"]).'",'.mysql_json_encode($translation["rows"]).','.$_SESSION["user_id"].') on duplicate key update name=values(name),description=values(description),reference=values(reference),translation=values(translation)';
#name=new.name,description=new.description,reference=new.reference,translation=new.translation';

$mysqli->query($q);
$res["log"].=$q;
echo json_encode($res);

