<?php
	$relative="./";
	include_once($relative."/settings/conf.php");
	include_once($systemdirs["backend"]."checklogin.php");
	
	if(!$_SESSION["user_id"]) exit;



?>
<div class="container">
		<?php 
		$maxlen=200;
		$maxreflen=100;
		$q="select * from observers o left join studies s on o.study_id=s.id where o.user_id=".$_SESSION["realuser_id"];
		$result=$mysqli->query($q);
		if($result->num_rows>0) { ?>
			<div class="row">
				<div class="col text-center mt-5">
					<h3><?= _("Choose a study to start observe");?></h3>
				</div>
			</div>
			<div class="row ">
		<?php
			while($r=$result->fetch_assoc()) { ?>
			<div class="col d-flex justify-content-center collapse show study study<?=$r["id"];?>">
				<div class="card my-4 obscard" style="width: 18rem;">
					<img class="card-img-top" src="./img/obsschemes/<?= $r["observation_scheme"];?>.png" alt="<?= _("Observation Scheme") ?>">
					<div class="card-body">
						<h5 class="card-title"><?=$r["name"];?></h5>
						<p class="card-text"><?= substr($r["description"],0,$maxlen).(strlen($r["description"])>$maxlen?" ...":"");?></p>
						<p class="card-text"><small class="text-muted"><?= substr($r["reference"],0,$maxreflen).(strlen($r["reference"])>$maxreflen?" ...":"");?></small></p>
						<button class="btn btn-observe observestudy" data-studyid="<?= $r["id"];?>"><?=_('Choose this study');?></button>
					</div>
				</div>
			</div>
			<?php }
		} else { ?>
		<div class="row ">
			<div class="col">
				<h3><?= _("Start using Observe.education");?></h3>
				<ul>
					<li><a class="firstvisitlink" href="#" data-page="myUser"><?= _("Provide your name and choose an optional username.");?></a></li>
					<li><a class="firstvisitlink" href="#" data-page="observationschemes"><?= _("Create a new observation scheme.");?></a></li>
					<li><a class="firstvisitlink" href="#" data-page="studies"><?= _("Create a study using your own or a public observation scheme.");?></a></li>
 					<li><a class="firstvisitlink" href="#" data-page="observationschemes"><?= _("Observe using a public observation scheme");?></a></li>
					<li><?= _("If you are an observer in an existing study, please contact the manager of the study and ask to be added to the list of observers. Then the study will appear here.");?></li>
				</ul>
			</div>
		</div>
		<?php }?>
	</div>
	<div class="row collapse" id="chooseinstitution">
		<div class="col collapse" id="subjectcol">
			<h3><?= _("Choose a subject");?></h3>
			<div id="subjects"></div>
		</div>
		<div class="col collapse" id="institutioncol">
			<h3><?= _("Choose an institution");?></h3>
			<div id="institutions"></div>
		</div>
	</div>
	<div class="row collapse" id="choosegs">
		<div class="col collapse" id="groupcol">
			<h3><?= _("Choose groups");?></h3>
			<div id="groups"></div>
		</div>
		<div class="col collapse" id="participantcol">
			<h3><?= _("Choose participants");?></h3>
			<div id="participants"></div>
		</div>
		<div class="col-12 text-center">
			<button id="startobserve" class="btn btn-lg btn-observe  my-3" data-studyid="<?= $r["id"];?>"><?=_('Start observing');?></button>
		</div>
	</div>
	
</div>
