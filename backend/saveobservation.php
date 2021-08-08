<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

// $res["log"].=print_r($_POST,true);
$res=array();
$observations=mysql_json_encode($_POST["session"]["observations"]);
$starttime=mysql_json_encode($_POST["session"]["starttime"]);
$endtime=mysql_json_encode($_POST["session"]["endtime"]);
$date=mysql_json_encode($_POST["session"]["date"]);
$study_id=mysql_json_encode($_POST["institutioncontext"]["study_id"]);
$institution_id=mysql_json_encode($_POST["institutioncontext"]["institution_id"]);
$participants=mysql_json_encode($_POST["institutioncontext"]["participants"]);
$groups=mysql_json_encode($_POST["institutioncontext"]["groups"]);
$subject=mysql_json_encode($_POST["institutioncontext"]["subject"]);

$q='insert into observations (`study_id`,`observations`,`date`,`starttime`,`endtime`,`institution_id`,`participants`,`groups`,`subject`,`observer_id`,`round`) VALUES ('.$study_id.','.$observations.','.$date.','.$starttime.','.$endtime.','.$institution_id.','.($_POST["institutioncontext"]["participants"]?$participants:"'[]'").','.($_POST["institutioncontext"]["groups"]?$groups:"'[]'").','.$subject.','.$_SESSION["realuser_id"].',(select rounds->>"$.active" from studies s where s.id='.$study_id.'))';
$re=$mysqli->query($q);
$res["log"].=$q;
$res["id"]=$mysqli->insert_id;

echo json_encode($res);
