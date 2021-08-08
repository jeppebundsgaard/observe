<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();
if(is_numeric($_POST["study_id"])) {
	$q="update studies set settings='{\"categoryOrder\":".json_encode($_POST["categoryOrder"]).",\"curleyCategories\":".json_encode($_POST["curleyCategories"])."}' where id=".$_POST["study_id"];
	$mysqli->query($q);
	$res["log"].=$q;
}
include("get_context.php");
echo json_encode($res);
