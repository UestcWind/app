<?php
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

class dbconnect{
	static function ConnectDB($server_type,$create=""){//server type contents app/user/basic/unit
		if($server_type=="index"){
			$conf_str=dbconnect::basicPHPFileRead();
			
			$server_port=$conf_str[$server_type."_db_port"]?":".$conf_str[$server_type."_db_port"]:"";
			$db_link = mysql_connect($conf_str[$server_type."_db_host"].$server_port,$conf_str[$server_type."_db_user"],$conf_str[$server_type."_db_pwd"]);
			mysql_query("set names utf8");
			
			if($create=="create"){
				mysql_query("create database if not exists ".$conf_str[$server_type."_db_dbname"]." DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci",$db_link);
			}
			
			if(mysql_select_db($conf_str[$server_type."_db_dbname"],$db_link)) return $db_link;
			else {
				echo "链接失败".mysql_error().$conf_str[$server_type."_db_host"].$server_port,$conf_str[$server_type."_db_user"].$conf_str[$server_type."_db_pwd"].$conf_str[$server_type."_db_dbname"];
				return false;
			}
		}else{
			return false;
		}
	}
	static function basicPHPFileRead(){
		$line=require "basic.php";
		return $line;
	}
}
?>