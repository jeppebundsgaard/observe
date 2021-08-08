<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

$res=array();
$col=$_POST["col"];
#$res["log"].=print_r($_POST,true);
$i=0;
$whereandvals=array();
for($i=0;$i<count($_POST["whereandvals"]);$i=$i+2) {
	$whereandvals[]=mysql_json_encode($_POST["whereandvals"][$i]);
	if(!$_POST["deleteelem"]=="true")
		$whereandvals[]=mysql_json_encode($_POST["whereandvals"][$i+1]);
}
#array_walk(,function (&$v) {$v=json_encode($v);});//'objectify');

#$res["log"].=print_r($_POST,true);
if($_POST["update"]=="true") {
	$q='update studies set `'.$col.'`=JSON_SET(`'.$col.'`,'.implode(',',$whereandvals).') where id='.$_POST["study_id"];
} else if($_POST["arrayappend"]=="true") {
	$q='update studies set `'.$col.'`=JSON_ARRAY_APPEND(`'.$col.'`,'.implode(',',$whereandvals).') where id='.$_POST["study_id"];
} else if($_POST["deleteelem"]=="true") {
	$q='update studies set `'.$col.'`=JSON_REMOVE(`'.$col.'`,'.implode(',',$whereandvals).') where id='.$_POST["study_id"];
}
$re=$mysqli->query($q);
$res["log"].=$q;
echo json_encode($res);
