<?php
// Base URL
$globals=array(
"baseurl"=>"baseurl.dk"
);
foreach ($globals as $key => $value) {
    $GLOBALS[$key] = $value;
}
//////////////////////////////
// Leave these as they are ...
//////////////////////////////
session_start(array('cookie_lifetime' => 60*60*24*365)); //Let Observe remember me one more year ...);
// ini_set("display_errors","true");
if(!$relative) $relative="./";
$systemdirs=array(
	"settings"=>$relative."settings/",
	"templates"=>$relative."templates/",
	"frontend"=>$relative."frontend/",
	"functions"=>$relative."functions/",
	"backend"=>$relative."backend/",
	"img"=>$relative."img/",
	"js"=>$relative."js/",
	"pages"=>$relative."pages/"
);
include_once($systemdirs["functions"]."database.php");
include_once($systemdirs["functions"]."setlang.php");
list($locale,$lang)=set_lang($relative);
