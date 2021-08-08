<?php
	$relative="../";
	include_once($relative."/settings/conf.php");
	include_once($systemdirs["backend"]."checklogin.php");
	
	if(!$_SESSION["user_id"]) exit;

include($systemdirs["backend"]."get_context.php");


$q='select o.*,if(u.username!="",u.username,u.email) as observer,institution_id from  observations o left join users u on o.observer_id=u.user_id left join studies s on o.study_id=s.id where o.study_id='.$_POST["study_id"].' order by date,starttime';
$res["log"].=$q;
$result=$mysqli->query($q);
if(!$result) echo $q;
else {
	$res["sessions"]=$result->fetch_all(MYSQLI_ASSOC);
		array_walk($res["sessions"],function(&$v) {
		$v["observations"]=json_decode($v["observations"]);
		$v["groups"]=json_decode($v["groups"]);
		$v["participants"]=json_decode($v["participants"]);
	});
} 
?>
<div class="container">
	<div class="row">
		<div class="col">
			<button class="btn btn-observe float-right onbutton" id="viewgraphics" data-on="<?= _("Hide&nbsp;graphics");?>" data-off="<?= _("View&nbsp;graphics");?>"></button>
			<button class="btn btn-observe float-right mx-3" id="settings" data-toggle="modal" data-target="#settingsmodal"><?= _('<i class="fas fa-cog"></i> Settings');?></button>
			<h3><?= sprintf(_("Sessions in %s"),$context["studyname"]); ?></h3>
			<small class="text-muted"><?= _('Click on a session to view the observations.');?></small>
			<table class="table table-sm table-hover mt-2">
				<thead>
					<tr>
						<th scope="col"><?= _('Round');?></th>
						<th scope="col"><?= _('Date');?></th>
						<th scope="col"><?= _('Start time');?></th>
						<th scope="col"><?= _('End time');?></th>
						<th scope="col"><?= _('Institution');?></th>
						<th scope="col"><?= _('Groups');?></th>
						<th scope="col"><?= _('Participants');?></th>
						<th scope="col"><?= _('Subject');?></th>
						<th scope="col"><?= _('Observer');?></th>
					</tr>
				</thead>
				<tbody class="table-striped " id="sessions">
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="modal" id="obsmodal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg modal-observations" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?= sprintf(_('Observations by %1$s in %2$s'),'<span id="observer"></span>','<span id="inst"></span>');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="form-row">
						<div class="col">
							<button class="btn btn-observe float-right mx-3" id="settings" data-toggle="modal" data-target="#settingsmodal"><?= _('<i class="fas fa-cog"></i> Settings');?></button>
							<small><?= sprintf(_('Date: %s'),'<span id="date"></span>');?></small><br>
							<small><?= sprintf(_('Groups: %s'),'<span id="groups"></span>');?></small><br>
							<small><?= sprintf(_('Participants: %s'),'<span id="participants"></span>');?></small>
						</div>
					</div>
					<div class="form-row">
						<div class="col">
							<canvas id="obscanvas" ></canvas>
						</div>
					</div>
					<div class="form-row">
						<div class="col overflow-auto">
							<table class="table table-sm table-hover mt-2">
								<thead>
									<tr id="obstablehead">
										<th scope="col"><?= _('Start time');?></th>
										<th scope="col"><?= _('End time');?></th>
										<?php foreach($context["obsscheme"]->categories as $category) echo '<th scope="col">'.$category.'</th>';?>
									</tr>
								</thead>
								<tbody class="table-striped " id="observations">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-observe" id="prev"><?=_('Previous');?></button>
				<button type="button" class="btn btn-observe" id="next"><?=_('Next');?></button>
				<button type="button" class="btn btn-observe" data-dismiss="modal"><?=_('Close');?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="settingsmodal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?= _('Settings');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="form-row">
						<div class="col">
							<label for=""><?= _('Order of observation categories in tables');?></label>
							<ul class="list-group" id="categoryOrder">
								<?php foreach($context["obsscheme"]->categories as $category) {?>
								<li class="list-group-item"><?= $category;?></li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<div class="form-row">
						<div class="col">
							<label for="curleyCategories"><?= _('Categories used for naming and coloring curley brackets graphics');?></label>
							<small class="text-muted"><?=_('Hold down Ctrl or Shift keys while clicking to select multiple categories.');?></small>
							<select class="custom-select" id="curleyCategories" multiple="multiple">
							<?php 
								$curleyCategories=($context["settings"]->curleyCategories?$context["settings"]->curleyCategories:array($context["obsscheme"]->categories[0]));
								foreach($context["obsscheme"]->categories as $category) echo '<option value="'.$category.'" '.(in_array($category,$curleyCategories)?"selected":"").'>'.$category.'</option>';
							?>
							</select>
						</div>
					</div>
					<div class="form-row">
						<div class="col overflow-auto">
						
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-observe" data-dismiss="modal"><?=_('Cancel');?></button>
				<button type="button" class="btn btn-observe" id="savesettings"><?=_('Save');?></button>
			</div>
		</div>
	</div>
</div>
