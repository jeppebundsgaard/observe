<?php 
	$relative="../";
	include_once($relative."/settings/conf.php");
	include_once($systemdirs["backend"]."checklogin.php");
	if(!$_SESSION["user_id"]) exit;
$res=array();
if($_POST["transid"])
	$q='select o.*,t.name as tname,t.description as tdescription, t.reference as treference,t.language as tlanguage,t.translation,language_name from obsschemes o left join translations t on t.obsscheme_id=o.id left join languages l on l.code=t.language where t.id='.$_POST["transid"];
else 
	$q="select * from obsschemes where id=".$_POST["id"];
$result=$mysqli->query($q);
$r=$result->fetch_assoc();
// print_r($r);
#echo $q;
#$res=array("obsid"=>$r["id"],"obsscheme"=>json_decode($r["obsscheme"]),"name"=>$r["name"],"description"=>$r["description"],"reference"=>$r["reference"],"log"=>$log);
?>
<div class="col ">
	<button class="btn btn-observe float-right savetranslation"><?=_('Save Translation');?></button>
</div>
<form id="translationform">
	<div class="container">
		<div class="row">
			<div class="col">
				<h3><?= _('Translate observation scheme');?></h3>
				<input type="hidden" name="obsid" value="<?= $_POST["id"]; ?>">
				<input type="hidden" name="transid" value="<?= $_POST["transid"]; ?>">
				<label for="language"><?=_('Target Language');?></label>
				<?php
				if(!$_POST["transid"]) {
					$q="select * from languages";
					$resultlang=$mysqli->query($q);
					?> 
					<img id="langflag" class="flagthumb">
					<select name="language" id="language" class="custom-select" requried>
					<option></option>
					<?php
					while($rlang=$resultlang->fetch_array()) {
						echo '<option value="'.$rlang["code"].'">'.$rlang["language_name"].'</option>';
					}
					?>
					</select>
					<?php
				} else { ?>
				<h4><img src="./img/flags/<?= $r["tlanguage"]; ?>.png" class="flagthumb"> <?= $r["language_name"];?></h4>
				<input type="hidden" name="language" id="language" value="<?= $r["tlanguage"]; ?>">
				<?php
				}
				?>
				<label for="osname"><?=_('Observation Scheme Name');?></label>
				<p class="text-muted" ><?= $r["name"];?></p>
				<input type="text" class="form-control" name="name" value="<?= $r["tname"] ?>" id="osname">
				<label for="osdescr" ><?=_('Observation Scheme Description');?></label>
				<p class="text-muted" ><?= $r["description"];?></p>
				<textarea name="description" class="form-control" id="osdescr"><?= $r["tdescription"] ?></textarea>
				<?php if($r["reference"]) { ?>
					<label for="osref"><?=_('Reference');?></label>
					<p class="text-muted" ><?= $r["reference"];?></p>
					<input type="text" class="form-control" name="osref" value="<?= $r["treference"] ?>" id="osref">
					<small><?= _('Recommended quote when using public observation schemes.');?></small>
				<?php } ?>
			</div>
		</div>
		<div class="row">
			<div class="col">
			<h4><?= _('Observation scheme');?></h4>
			<?php
				function translation($txt,$trans,$rno,$cono=-1,$cano=-1,$vno=-1) { echo '<label for="#r_'.$rno.'_'.$cono.'_'.$cano.'_'.$vno.'">'.$txt.'</label><input id="r_'.$rno.'_'.$cono.'_'.$cano.'_'.$vno.'" class="form-control" type="text" value="'.$trans.'" name="rows['.$rno.']'.($cono>-1?'[cols]['.$cono.']'.($cano>-1?'[cats]['.$cano.']'.($vno>-1?'[vals]['.$vno.']':''):''):'').($vno>-1?'':'[name]').'">';}
				$rows=json_decode($r["obsscheme"])->rows;
				$translation=json_decode($r["translation"]);
				echo '<ol>';
				foreach($rows as $rno=>$row) {
					echo '<li><small class="text-muted">'._("Row").'</small><br>';
					if($row->name!="") translation($row->name,$translation[$rno]->name,$rno);
					echo '<ol>';
					foreach($row->cols as $cono=>$col) {
						echo '<li><small class="text-muted">'._("Column").'</small><br>';
						if($col->name!="") translation($col->name,$translation[$rno]->cols[$cono]->name,$rno,$cono);
						echo '<ol>';
						foreach($col->cats as $cano=>$cat) {
							echo '<li><small class="text-muted">'._("Category").'</small><br>';
							if($cat->name!="") translation($cat->name,$translation[$rno]->cols[$cono]->cats[$cano]->name,$rno,$cono,$cano);
							echo '<ol>';
							foreach($cat->vals as $vno=>$value) {
								echo '<li><small class="text-muted">'._("Value").'</small><br>';
								translation($value,$translation[$rno]->cols[$cono]->cats[$cano]->vals[$vno],$rno,$cono,$cano,$vno);
								echo '</li>';
							}
							echo '</ol></li>';
						}
						echo '</ol></li>';
					}
					echo '</ol></li>';
				}
				echo '</ol>';
			?>
			</div>
		</div>
	</div>
</form>
<div class="col ">
	<button class="btn btn-observe float-right savetranslation"><?=_('Save Translation');?></button>
</div>
