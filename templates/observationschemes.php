<?php
	$relative="../";
	include_once($relative."/settings/conf.php");
	include_once($systemdirs["backend"]."checklogin.php");
	

	if(!$_SESSION["user_id"]) exit;
	$q='select o.*, CONCAT("0:",o.language,":",l2.language_name) as orig_language, (select GROUP_CONCAT(CONCAT(t.id,":",t.language,":",l1.language_name) SEPARATOR ",") from translations t left join languages l1 on t.language=l1.code  where t.obsscheme_id=o.id) as translations from obsschemes o left join languages l2 on o.language=l2.code  where ';
	if($_POST["publicschemes"]!="true") {
		$q1=$q."owner=".$_SESSION["user_id"];
		$result=$mysqli->query($q1);
	} if($_POST["publicschemes"]=="true" or $result->num_rows==0) {
		$q1=$q."public=1";
		$result=$mysqli->query($q1);
		$ispublic=true;
	}
?>
<div class="container">
	<div class="row">
		<div class="col">
		</div>
	</div>

	<div class="row">
		<div class="col">
			<h3>
				<?= _("Observation Schemes"); ?>
				<span class="float-right"><input type="checkbox" class="bs-toggle" id="usepublic" <?=($ispublic?'checked="checked"':'');?> data-toggle="toggle" data-on="<?= _("Public&nbsp;Schemes");?>" data-off="<?= _("Personal&nbsp;Schemes");?>" data-onstyle="observe" data-offstyle="observe"> <button type="button" class="btn btn-observe" id="newScheme"><?= _('New Observation Scheme');?></button></span>
			</h3>
			<table class="table table-sm table-hover mt-2">
				<thead>
					<tr>
					<th scope="col"><?= _('Name');?></th>
					<th scope="col"><?= _('Description');?></th>
<!--					<th scope="col"><?= _('Used in');?></th>-->
					<th scope="col"><?= _('Language');?></th>
					<th scope="col"><?= _('Scheme');?></th>
					<th scope="col"><?= _('Try it');?></th>
					<?php if(!$ispublic) {?>
						<th scope="col"><?= _('Public');?></th>
						<th scope="col"><?= _('Edit');?></th>
						<th scope="col"><?= _('Delete');?></th>
					<?php	} else { ?>
						<th scope="col"><?= _('Clone');?></th>
					<?php	} ?>
					</tr>
				</thead>
				<tbody class="table-striped " id="userlist">
				<?php
					while($result and $r=$result->fetch_assoc()) { 
						$languages=array_diff(explode(",",$r["translations"]),array(""));
						array_unshift($languages,$r["orig_language"]);
						$flags="";
						foreach($languages as $l) {
							$codelang=explode(":",$l);
							$flags.='<img src="img/flags/'.$codelang[1].'.png" class="flagthumb '.($codelang[0]?'translate':'').'" data-transid="'.$codelang[0].'" alt="'.$codelang[2].'" title="'.$codelang[2].'"> ';
						}
						?>
						<tr data-obsid="<?= $r["id"];?>">
							<td><?= $r["name"];?></td>
							<td><?= $r["description"];?></td>
							<!--<td><?= $r["usedin"];?></td>-->
							<td class=""><?= $flags;?><i class="translate fas fa-language float-right" title="<?=_('Create new translation');?>"></i></td>
							<td><img src="./img/obsschemes/<?= $r["id"];?>.png<?= (($_POST["update"] and $_POST["obsid"]==$r["id"])?"?".rand():""); ?>" class="obsthumb"></td>
							<td class="tryscheme"><i class="far fa-eye"></i></td>
							<?php if(!$ispublic) {?>
								<td class="togglepublic"><?= ($r["public"]?'<i class="fas fa-check-circle"></i>':'<i class="fas fa-times-circle"></i>');?></td>
								<td class="editscheme"><i class="fas fa-edit"></i></td>
								<td class="deletescheme"><i class="fas fa-trash text-danger"></i></td>
							<?php } else {?>
								<td class="clonescheme"><i class="fas fa-copy"></i></td>
							<?php } ?>
						<tr>
				<?php	}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="modal" id="showimage">
	<div class="modal-dialog modal-img modal-dialog-centered" role="document">
		<div class="modal-content modal-img-body" >
			<div class="modal-body ">
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<p id="pimg">
				<img id="imgsrc" class="obsimg img-fluid rounded mx-auto d-block" src="">
				</p>
			</div>
		</div>
	</div>
</div>
