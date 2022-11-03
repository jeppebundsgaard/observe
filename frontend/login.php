<?php
$relative="../";
include_once($relative."/settings/conf.php");

$logintype=$_POST["logintype"];
if ($logintype!="login" and !filter_var($_POST["inputEmail"], FILTER_VALIDATE_EMAIL)) $warning=_("Not a valid e-mail address");
else {
	$email=strtolower(filter_var($_POST["inputEmail"], FILTER_VALIDATE_EMAIL));
	$username=$_POST["inputEmail"];
	if($logintype=="signup") {
		$q='select * from users where email LIKE "'.$email.'"';
		$res=$mysqli->query($q);
		$r=$res->fetch_assoc();
		if($r) $warning=_("E-mail already registered");
		else {
			$password=md5($_POST["inputPassword"]);
			$q='insert into users (email,password) values ("'.$email.'","'.$password.'")';
			$res=$mysqli->query($q);
			$_SESSION["user_id"]=$mysqli->insert_id;
			$_SESSION["realuser_id"]=$_SESSION["user_id"];
			$welcome=_("Welcome to Observe!");
		}
		$log.=$q;
	}
	elseif($logintype=="login") {
		$password=md5($_POST["inputPassword"]);
		$log.=$_POST["newpass"];
		if($_POST["newpass"]) {
			$file=$relative."newpass/".$_POST["newpass"];
			$log.=$file;
			if(file_exists($file)) {
				$email=file_get_contents($file);
				unlink($file);
				$q='update users set password="'.$password.'" where email LIKE "'.$email.'"';
				$res=$mysqli->query($q);
			}
		}
		$q='select * from users where (email LIKE "'.$email.'" or username LIKE "'.$mysqli->real_escape_string($username).'") and password="'.$password.'"';
		$res=$mysqli->query($q);
		$r=$res->fetch_assoc();
		if($r) {
			$_SESSION["realuser_id"]=$r["user_id"];
			$_SESSION["permissions"]=$r["permissions"];
			if($r["org_id"]>0) {
				$r["user_id"]=$r["org_id"];
				$_SESSION["orgMember"]=$r["org_id"];
			}
			$_SESSION["user_id"]=$r["user_id"];
			$welcome=_("Welcome back to Observe!");
		}
		else $warning=_("E-mail or password was wrong");
	}
	if($welcome and $_POST["rememberMe"]=="true") {
		setcookie("rememberMe","true",time()+60*60*24*365,"/"); //Let Observe remember me one more year ...
	}
	else setcookie("rememberMe","dont",0,"/");
#	$log.=print_r($_COOKIE,true);
	
}
echo json_encode(array("log"=>$log,"warning"=>$warning,"user_id"=>$_SESSION["user_id"],"welcome"=>$welcome,"relogin"=>$_POST["relogin"]));
