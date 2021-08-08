<?php 
$q='select s.id as study_id,obsscheme,s.name as studyname, institutions,  rounds->>"$.activeround" as activeround,settings from studies s left join obsschemes o on s.observation_scheme=o.id where s.id='.$_POST["study_id"].' and s.owner='.$_SESSION["user_id"];
$result=$mysqli->query($q);
if($result)
	$context=$result->fetch_assoc();
else {echo $q;exit;}
$context["settings"]=json_decode($context["settings"]);
$context["obsscheme"]=json_decode($context["obsscheme"]);
if(count($context["obsscheme"]->categories)==count($context["settings"]->categoryOrder) and empty(array_diff($context["obsscheme"]->categories,$context["settings"]->categoryOrder))) $context["obsscheme"]->categories=$context["settings"]->categoryOrder;
$context["institutions"]=json_decode($context["institutions"]);
$res["context"]=$context;

