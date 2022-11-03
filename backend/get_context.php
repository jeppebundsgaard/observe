<?php 
$q='select s.id as study_id,obsscheme,t.translation,s.name as studyname, institutions,  rounds->>"$.activeround" as activeround,settings from studies s left join obsschemes o on s.observation_scheme=o.id left join translations t on t.id=s.translation where s.id='.$study_id.' and s.owner='.$_SESSION["user_id"];

$result=$mysqli->query($q);
if($result)
	$context=$result->fetch_assoc();
else {echo $q;exit;}
$context["settings"]=json_decode($context["settings"]);
$context["obsscheme"]=json_decode($context["obsscheme"]);
if($context["translation"]) {
	$context["translation"]=json_decode($context["translation"]);
	
		// Translate
		$nextk=0;
		foreach($context["translation"] as $i=>$r) {
			if($r->name!="")
				$context["obsscheme"]->rows[$i]->name=$r->name;
			if($r->cols) {
				foreach($r->cols as $j=>$co) {
					if($co->name!="")
						$context["obsscheme"]->rows[$i]->cols[$j]->name=$co->name;
					if($co->cats) {
						foreach($co->cats as $k=>$ca) {
							if($ca->name!="")	{
								$context["obsscheme"]->rows[$i]->cols[$j]->cats[$k]->name=$ca->name;
								$context["obsscheme"]->categories[$nextk]=$ca->name;
							}
							$nextk++;
							if($ca->vals) {
								foreach($ca->vals as $l=>$v) {
									if($v!="")
										$context["obsscheme"]->rows[$i]->cols[$j]->cats[$k]->vals[$l]=$v;
								}
							}
						}
					}
				}
			}
		}


}
if(count($context["obsscheme"]->categories)==count($context["settings"]->categoryOrder) and empty(array_diff($context["obsscheme"]->categories,$context["settings"]->categoryOrder))) $context["obsscheme"]->categories=$context["settings"]->categoryOrder;
$context["institutions"]=json_decode($context["institutions"]);
$res["context"]=$context;

