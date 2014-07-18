<?php
header("Content-Type:text/html;charset=gb2312");
//This page is the back-end of the SOL.
//-------------------------------------
//'SOL PROJECT' IS THE ABBREVIATION OF 'STUDENT ONLINE PROJECT'.
//NOBODY SHOULD READ\COPY\SELL THE CODES WITHOUT DEVELOPER'S PERMISSION IN ANY WAY.
//THIS PAGE IS SPECIALLY RUNNING FOR SOL
//DEVELOPER INFORMATION:
//DEVELOPER	: Meng Zelin.
//E-MAIL	: SMARTMZL@QQ.COM
//OWNWAY GROUP (C) 2013
//HTTP://WWW.OWNWAY.NET

//设置控制开关
define("CONTROL_SWITCH","true");

//设置用户名
define("ADMIN_USERNAME","smartmzl");

//设置管理用户密码（两次MD5加密）
define("ADMIN_PASSWORD","c19021b15d9f245ce9c67e0371c267e1");

//基本函数
function rstr_encode(array $array){//返回值编码
	$return_str=isset($_GET['callback'])?$_GET['callback']."(".json_encode($array).")":json_encode($array);
	return $return_str;
}

include_once "SDC.sdk.php";

$sname=$_POST["sname"];
$username=$_POST["username"];
$password=$_POST["password"];

if(CONTROL_SWITCH<>"true"){
	die(rstr_encode(array("s"=>"0","r"=>"control switch is shut down")));
}

if($username<>constant("ADMIN_USERNAME") or md5(md5($password))<>constant("ADMIN_PASSWORD"))
	die(rstr_encode(array("s"=>"0","r"=>"user identify failed")));

$analyze_query=new SDCToolKit($sname);

$op=$_POST["op"];

switch($op){
	case "setupIndexDB":
		$return=$analyze_query->setupIndexDB();
		break;
	case "listIndex":
		$return=$analyze_query->listIndex($_POST["source"]);
		break;
	case "createIndex":
		$return=$analyze_query->createIndex($_POST["server_name"],$_POST["read_host"],$_POST["write_host"],$_POST["read_switch"],$_POST["write_switch"],$_POST["table_count"]);
		break;
	case "deleteIndex":
		$return=$analyze_query->deleteIndex($_POST["server_name"]);
		break;
	case "getIndexDetail":
		$return=$analyze_query->getIndexDetail($_POST["server_name"]);
		break;
	case "turnSwitch":
		$return=$analyze_query->turnSwitch($_POST["server_name"],$_POST["method"],$_POST["value"]);
		break;
	case "createNewTable":
		$return=$analyze_query->createNewTable($_POST["server_name"]);
		break;
	case "reloadAllIndexCache":
		$return=$analyze_query->refreshServerMemory($_POST["server_name"]);
		break;
	case "getAllServersList":
		$return=$analyze_query->getAllServersList();
		break;
	case "getServerInfomation":
		$return=$analyze_query->getServerInfomation();
		break;
	default:
		$return=array("s"=>"0","r"=>"undefined operation");
}

die(rstr_encode($return));
?>