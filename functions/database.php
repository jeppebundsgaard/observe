<?php
if(!$mysqli) {
# .htdatabase has the following form: 
# localhost,user,password,database
	$database=trim(file_get_contents($systemdirs["settings"].".htdatabase"));
	$db=explode(",",$database);
	// print_r($db);
	$mysqli = new mysqli($db[0],$db[1],$db[2],$db[3]);
	if ($mysqli->connect_error) {
		echo "Failed to connect to MySQL: " . $mysqli->connect_error;
		exit;
	}
	$mysqli->set_charset("utf8");

	function mysqlerror($q) {
		return "\nError in query: ".$q;
	}
	function checkpermissions() {
		$users=func_get_args();
		return (in_array($_SESSION["permissions"],$users));
	}
	function mysql_json_encode($obj) {
		if(is_array($obj)) {
			$arr=array();
			$isassoc=(array_keys($obj) !== range(0, count($obj) - 1));
			foreach($obj as $key=>$val) {if($isassoc) $arr[]='"'.$key.'"';$arr[]=mysql_json_encode($val);}
			$json=($isassoc?'JSON_OBJECT(':'JSON_ARRAY(').implode(',',$arr).')';
		} else {
			global $mysqli;
			$json='"'.$mysqli->real_escape_string($obj).'"';
		}
		return $json;
	}
}
