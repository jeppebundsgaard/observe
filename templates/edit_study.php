<?php
	$relative="../";
	include_once($relative."/settings/conf.php");
	include_once($systemdirs["backend"]."checklogin.php");
	
	if(!$_SESSION["user_id"]) exit;
if($_POST["id"]) {
	$q="select s.*,l.language_name,if(s.translation>0,t.language,o.language) as language,o.name as obsschemename from studies s left join obsschemes o on o.id=s.observation_scheme left join translations t on t.id=s.translation left join languages l on l.code=if(s.translation>0,t.language,o.language) where s.id=".$_POST["id"]." and s.owner=".$_SESSION["user_id"];
	$result=$mysqli->query($q);
	$r=$result->fetch_assoc();
	
	$institutions=json_decode($r["institutions"]);
	
	$q="select * from users u left join observers o on u.user_id=o.user_id where study_id=".$_POST["id"];
	$result=$mysqli->query($q);
	$observers="";
	while($o=$result->fetch_assoc()) {
		$observers.='<div class="observer" data-userid="'.$o["user_id"].'">'.($o["name"]?$o["name"]." (":"").($o["username"]?$o["username"]:$o["email"]).($o["name"]?")":"").'<i class="fas fa-trash text-danger float-right deleteobserver"></i></div>';
	};
	$q="select email from invited_users where study_id=".$_POST["id"];
	$result=$mysqli->query($q);
	while($o=$result->fetch_assoc()) {
		$observers.='<div class="observer invited" data-userid="0" data-useremail>'.($o["email"]).'<i class="fas fa-trash text-danger float-right deleteobserver"></i></div>';
	};

	$roundopts="";
	$rounds=json_decode($r["rounds"]);
	foreach($rounds->rounds as $around) $roundopts.='<option value="'.str_replace("\"","&quot",$around).'" '.($around==$rounds->active?'selected="selected"':'').'>'.$around.'</option>';
}

?>
<div class="container">
	<div class="form-row">
		<div class="col">
			<h3><?= _("Edit study");?></h3>
		</div>
	</div>
	
	<div class="form-row">
		<div class="col">
			<label for="name" class="col-form-label"><strong><?= _('Name');?></strong></label>
			<input class="editval form-control form-control-sm" type="text" name="name" id="name" value="<?= $r["name"];?>">
			<input type="hidden" id="study_id" value="<?= $_POST["id"];?>">
		</div>
	</div>
	<div class="form-row">
		<div class="col">
			<label for="description" class="col-form-label"><?= _('Description');?></label>
			<textarea class="form-control editval notname form-control form-control-sm" name="description" id="description" ><?= $r["description"];?></textarea>
		</div>
	</div>
	<div class="form-row">
		<div class="col">
			<label for="observation_scheme" class="col-form-label"><?= _('Observation Scheme');?></label>
			<p><img src="img/flags/<?= $r["language"];?>.png" class="flagthumb" alt="<?= $r["language_name"];?>" title=""> <span class="strong" id="obsschemename"><?=$r["obsschemename"];?></span></p>
			<p><button class="btn btn-observe" id="selectobsscheme"><?= ($r["obsschemename"]?_('Change Observation Scheme'):_('Select Observation Scheme'));?></button></p>
		</div>
	</div>
	<div class="form-row">
		<div class="col">
			<label for="activeround" class="col-form-label"><?= _('Active round');?></label>
			<select class="custom-select notname custom-select-sm" id="activeround"><option></option><?=$roundopts;?></select>
			<input class="form-control notname form-control-sm" type="text" id="newround" placeholder="<?= _('New round');?>">
		</div>
	</div>
	<div class="form-row">
		<div class="col">
			<label for="observers" class="col-form-label"><?= _('Observers');?></label>
			<div class="pool" id="observers">
				<?= $observers;?>
			</div>
			<p class="noneyet text-muted <?= ($observers?"":"show") ?>"><?=_('No observers appointed yet.');?></p>
		</div>
		<div class="col">
			<label for="addobservers" class="col-form-label"><?= _('Add Observers');?></label>
			<small class="form-text text-muted"><?= _('Write one username or e-mail address on each line. If there is no user with that e-mail address, an invitation to join Observe.education will be sent.');?></small>
			<textarea class="form-control addobjs notname" id="addobstxtarea"></textarea>
			<button type="button" class="btn btn-observe addobj float-right "  id="addobsbtn"><?=_("Add observers");?></button>
		</div>
	</div>
	
		
	<div class="form-row">
		<div class="col">
			<label for="subjects" class="col-form-label"><?= _('Subjects');?></label>
			<div class="pool" id="subjects">
			<?php
				$subjects=json_decode($r["subjects"]);
				foreach($subjects as $s) {
					echo '<div class="instobj subject"><span class="subjectspan">'.$s.'</span><i class="fas fa-trash text-danger float-right deletesubject"></i></div>';
				}
			?>
			</div>
			<p class="noneyet text-muted <?= ($subjects?"":"show") ?>"><?=_('No subjects defined yet.');?></p>			
			<small class="subjects somethere collapse text-muted <?= ($subjects?"show":"") ?>"><?= _('Edit subjects by clicking on them.');?></small>
		</div>
		<div class="col">
			<label for="addsubjects" class="col-form-label"><?= _('Add Subjects');?></label>
			<small class="form-text text-muted"><?= _('Write one subject on each line.');?></small>
			<textarea class="form-control addobjs notname"></textarea>
			<button type="button" class="btn btn-observe addobj float-right"><?=_("Add subjects");?></button>
		</div>
	</div>
	
	<div class="form-row">
		<div class="col">
			<label class="col-form-label"><?= _('Institutions');?></label>
			<div class="pool" id="institutions">
				<?php
					$i=0;
					foreach($institutions as $v) {
						echo '<div class="instobj" data-instnum="'.$i.'"><span class="editinstitution">'.$v->id.": ".$v->name.'</span><i class="fas fa-trash text-danger float-right deleteinst"></i></div>';
						$i++;
					}
				?>
			</div>
			<p class="noneyet text-muted <?= ($institutions?"":"show") ?>"><?=_('No institutions created yet.');?></p>			
			<small class="form-text somethere collapse text-muted <?= ($institutions?"show":"") ?>"><?= _('Click on the institutions to edit and add groups and participants.');?></small>
		</div>
		<div class="col">
			<label for="addinstitutions" class="col-form-label"><?= _('Add Institutions');?></label>
			<small class="form-text text-muted"><?= _('Write one institution on each line. Each institution will be given a unique ID. You can provide one yourself before each institution name and separate with a semicolon (;).');?></small>
			<textarea class="form-control addobjs notname"></textarea>
			<button type="button" class="btn btn-observe addobj float-right"><?=_("Add institutions");?></button>
		</div>
	</div>
	<div class="form-row">
		<div class="col">
				<button type="button" class="btn btn-observe float-right" id="done"><?=_('Done');?></button>
		</div>
	</div>
