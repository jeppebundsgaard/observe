<?php
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");
if(!$_SESSION["user_id"]) exit;
# Safety check ... Do you own this study?
$study_id=$_GET["study_id"];
// $q='select s.id as study_id,obsscheme,s.name as studyname from studies s left join obsschemes o on s.observation_scheme=o.id where s.id='.$_GET["study_id"].' and s.owner='.$_SESSION["user_id"];
// $result=$mysqli->query($q);
// if($result)
// 	$r=$result->fetch_assoc();
// else {echo $q;exit;}
include($systemdirs["backend"]."get_context.php");

// $study_id=$r["study_id"];
// $studyname=$r["studyname"];
// $obsscheme=json_decode($r["obsscheme"]);

$studyname=$context["studyname"];
$obsscheme=$context["obsscheme"];

#$res["log"].=$q;

$q='select * from  observations where study_id='.$study_id.' order by date,starttime';
$result=$mysqli->query($q);
if($result)
	$r=$result->fetch_assoc();
else {echo $q;exit;}

header( 'Content-Type: text/csv' );
header( 'Content-Disposition: attachment;filename='.$studyname.".csv");

$out = fopen('php://output', 'w');
$contextcols=array("observer_id","round","institution_id","groups","participants","subject"); 
$contextcolskeys=array_flip($contextcols);
$valkeys=array_flip($obsscheme->categories);
fputcsv($out, array_merge($contextcols,$obsscheme->categories),";");
while($r=$result->fetch_assoc()) {
	$r["groups"]=rmjson($r["groups"]);
	$r["participants"]=rmjson($r["participants"]);
		
	$context=array_intersect_key($r,$contextcolskeys);
	$observations=json_decode($r["observations"],true);
// print_r($observations);
	foreach($observations as $observation) {
		array_walk($observation,function(&$v) {$v=(is_array($v)?implode(",",$v):$v);});
		$data=array_intersect_key($observation,$valkeys);
		$row=array_merge($context,$data);
		fputcsv($out, $row,";");
	}
}
fclose($out);
//Headers... 
// Studyname
function rmjson($s) {
	return preg_replace('/["\\[\\]]/',"",$s);
}
