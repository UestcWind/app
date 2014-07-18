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

class ConnectLAN{
	function executeQueryLAN(){
		$op=$_GET["op"];
		switch($op){
			//初始化操作，创建索引数据表
			case "setupDB":
				$status=new basicSetup();
				$return=$status->createDB();
				break;
			//缓存操作，
			case "emptyServerMemory":
				$return=serviceConf::emptyServerMemory();
				break;
			case "refreshServerMemory":
				$return=serviceConf::refreshServerMemory();
				break;
			case "loadServerMemory":
				$return=serviceConf::loadServerMemory();
				break;
			//MYSQL服务器索引操作
			case "createIndex":
				$return=serviceConf::createIndex($_POST["server_name"],$_POST["read_host"],$_POST["write_host"],$_POST["read_switch"],$_POST["write_switch"],$_POST["table_count"]);
				break;
			case "listIndex":
				$return=serviceConf::listIndex($_POST["source"]);
				break;
			case "deleteIndex":
				$return=serviceConf::deleteIndex($_POST["server_name"]);
				break;
			case "getIndexDetail":
				$return=serviceConf::getIndexDetail($_POST["server_name"]);
				break;
			case "turnSwitch":
				$return=serviceConf::turnSwitch($_POST["server_name"],$_POST["method"],$_POST["value"]);
				break;
			case "getServerInfomation":
				$return=serviceConf::getServerInfomation();
				break;
				
			//创建新的数据表
			case "createNewTable":
				$return=serviceConf::createNewTable($_POST["server_name"]);
				break;
				
			//UDDM下的操作,其中value_string均用json和base64编码：
			case "createItemUDDM":
				$return=serviceConf::createItemUDDM($_POST["unique_name"],json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "reomoveValueUDDM":
				$return=serviceConf::executeQueryUDDM("removeValue",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "pushValueUDDM":
				$return=serviceConf::executeQueryUDDM("pushValue",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "updateItemsUDDM":
				$return=serviceConf::executeQueryUDDM("updateItems",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "deleteItemsUDDM":
				$return=serviceConf::executeQueryUDDM("deleteItems",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "readItemsUDDM":
				$return=serviceConf::executeQueryUDDM("readItems",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			
			//RDM下的操作,其中value_string均用json和base64编码：
			case "createItemRDM":
				$return=serviceConf::createItemRDM(json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "removeValueRDM":
				$return=serviceConf::executeQueryRDM("removeValue",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "pushValueRDM":
				$return=serviceConf::executeQueryRDM("pushValue",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "updateItemsRDM":
				$return=serviceConf::executeQueryRDM("updateItems",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "deleteItemsRDM":
				$return=serviceConf::executeQueryRDM("deleteItems",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			case "readItemsRDM":
				$return=serviceConf::executeQueryRDM("readItems",json_decode(base64_decode($_POST["value_string"]),true));
				break;
			
			default:
				$return=array("s"=>"0","r"=>"undefined option");
		}
		return $return;
	}
}
?>