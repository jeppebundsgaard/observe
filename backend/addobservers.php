<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$relative="../";
include_once($relative."/settings/conf.php");
include_once($systemdirs["backend"]."checklogin.php");

function randomstring($len) {
	$keyspace = '-_~.0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$s="";
	for($i=0;$i<$len;$i++) {
		$s.=substr($keyspace,rand(0,strlen($keyspace)-1),1);
	}
	return $s;
}
$res=array();
$col=$_POST["col"];
array_walk($_POST["newobservers"],function($v) {global $mysqli; $mysqli->real_escape_string($v);});
$newobservers=$_POST["newobservers"];
$users='"'.implode('","',$_POST["newobservers"]).'"';

$q='select user_id,username,email from users where username in ('.$users.') or email in ('.$users.')';
$result=$mysqli->query($q);
$addobservers=array();
$accepted=array();
while($r=$result->fetch_assoc()) {
	$addobservers[]="(".$_POST["study_id"].",".$r["user_id"].")";
	$accepted[($r["username"]?$r["username"]:$r["email"])]=$r["user_id"];
	$newobservers=array_diff($newobservers,array($r["username"],$r["email"]));
}
if(!empty($addobservers)) {
	$q='insert into observers (study_id,user_id) values '.implode(",",$addobservers).' on duplicate key update user_id=user_id';
	$result=$mysqli->query($q);
}
if(!empty($newobservers)) {
	$q='select s.name,email,u.name as userfullname from studies s left join users u on owner=u.user_id where s.id='.$_POST["study_id"];
	$result=$mysqli->query($q);
	$r=$result->fetch_assoc();
	$studyname=$r["name"];	// Load Composer's autoloader
	
	require $relative.'vendor/autoload.php';

	$secrets=explode("\n",file_get_contents($relative."settings/.htmail"));
	// Instantiation and passing `true` enables exceptions
	$mail = new PHPMailer(true);
		$hostport=explode(":",$secrets[0]);
		$host=$hostport[0];
		$port=$hostport[1]?$hostport[1]:465; 

	
	//Server settings
	$mail->SMTPDebug = 0;                                       // Enable verbose debug output
	$mail->Debugoutput = function($str, $level) {global $warnings; $warnings[]="<br>message: $str";};
	$mail->isSendmail();#isSMTP();                                            // Set mailer to use SMTP
	$mail->CharSet = 'utf-8';
	$mail->Host       = $host;							  // Specify main and backup SMTP servers
	$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
	$mail->Username   = $secrets[1];                     // SMTP username
	$mail->Password   = $secrets[2];                               // SMTP password
	$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
	$mail->Port       = $port;                                    // TCP port to connect to
	$mail->AddReplyTo($r["email"],($r["userfullname"]?$r["userfullname"]:$r["email"]));
	$mail->setFrom('admin@observe.education', _('Administrator at observe.education'));



	
	
	foreach($newobservers as $newobserver) {
		$mailproblem="";
		if(filter_var($newobserver, FILTER_VALIDATE_EMAIL)){
			try {	
				//Recipients
				$mail->addAddress($newobserver);               // Name is optional

				// Content
				$registrationcode=randomstring(24);
				$subject=_('Invitation to join Observe.education as an observer');
				$message=
	'<html>
		<head>
			<title>'.$subject.'</title>
		</head>
		<body>
			'.sprintf(_('Hello!<br><br>You are invited by %1$s to become an observer in the study: %2$s on <a href="https://Observe.education">Observe.education</a>.<br><br>
			To accept the invitation click <a href="%3$s">here</a><br><br>Or you can copy this URL and paste it into your browser: %3$s.<br><br>
			We look forward to see you on Observe.education.'),($r["userfullname"]?$r["userfullname"]:$r["email"]),$studyname,'https://observe.education/?acceptinvite='.$newobserver.'&amp;registrationcode='.$registrationcode).'
		</body>
	</html>';
			
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = $subject;
				$mail->Body    = $message;

				$mail->send();
				$accepted[$newobserver]=0;
			} catch (Exception $e) {
				$mailproblem=sprintf(_('Error when inviting %1$s. Mailer Error: %2$s'),$newobserver,$mail->ErrorInfo);
			}
			
			
		} else $mailproblem=sprintf(_("%s is not a valid e-mail address, so it has not been invited."),$newobserver);
		if(!$mailproblem) $invited[]="(".$_POST["study_id"].",'".$newobserver."','".$registrationcode."')";
		else $warnings[]=$mailproblem;
	}
	if(!empty($invited)) {
		$q='insert into invited_users (study_id,email,registrationcode) values '.implode(",",$invited).' on duplicate key update registrationcode=values(registrationcode)';
		$result=$mysqli->query($q);
	}
}
if(!empty($warnings)) $res["warning"]=implode("<br>",$warnings);
$res["log"].=$q;
$res["accepted"]=$accepted;
echo json_encode($res);
