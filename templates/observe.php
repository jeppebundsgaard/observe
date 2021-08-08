<div class="container">
	<div class="row ">
		<div class="col text-center ">
			<h1><?= _('Observe');?></h1>
			<p><input class="form-control text-center mx-auto timeSize" id="sessionDate" type="text" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" required size="10"></p>
		</div>
	</div>
</div>
<div class="container-fluid" id="observe">
</div>
<div class="container">
	<div class="row">
		<div class="col">
			<button id="newsequence" class="btn btn-observe"><i class="fas fa-plus-circle observefont"></i> <?=_("New Sequence");?></button>
			<?php if($_POST["study_id"]) { ?>
				<button id="saveobs" class="btn btn-observe float-right"><i class="fas fa-file-export observefont"></i> <?=_("Save and finish");?></button>
			<?php } else { ?>
				<button id="download" class="btn btn-observe px-2 float-right mx-3"><i class="fas fa-file-export observefont "></i> <?=_("Download and finish");?></button>
				<button id="finish" class="btn btn-observe float-right mx-3"><i class="fas fa-file-export observefont"></i> <?=_("Finish");?></button>
			<?php } ?>
		</div>
	</div>
</div>
<!-- <button id="sshot" class="btn btn-info "><i class="fas fa-camera"></i> <?=_("Screenshot");?></button> -->
<!-- <img id="sshotimg"> -->
<?php 
if($_POST["id"]) {
	if($_POST["study_id"]) 
		$q='select o.*,t.translation from studies s left join obsschemes o on o.id=s.observation_scheme left join translations t on t.id=s.translation where s.id='.$_POST["study_id"];
	elseif($_POST["language"]!="") 
		$q='select * from obsschemes o left join translations t on o.id=t.obsscheme_id where o.id='.$_POST["id"].' and t.language="'.$_POST["language"].'"';
	else
		$q="select * from obsschemes where id=".$_POST["id"];
	$result=$mysqli->query($q);
	$r=$result->fetch_array();
	$log.=$q;
	$res=array("obsid"=>$r["id"],"obsscheme"=>json_decode($r["obsscheme"]),"name"=>$r["name"],"description"=>$r["description"],"translation"=>json_decode($r["translation"]));
} else $res=array("obsid"=>0,"obsscheme"=>"","name"=>"","description"=>"");
$res["log"]=$log;
$res["previousPage"]=$_POST["previousPage"];
