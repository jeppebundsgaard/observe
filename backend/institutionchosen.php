<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");
$q='select institutions from  studies where id='.$_POST["study_id"];
$result=$mysqli->query($q);
// $res["log"].=$q;
$r=$result->fetch_assoc();
$institution=json_decode($r["institutions"])[$_POST["instnum"]];
$res["groups"]=$institution->groups;
$res["participants"]=$institution->participants;
$res["inst_id"]=$institution->id;
$res["log"].=print_r($res,true);
echo json_encode($res);
