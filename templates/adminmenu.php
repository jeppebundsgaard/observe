<nav class="navbar navbar-light navbar-expand-lg admin-navbar">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#adminToggler" aria-controls="adminToggler" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
	<div class="collapse tab-pane navbar-collapse " id="adminToggler">
		<ul class="nav-bar nav  w-100">
			<li class="nav-item "><a class="nav-link adminmenulink" href="#" data-page="startobserve"><?= _('Observe');?></a></li>
			<li class="nav-item "><a class="nav-link adminmenulink" href="#" data-page="observationschemes"><?= _('Observation Schemes');?></a></li>
			<li class="nav-item "><a class="nav-link adminmenulink" href="#" data-page="studies"><?= _('Studies');?></a></li>
		<?php if($_SESSION["orgMember"] and $_SESSION["permissions"]=="admin") {?> 
			<li class="nav-item justify-content-end float-right" id="showMyOrg">
				<a class="nav-link smallmenulink" href="#"><?= _('My Organization');?></a>
			</li>
		<?php }?>
		<?php if($_SESSION["orgMember"] and $_SESSION["user_id"]==1) {?> 
			<li class="nav-item justify-content-end float-right" id="showMyObserve">
				<a class="nav-link smallmenulink" href="#"><?= _('My Observe');?></a>
		<?php } ?>
		</ul>
	</div>
	<div class="col text-right" id="ObserveHelp">
		<a href="./?page=features" target="_blank" title="<?= _('See how things are done ...');?>"><i class="far fa-question-circle"></i></a>
		<!--<a href="./help/<?= explode(".",$_SESSION["locale"])[0];?>-ObserveAdministratorManual.pdf" target="_blank" title="<?= _('Download the manual ...');?>"><i class="far fa-question-circle"></i></a>-->
	</div>
</nav>
