<?php
	$relative="../";
	include_once($relative."/settings/conf.php");
	include_once($systemdirs["backend"]."checklogin.php");
	
	if(!$_SESSION["user_id"]) exit;

	$q="select *,(select count(*) from observations where study_id=s.id) as numobs from studies s where owner=".$_SESSION["user_id"]." order by `status`,`name`";
	$result=$mysqli->query($q);
$statuses=array('under_construction'=>_("Under construction"),'active'=>_("Active"),'inactive'=>_("Inactive"),'finished'=>_("Finished"));
?>
<div class="container">
	<div class="row">
		<div class="col">
			<button type="button" class="btn btn-observe float-right" id="newStudy"><?= _('New Study');?></button>
		</div>
	</div>

	<div class="row">
		<div class="col">
			<h3><?= _("Studies"); ?></h3>
			<table class="table table-sm table-hover mt-2">
				<thead>
					<tr>
					<th scope="col"><?= _('Name');?></th>
					<th scope="col"><?= _('Description');?></th>
					<th scope="col"><?= _('Status');?></th>
					<th scope="col"><?= _('Edit');?></th>
					<th scope="col"><?= _('# Observations');?></th>
					<th scope="col"><?= _('View');?></th>
					<th scope="col"><?= _('Export');?></th>
					<th scope="col"><?= _('Delete');?></th>
					</tr>
				</thead>
				<tbody class="table-striped " id="userlist">
				<?php
					while($r=$result->fetch_assoc()) { ?>
						<tr data-id="<?= $r["id"];?>"><td><?= $r["name"];?></td><td><?= $r["description"];?></td><td class="changestatus" data-status="<?= $r["status"];?>"><?= $statuses[$r["status"]];?></td><td class="editstudy"><i class="fas fa-edit"></i></td><td><?= $r["numobs"];?></td><td class="viewobservations"><?= ($r["numobs"]>0?'<i class="fas fa-eye"></i>':'');?></td><td class="export"><?= ($r["numobs"]>0?'<i class="fas fa-file-download"></i>':'');?></td><td class="deletestudy"><i class="fas fa-trash text-danger"></i></td><tr>
				<?php	}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php 
$res["statuses"]=$statuses;
