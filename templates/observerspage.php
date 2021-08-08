<?php
	$relative="../";
	include_once($relative."/settings/conf.php");
	include_once($systemdirs["backend"]."checklogin.php");
	
	if(!$_SESSION["user_id"]) exit;

	$q="select * from obsschemes where owner=".$_SESSION["user_id"];
	$result=$mysqli->query($q);
	
	
?>
<div class="container">
	<div class="row">
		<div class="col">
			<button type="button" class="btn btn-primary float-right" id="newScheme"><?= _('New Observation Scheme');?></button>
		</div>
	</div>

	<div class="row">
		<div class="col">
			<h3><?= _("Observation Schemes"); ?></h3>
			<table class="table table-sm table-hover mt-2">
				<thead>
					<tr>
					<th scope="col"><?= _('Name');?></th>
					<th scope="col"><?= _('Description');?></th>
					<th scope="col"><?= _('Used in');?></th>
					<th scope="col"><?= _('Public');?></th>
					<th scope="col"><?= _('Edit');?></th>
					<th scope="col"><?= _('Delete');?></th>
					</tr>
				</thead>
				<tbody class="table-striped " id="userlist">
				<?php
					while($r=$result->fetch_assoc()) { ?>
						<tr data-obsid="<?= $r["id"];?>"><td><?= $r["name"];?></td><td><?= $r["description"];?></td><td><?= $r["usedin"];?></td><td><?= $r["public"];?></td><td class="editscheme"><i class="fas fa-edit"></i></td><td class="deletescheme"><i class="fas fa-trash text-danger"></i></td><tr>
				<?php	}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
