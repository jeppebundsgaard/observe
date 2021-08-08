<?php $relative="../";
	include_once($relative."/settings/conf.php"); 
	include_once($systemdirs["backend"]."checklogin.php");
	
	$q="select * from organizations where org_id='".$_SESSION["user_id"]."'";
	$result=$mysqli->query($q);
	$r=$result->fetch_assoc();
?>
<div class="row">
	<div class="col-3">
		<div id="csseditor" style="height:800px">
			<pre ><?php echo file_get_contents("../css/custom/org".$_SESSION["user_id"].".css"); ?></pre>
		</div>
		<div class="modal-footer"><button class="btn btn-success" id="editCSSsave"><?= _('Finish');?></button>
			<button class="btn btn-success" id="refreshStyle"><?= _('Use changes');?></button>
		</div>
	</div>
	<div class="col-9">
		<div class="row d-flex justify-content-center">
			<div class="col text-center">
				<h1 class="orgname"><?= $r["orgname"];?></h1>
				<h3 class="orgslogan"><?= $r["orgslogan"];?></h3>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<nav class="navbar navbar-expand-lg navbar-light bg-light">
					<button class="navbar-toggler" type="button" data-toggle="collapse" role="tab" data-target="#navbartoggle" aria-controls="navbartoggle" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse tab-pane navbar-collapse" id="navbartoggle">
						<ul class="navbar-nav nav " id="showTab" role="tab-list" >
							<li class="nav-item">
								<a class="nav-link active" id="games-tab" data-toggle="tab" role="tab" href="#games"  aria-controls="games" ><?= _('Navigation Bar');?></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="gameList-tab" data-toggle="tab" role="tab" href="#gameList"  aria-controls="gameList" ><?= _('Toggles at small screen sizes');?></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="tables-tab" data-toggle="tab" role="tab" href="#tables"  aria-controls="tables" ><?= _('Click!');?></a>
							</li>
						</ul>
					</div>
					<a class="navbar-brand" href="#"><?= _('Tournament Name');?></a>
				</nav>
			</div>
		</div>
		<div class="row">
			<?php 
				for($i=1;$i<7;$i++) { 
					echo '<div class="col"><h'.$i.'>'._('Header')." ".$i.'</h'.$i.'></div>';
				}
			?>
			<div class="col">
				<?= _('Normal text');?>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<!-- Table with Hover -->
				<table class="table table-hover"><thead><tr><th><?= _('Table');?></th><th><?= _('With');?></th><th><?= _('Hover');?></th></tr></thead><tbody><tr><td><?= _('Cell');?> 1</td><td><?= _('Cell');?> 2</td><td><?= _('Cell');?> 3</td></tr><tr><td><?= _('Cell');?> 1</td><td><?= _('Cell');?> 2</td><td><?= _('Cell');?> 3</td></tr></tbody></table>
			</div>
			<div class="col">
				<table class="table table-sm table-striped"><thead><tr><th scope="col"><?=_('Striped');?></th><th scope="col"><?= _('Small');?></th><th scope="col"><?= _('Table');?></th></tr></thead><tbody class="table-striped" ><tr><td><?= _('Cell');?> 1</td><td><?= _('Cell');?> 2</td><td><?= _('Cell');?> 3</td></tr><tr><td><?= _('Cell');?> 1</td><td><?= _('Cell');?> 2</td><td><?= _('Cell');?> 3</td></tr></tbody></table>
			</div>
			<div class="col">
				<div class="card table-card">
					<div class="card-header table-card-header">
						<h5 data-toggle="collapse" data-target="#collapseTable" aria-expanded="true" aria-controls="collapseTable" id="cardheader"><?= _('League Table Header');?></h5>
					</div>
					 <div id="collapseTable" class="collapse show" aria-labelledby="table_round_roundpart" data-parent="#cardheader">
						<div class="card-body table-card-body">
							<h3><?= _('League Table');?></h3>
							<table class="table table-sm table-striped"><thead><tr><th scope="col"><?=_('Table');?></th><th scope="col"><?= _('In');?></th><th scope="col"><?= _('Card');?></th></tr></thead><tbody><tr><td><?= _('Cell');?> 1</td><td><?= _('Cell');?> 2</td><td><?= _('Cell');?> 3</td></tr><tr><td><?= _('Cell');?> 1</td><td><?= _('Cell');?> 2</td><td><?= _('Cell');?> 3</td></tr></tbody></table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div class="row">
			<?php 
				$game=file_get_contents($systemdirs["templates"]."game.php");
				$m=array('shortname'=>"TU",'score_team1'=>2,'score_team2'=>3,"setscores"=>"15-10, 15-10, 12-15, 6-15, 8-15");
				$name=_("First Round. Group A");
				$showResults=true;
				for($i=0;$i<20;$i++) {
					echo '<div class="col-2">';
					$t[1]["name"]=($i>14?_('No.').' '.($i%4).' '._('First Round'):_('Team').' '.($i%5+1));
					$t[2]["name"]=($i>12?_('No.').' '.($i%5).' '._('First Round'):_('Team').' '.($i%7+1));
					$t[3]["name"]=($i>10?_('No.').' '.($i%6).' '._('First Round'):_('Team').' '.($i));
					$m['game_no']=$i;
					$m["played"]=($i<7);
					$m["team1_id"]=$m["team2_id"]=$m["team3_id"]=(10-$i);
					$tnos[$i]=$m["tournament_id"]=$i;
					include($systemdirs["templates"]."game.php");
					echo '</div>';
				}
			?>
				</div>
			</div>
		</div>
	</div>
</div>
