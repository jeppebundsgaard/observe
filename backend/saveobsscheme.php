<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();
$obsscheme=$mysqli->real_escape_string($_POST["obsscheme"]);
$name=$mysqli->real_escape_string($_POST["name"]);
$description=$mysqli->real_escape_string($_POST["description"]);
$reference=$mysqli->real_escape_string($_POST["reference"]);
if($_POST["id"]) {
	$q='update obsschemes set obsscheme="'.$obsscheme.'",name="'.$name.'",description="'.$description.'",reference="'.$reference.'" where id='.$_POST["id"];
	$mysqli->query($q);
	$res["log"].=$q;
 	$res["id"]=$_POST["id"];
} else  {
	$q='insert into obsschemes (obsscheme,name,description,reference,owner,public) VALUES ("'.$obsscheme.'","'.$name.'","'.$description.'","'.$reference.'",'.$_SESSION["user_id"].',0)';
 	$re=$mysqli->query($q);
	$res["log"].=$q;
 	$res["id"]=$mysqli->insert_id;
}
$res["obsscheme"]=json_decode($_POST["obsscheme"]);
echo json_encode($res);
