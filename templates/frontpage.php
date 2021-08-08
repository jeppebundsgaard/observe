<div class="container">
	<div class="row">
		<div class="col text-center mt-5">
			<p class="display-4"><?= _("Observe teaching and learning practices");?></p>
			<p class="lead"><?= _("Observe allows researchers and practitioners to systematically observe teaching and learning practices.");?></p>
			<hr class="my-4">
			<p><?= _('<a href="?backend=1" class="smallmenulink">Sign up</a> and start your own observe.education study using one of the predefined observation schemes - or develop one of your own.');?></p>
			<p><?= _("Or start by trying out one of the observation schemes below.");?></p>
		</div>
	</div>
	<div class="row ">
		<?php 
		$maxlen=200;
		$maxreflen=200;
		$q='select o.*,t2.description as tdescription, t2.name as tname, t2.reference as treference, t2.language as tlanguage, CONCAT(o.language,":",l2.language_name) as orig_language, (select GROUP_CONCAT(CONCAT(t.language,":",l1.language_name) SEPARATOR ",") from translations t left join languages l1 on t.language=l1.code where t.obsscheme_id=o.id) as translations from obsschemes o left join languages l2 on o.language=l2.code left join translations t2 on t2.obsscheme_id=o.id where public=1 and (t2.language="'.$lang.'" or t2.language is NULL or o.language="'.$lang.'")';
		$result=$mysqli->query($q);
		while($r=$result->fetch_assoc()) { 
			$languages=array_diff(explode(",",$r["translations"]),array(""));
			array_unshift($languages,$r["orig_language"]);
			$flags="";
			foreach($languages as $k=>$l) {
				$codelang=explode(":",$l);
				$flags.='<img src="img/flags/'.$codelang[0].'.png" class="flagthumb tryobserve" data-obsid="'.$r["id"].'" '.($k?'data-language="'.$codelang[0].'"':'').' alt="'.$codelang[1].'" title="'.$codelang[1].'"> ';
			}
			$description=(($r["tdescription"] and $r["language"]!=$lang)?$r["tdescription"]:$r["description"]);
			$name=(($r["tname"] and $r["language"]!=$lang)?$r["tname"]:$r["name"]);
			$reference=(($r["treference"] and $r["language"]!=$lang)?$r["treference"]:$r["reference"]);
		?>
		<div class="col d-flex justify-content-center">
			<div class="card my-4 obscard" style="width: 18rem;">
				<img class="card-img-top" src="./img/obsschemes/<?= $r["id"];?>.png" alt="<?= _("Observation Scheme") ?>">
				<div class="card-body">
					<h5 class="card-title"><?=$name;?></h5>
					<p class="card-text"><?= substr($description,0,$maxlen).(strlen($description)>$maxlen?" ...":"");?></p>
					<p class="card-text"><small class="text-muted"><?= substr($reference,0,$maxreflen).(strlen($reference)>$maxreflen?" ...":"");?></small></p>
				</div>
				<div class="card-footer">
					<button class="btn btn-observe tryobserve" data-obsid="<?= $r["id"];?>" <?= (($r["tlanguage"] and $r["language"]!=$lang)?'data-language="'.$r["tlanguage"].'"':'');?>><?=_("Try it!");?></button>
					<span class="float-right"><?=$flags;?></span>
				</div>
			</div>
		</div>
		<?php }?>
	</div>
</div>
