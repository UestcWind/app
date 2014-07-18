<?php
include_once "../../sdk/inkyun.sdk.php";
$dbLink=mysql_connect('localhost','root','925');
if(!$dbLink)	return array('s'=>'0','r'=>mysql_error($dbLink));
mysql_select_db('act_user',$dbLink);
$inkyunInstance=new inkyunOpen($_COOKIE['INKYUNPLATFORM_USERNAME'],$_COOKIE['INKYUNPLATFORM_USERTOKEN'],"web");
$query=mysql_query("SELECT * FROM FD0 WHERE sdcname='".$inkyunInstance->user_name."'");
if(!$query){
	$query=mysql_query("INSERT INTO FD0(sdcname,UserName,UserToken) VALUES('".$inkyunInstance->user_name."','".$inkyunInstance->user_name."','".time()."')",$dbLink);
	if($query){
		echo  "活动应用注册成功";
		header("Location: ../public/app.html");
	}else{
		echo json_encode(array('s'=>'0','r'=>'插入失败:'.mysql_error($dbLink)));
	}
}else{
	$query=mysql_query("UPDATE FD0 SET UserToken='".time()."'");
	if(!$query){
		die("修改token失败:".mysql_error($dbLink));
	}else{ 	
		echo  "登录成功";
		header("Location: ../public/app.html");
	}
}

?>