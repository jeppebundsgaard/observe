<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();

if(!is_numeric($_POST["user"]))
	$q='delete from invited_users where study_id='.$mysqli->real_escape_string($_POST["study_id"]).' and email LIKE "'.$mysqli->real_escape_string($_POST["user"]).'"';
else
	$q='delete from observers where study_id='.$mysqli->real_escape_string($_POST["study_id"]).' and user_id='.$mysqli->real_escape_string($_POST["user"]);
$re=$mysqli->query($q);
$res["log"]=$q;
echo json_encode($res);
