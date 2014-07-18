<?php
require_once "dbconnect.cls.php";
//$dblink=dbconnect::ConnectDB('index');
$dblink=dbconnect::basicPHPFileRead();
var_dump($dblink);
var_dump(json_decode($dblink,true));
echo "<br />";
$str='{"index_db_host":"localhost","index_db_port":"","index_db_user":"root","index_db_pwd":"925","index_db_dbname":"act_index"}';
//$str='{"Organisation": "Équipe de Documentation PHP"}';
//if(json_decode($str,true)==0) echo '$str是json数据';else echo '$str不是json数据';
//var_dump(json_decode($str,true));
var_dump(json_decode($str,true)); 
?>