<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();

$q='update studies set `observation_scheme`="'.$mysqli->real_escape_string($_POST["obsid"]).'", `translation`="'.$mysqli->real_escape_string($_POST["transid"]).'" where id='.$_POST["study_id"];
$re=$mysqli->query($q);
$res["log"].=$q;
echo json_encode($res);
