<?php 
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");
if(!$_SESSION["user_id"]) exit;
	
$maxlen=200;
$maxreflen=200;

$q0='select o.*, CONCAT("0:",o.language,":",l2.language_name) as orig_language, (select GROUP_CONCAT(CONCAT(t.id,":",t.language,":",l1.language_name) SEPARATOR ",") from translations t left join languages l1 on t.language=l1.code  where t.obsscheme_id=o.id) as translations from obsschemes o left join languages l2 on o.language=l2.code  where ';
if($_POST["publicschemes"]!="true") {
	$q=$q0."owner=".$_SESSION["user_id"];
	$result=$mysqli->query($q);
} if($_POST["publicschemes"]=="true" or $result->num_rows==0) {
	$q=$q0."public=1";
	$result=$mysqli->query($q);
	$ispublic=true;
}
//  	echo($q);
$button='<span class="float-right"><input type="checkbox" class="bs-toggle" id="usepublic" '.($ispublic?'checked="checked"':'').' data-toggle="toggle" data-on="'. _("Public&nbsp;Schemes").'" data-off="'. _("Personal&nbsp;Schemes").'" data-onstyle="observe" data-offstyle="observe">';
$html='';
while($r=$result->fetch_assoc()) { 
	$languages=array_diff(explode(",",$r["translations"]),array(""));
	array_unshift($languages,$r["orig_language"]);
	$flags="";
	foreach($languages as $k=>$l) {
		$codelang=explode(":",$l);
		$flags.='<img src="img/flags/'.$codelang[1].'.png" class="flagthumb chooseobsscheme" data-obsid="'. $r["id"].'" data-transid="'.$codelang[0].'"  data-language="'.$codelang[1].'" alt="'.$codelang[2].'" title="'.$codelang[2].'"> ';
	}
$html.='
	<div class="col d-flex justify-content-center">
		<div class="card my-4 obscard" style="width: 18rem;">
			<img class="card-img-top" src="./img/obsschemes/'. $r["id"].'.png" alt="'. _("Observation Scheme") .'">
			<div class="card-body">
				<h5 class="card-title">'.$r["name"].'</h5>
				<p class="card-text">'. substr($r["description"],0,$maxlen).(strlen($r["description"])>$maxlen?" ...":"").'</p>
				<p class="card-text"><small class="text-muted">'. substr($r["reference"],0,$maxreflen).(strlen($r["reference"])>$maxreflen?" ...":"").'</small></p>
			</div>
			<div class="card-footer">
				<p class="text-muted">'._('Choose by clicking on a language').'</p>
				<span class="float-right">'.$flags.'</span>
			</div>
		</div>
	</div>';
}
$res["log"]=$q;
$res["html"]=$html;
$res["button"]=$button;
echo json_encode($res);
