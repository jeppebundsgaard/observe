<?php
$res=array();
#$res["log"]=print_r($_FILES,true);

if($_FILES["data"]["tmp_name"]) {
	$filename="./img/obsschemes/".$_POST["obsid"].".png";
	copy($_FILES["data"]["tmp_name"],".".$filename);
	$res["src"]=$filename;
} else $res["warning"]=_("File not uploaded");
echo(json_encode($res));
