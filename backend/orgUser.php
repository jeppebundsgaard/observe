<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");
if($_POST["org_id"] and $_SESSION["user_id"]!=1) exit;
$org_id=($_POST["org_id"]?$_POST["org_id"]:$_SESSION["user_id"]);

$update=$_POST["update"];
$res=array();
$res["org_id"]=$_POST["org_id"];
if($_POST["invite"]) {
	$q='update users set org_id="'.$org_id.'" where email="'.$_POST["invite"].'"';
}
if($_POST["create"]) {
	$q='insert into users (`org_id`,`email` ,`password`) VALUES ("'.$org_id.'","'.$_POST["create"].'","'.$_POST["password"].'")';
}
if($_POST["remove"]) {
	$q='update users set org_id=0 where user_id='.$_POST["remove"];
}
if($_POST["deleteuser"] and $_SESSION["user_id"]==1) {
	$q='delete from users where user_id='.$_POST["deleteuser"];
}
	

$result=$mysqli->query($q);
if((!$result or $mysqli->affected_rows<1) and $_POST["invite"])
		$res["warning"]=_("Unable to invite the user. Are you sure a user with that e-mail address exists?");
elseif(!$result and $_POST["create"])
		$res["warning"]=_("Unable to create the user. A user with that e-mail address might exist already...");

$res["log"]=$q;

echo json_encode($res);
