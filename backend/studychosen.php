<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$q='select institutions,observation_scheme,subjects from  studies where id='.$_POST["study_id"];
$result=$mysqli->query($q);
$res["log"].=$q;
$r=$result->fetch_assoc();
$res["institutions"]=array();
foreach(json_decode($r["institutions"]) as $inst) {
	$res["institutions"][]=array("id"=>$inst->id,"name"=>$inst->name);
};
$res["study_id"]=$_POST["study_id"];
$res["subjects"]=($r["subjects"]?json_decode($r["subjects"]):array());
$res["observation_scheme"]=$r["observation_scheme"];

$res["log"].=print_r($res,true);
echo json_encode($res);
