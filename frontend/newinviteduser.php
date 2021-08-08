<?php
$relative="../";
include_once($relative."/settings/conf.php");

$res=array();
if($_POST["email"]) {
	$q='select from users where email LIKE "'.$_POST["email"].'" or username LIKE "'.$_POST["username"].'"';
	$result=$mysqli->query($q);
	if($result->num_rows>0) {
		$r=$result->fetch_assoc();
		$res["warning"]=($r["email"]==$_POST["email"]?_('You have already registered a user with this e-mail. Please just log in.'):_('The username is not available. Please select a new one.'));
	}
	else {
		$q='insert into users (username,name,email,password) values ("'.$mysqli->real_escape_string($_POST["username"]).'","'.$mysqli->real_escape_string($_POST["name"]).'","'.$mysqli->real_escape_string($_POST["email"]).'","'.$mysqli->real_escape_string($_POST["password"]).'")';
		$result=$mysqli->query($q);
		$_SESSION["realuser_id"]=$_SESSION["user_id"]=$res["user_id"]=$mysqli->insert_id;
		$q='select * from invited_users where email LIKE "'.$_POST["email"].'" and registrationcode LIKE "'.$_POST["registrationcode"].'"';
		$result=$mysqli->query($q);
		if($result->num_rows>0) {
			$q='insert into observers (study_id,user_id) select study_id,'.$res["user_id"].' from invited_users where email LIKE "'.$_POST["email"].'"';
			$result=$mysqli->query($q);
			$q='delete from invited_users where email LIKE "'.$_POST["email"].'"';
			$result=$mysqli->query($q);
		}
	}
}

echo json_encode($res);
