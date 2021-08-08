<div class="container">
	<div class="row d-flex mt-4 justify-content-center">
		<div class="col">
<?php 
	$q='select * from invited_users where email LIKE "'.$_GET["acceptinvite"].'" and registrationcode LIKE "'.$_GET["registrationcode"].'"';
	$result=$mysqli->query($q);
	if($result->num_rows==0) { ?>
			<h3 class=""><?= _('Error when fetching invitation');?></h3>
			<?=_('We cannot find the invitation in our system. The reson is probably one of the following.');?>
			<ul>
				<li><?=_('Maybe the registration has been renewed. Then you will have received a new invitaion e-mail. Click on the link in that e-mail.');?></li>
				<li><?=_('Or you have missed some of the registration code, when you copied the URL in the e-mail. Make sure to copy all of it.');?></li>
				<li><?=_('Or the invitaion has been withdrawn. Please ask the study manager to invite you again.');?></li>
			</ul>
			<?php } else {
			$r=$result->fetch_assoc();

			?>
			<p class="text-center"><?= _('Welcome to observe.education!');?></h3>
			<h3 class="text-center"><?= _('Create a user');?></h3>
			<div class="d-flex justify-content-center">
				<div class="card card-block logindiv ">
					<div class="form-row">
						<p><?=_('Email address').": ".$_GET["acceptinvite"];?></p>
					</div>
					<div class="form-row">
						<label for="name"><?= _('Your Name');?></label>
						<input type="text" id="name" class="form-control " placeholder="<?=_('Your Name');?>" required>
					</div>
					<div class="form-row">
						<label for="username"><?= _('Username');?></label>
						<input type="text" id="username" class="form-control " placeholder="<?=_('Username');?>" required value="<?= $_GET["acceptinvite"];?>">
						<small class="text-muted"><?= _('Your username can be your e-mail address or a name of your own choice.');?></small>
					</div>
					<div class="form-row">
						<label for="password"><?= _('Password');?></label>
						<input type="password" id="password" class="form-control " placeholder="<?=_('Password');?>" required>
						<input type="hidden" id="email" value="<?= $_GET["acceptinvite"];?>">
						<input type="hidden" id="registrationcode" value="<?= $_GET["registrationcode"];?>">
						<button class="btn btn-observe float-right mt-2" id="newinviteduser" type="button"><?= _('Create user');?></button>				
					</div>
				</div>

			</div>
<!-- 		< ?php }  ?> -->
<?php }?>
		</div>
	</div>
</div>
