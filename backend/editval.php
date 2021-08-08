<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();

if(!$_POST["study_id"]) {
	$q='insert into studies (`'.$_POST["editval"].'`,`owner`,`institutions`,`subjects`, `rounds`, `settings`) values ("'.$mysqli->real_escape_string($_POST["value"]).'",'.$_SESSION["user_id"].',"[]","[]",\'{"rounds":[],"active":""}\',"{}")';
	$re=$mysqli->query($q);
// 	$res["log"].=$q;
	$res["study_id"]=$mysqli->insert_id;
} else {
	$q='update studies set `'.$_POST["editval"].'`="'.$mysqli->real_escape_string($_POST["value"]).'" where id='.$_POST["study_id"];
	$re=$mysqli->query($q);
}
$res["log"].=$q;
echo json_encode($res);
