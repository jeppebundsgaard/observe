<!DOCTYPE html>
<html lang="<?= $lang; ?>">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Observe.education allows researchers and practitioners to systematically observe teaching and learning practices.">
    <meta name="author" content="Jeppe Bundsgaard">
    <link rel="icon" href="favicon.png">

    <title>Observe.Education</title>
    
	<link rel="stylesheet" href="./vendor/twbs/bootstrap/dist/css/bootstrap.min.css">

    <!-- Add icon library -->
	<link rel="stylesheet" href="./vendor/components/font-awesome/css/all.min.css">
<!-- styles for Observe -->
    
    <link rel="stylesheet" href="./css/basesystem.css" id="basesystemstyles">
    <link rel="stylesheet" href="./css/observe.css">
    <link rel="stylesheet" href="./css/basesystemadmin.css">
    <link rel="stylesheet" href="./css/vendor/bootstrap4-toggle.min.css">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.js"></script>
	
	<!-- Adobe Acumin Font	 -->
	<link rel="stylesheet" href="https://use.typekit.net/ltl7ebe.css">
</head>

<body  class="pb-0 pb-lg-5"> <!-- Removed: d-flex flex-column -->
	<!-- Header --><!-- add? fixed-top -->
	<nav class="navbar navbar-expand-md justify-content-between site-header d-print-none">
		<div class="col-xl-4 d-none d-xl-block">
		</div>
		<div class="col-4 text-center">
			<h1 class="display-4 text-dark">OBSERVE.<span class="observefont">education</span></h1>
		</div>
		<div class="col-4 d-lg-none">
			<button class="navbar-toggler float-right navbar-light" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon "></span>
			</button>
		</div>
		<div class="collapse navbar-collapse col-md-4 col-12 justify-content-end " id="navbarToggler">
				<ul class="navbar-nav justify-content-end">
					<li class="nav-item active d-flex justify-content-end">
						<a class="nav-link menulink" href="./"><?= strtolower(_('Frontpage'));?></a> <span class="menubar d-none d-lg-inline">&vert;</span>
					</li>
					<li class="nav-item active d-flex justify-content-end">
						<a class="nav-link menulink" href="./?page=features"><?= strtolower(_('Features'));?></a> <span class="menubar d-none d-lg-inline">&vert;</span>
					</li>
					<?php 
					$orgpagesdir=$systemdirs["pages"]."org/".$_SESSION["locale"]."/";
					if(file_exists($orgpagesdir)) {
						if ($handle = opendir($orgpagesdir)) {
							while (false !== ($e = readdir($handle))) {
								if ($e != "." && $e != "..") {
									$f=str_replace(".html","",$e);
								?>
								<li class="nav-item active d-flex justify-content-end">
									<a class="nav-link menulink" href="./?page=<?= $f;?>"><?= $f;?></a>
								</li>
								<?php
								}
							}
							closedir($handle);
						}
					}
					?>
					<?php if(!$_SESSION["user_id"]) {?>
					<li class="nav-item active d-flex justify-content-end">
						<a class="nav-link menulink observefont display-5" href="./?backend=1"><?= strtolower(_('Log in'));?></a>
					</li>
					<?php } else { ?>
					<li class="nav-item active d-flex justify-content-end" id="showMyUser">
						<a class="nav-link menulink observefont display-5" href="#"><?= strtolower(_('My User'));?></a>
					</li>
					<?php }?>
				</ul>
			
		</div>
	</nav>
			<div  id="adminmenu">
			<?= ($user_id?get_template("adminmenu")["template"]:""); ?>
			</div>
	<main role="main" class="flex-shrink-0 h-100 mb-3">
		<div class="h-100"  id="contentdiv">