</div>
<div class="modal" id="instmodal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?= _('Edit institution, groups and participants');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="form-row">
						<div class="col-1">
							<label for="instid" class="col-form-label"><?= _('Id');?></label>
							<input class="form-control notname form-control-sm" type="text" id="instid" value="">
							<input type="hidden" id="instnum" value="">
						</div>
						<div class="col">
							<label for="instname" class="col-form-label"><?= _('Name');?></label>
							<input class="form-control notname form-control-sm" type="text" id="instname" value="">
						</div>
					</div>
					<div class="form-row">
						<div class="col">
							<label for="groups" class="col-form-label"><?= _('Groups');?></label>
							<div class="pool" id="groups"></div>
							<p class="groups noneyet text-muted"><?= _('No groups created yet.');?></p><small class="groups somethere collapse text-muted"><?= _('Edit ID and name by clicking on them.');?></small>
						</div>
						<div class="col">
							<label for="addgroups" class="col-form-label"><?= _('Add Groups');?></label>
							<small class="form-text text-muted"><?= _('Write one group on each line. Each group will be given a unique ID. You can provide one yourself before each group name and separate with a semicolon (;).');?></small>
							<textarea class="form-control addobjs notname"></textarea>
							<button type="button" class="btn btn-observe addobj float-right"><?=_("Add groups");?></button>
						</div>
					</div>
					<div class="form-row">
						<div class="col">
							<label for="participants" class="col-form-label"><?= _('Participants');?></label>
							<div class="pool" id="participants"></div>
							<p class="participants noneyet text-muted"><?= _('No participants created yet.');?></p><small class="participants somethere collapse text-muted"><?= _('Edit ID and name by clicking on them.');?></small>
						</div>
						<div class="col">
							<label for="addparticipants" class="col-form-label"><?= _('Add Participants');?></label>
							<small class="form-text text-muted"><?= _('Write one participant on each line. Each participant will be given a unique ID. You can provide one yourself before each participant name and separate with a semicolon (;).');?></small>
							<textarea class="form-control form-control addobjs notname"></textarea>
							<button type="button" class="btn btn-observe addobj float-right"><?=_("Add participants");?></button>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="saveinstitution"><?=_('Save');?></button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?=_('Close');?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="obsschememodal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?= _('Select Observation Scheme');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="form-row">
						<div class="col" id="privpublbutton">
						</div>
					</div>
					<div class="form-row" id="obsschemes">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?=_('Close');?></button>
			</div>
		</div>
	</div>
</div>


<?php 
$res["institutions"]=$institutions;
