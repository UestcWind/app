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

class SDCToolKit{
	
	var $sname;
	var $url;
	var $sname_list;

	//构造函数，用于实例化一个连接对象，但此时并不实际
	function __construct($sname){
		if($_POST["op"]<>"refreshServerMemory" and $_POST["op"]<>"getAllServersList"){
			if($sname<>""){
				$list=require("SDCServerlist.php");
				if($list[$sname]<>""){
					$this->sname=$sname;
					$this->url=$list[$sname];
				}
			}
		}else{
			$this->sname_list=require("SDCServerlist.php");
		}
	}

	
	//具体操作函数
	
	function setupIndexDB(){
		return $this->req4conf("setupDB");
	}
	
	//创建索引
	function createIndex($server_name,$read_host,$write_host,$read_switch,$write_switch,$table_count){
		return $this->req4conf("createIndex",array("server_name"=>$server_name,"read_host"=>$read_host,"write_host"=>$write_host,"read_switch"=>$read_switch,"write_switch"=>$write_switch,"table_count"=>$table_count));
	}
	
	//获得sdc服务器的配置信息
	function getServerInfomation(){
		return $this->req4conf("getServerInfomation");
	}
	
	//$source值为hdd或memory，指定列出储存在硬盘或内存表中的列表
	function listIndex($source){
		return $this->req4conf("listIndex",array("source"=>$source));
	}
	
	//$server_name指定删除的MYSQL服务器索引的server_name
	function deleteIndex($server_name){
		return $this->req4conf("deleteIndex",array("server_name"=>$server_name));
	}
	
	function getIndexDetail($server_name){
		return $this->req4conf("getIndexDetail",array("server_name"=>$server_name));
	}
	
	//method为read和write，value为true和false
	function turnSwitch($server_name,$method,$value){
		return $this->req4conf("turnSwitch",array("server_name"=>$server_name,"method"=>$method,"value"=>$value));
	}
	
	function createNewTable($server_name){
		return $this->req4conf("createNewTable",array("server_name"=>$server_name));
	}
	
	function getAllServersList(){
		$sname_array=$this->sname_list;

		$count=0;
		foreach($sname_array as $key=>$value){
			$return[$count++]=$key;
		}
		
		return array("s"=>"1","count"=>$count,"data"=>$return);
	}
	
	//UDDM模式下的操作
	
	function createItemUDDM($unique_name,$value_array){
		//在UDDM模式下创建一个条目，你需要提供unique_name和一个包含所需字段数据的数组。
		return $this->req4normal("createItemUDDM",$value_array,$unique_name);
	}
	
	function removeValueUDDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"][i]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)
		//$a["modify_value"][i][j]="xxx";    指定需要push的字段值(可以设置多个字段)
		//其中i表示检索字段计数，j表示修改字段计数
		return $this->req4normal("removeValueUDDM",$value_array);
	}
	
	function pushValueUDDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"][i]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)
		//$a["modify_value"][i][j]="xxx";    指定需要push的字段值(可以设置多个字段)
		//其中i表示检索字段计数，j表示修改字段计数
		return $this->req4normal("pushValueUDDM",$value_array);
	}
	
	function updateItemsUDDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"][i]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)
		//$a["modify_value"][i][j]="xxx";    指定需要修改的字段值(可以设置多个字段)
		//其中i表示检索字段计数，j表示修改字段计数
		return $this->req4normal("updateItemsUDDM",$value_array);
	}
	
	function deleteItemsUDDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		return $this->req4normal("deleteItemsUDDM",$value_array);
	}
	
	function readItemsUDDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		return $this->req4normal("readItemsUDDM",$value_array);
	}
	
	//RDM模式下的操作
	
	function createItemRDM($value_array){
		//在RDM模式下创建一个条目，你需要提供unique_name和一个包含所需字段数据的数组。
		return $this->req4normal("createItemRDM",$value_array);
	}
	
	function updateItemsRDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"][i]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)
		//$a["modify_value"][i][j]="xxx";    指定需要修改的字段值(可以设置多个字段)
		//其中i表示检索字段计数，j表示修改字段计数
		return $this->req4normal("updateItemsRDM",$value_array);
	}
	
	function deleteItemsRDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		return $this->req4normal("deleteItemsRDM",$value_array);
	}
	
	function readItemsRDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		return $this->req4normal("readItemsRDM",$value_array);
	}

	function removeValueRDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"][i]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)
		//$a["modify_value"][i][j]="xxx";    指定需要push的字段值(可以设置多个字段)
		//其中i表示检索字段计数，j表示修改字段计数
		return $this->req4normal("removeValueRDM",$value_array);
	}

	function pushValueRDM($value_array){
		//在此操作下$value数组的书写格式应该是：
		//$a["key"][i]="xxx";   指定检索的字段名
		//$a["list"][i]="xxx";    指定检索的字段值
		//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)
		//$a["modify_value"][i][j]="xxx";    指定需要push的字段值(可以设置多个字段)
		//其中i表示检索字段计数，j表示修改字段计数
		return $this->req4normal("pushValueRDM",$value_array);
	}
	
	//对SDC-SERVER进行配置的操作:
	protected function checkInitStatus(){
		if(!$this->sname or !$this->url) return false;
			return true;
	}
	
	//对所有SDCServerlist.php中的server进行缓存初始化：
	function refreshServerMemory($server_name){
		//server_name不填代表对所有server进行初始化
		$list=require("SDCServerlist.php");

		if($server_name==""){
			foreach($list as $key=>$value){
				$this->req4conf("refreshServerMemory",NULL,$key,$value);
			}
			return array("s"=>"1");
		}else{
			if($list[$server_name]=="") return array("s"=>"0","wrong sname");
			$this->req4conf("refreshServerMemory",NULL,$server_name,$list[$server_name]);
			return array("s"=>"1");
		}
	}
	
	
	//为createItemUDDM、updateItemsUDDM、deleteItemsUDDM、readItemsUDDM（及RDM模式下的相同函数）提供的请求发送函数。
	protected function req4normal($op,$value_array = NULL,$unique_name = NULL) {
		
		if ($this->sname=="" or $this->url=="") return array("s"=>"0","r"=>"wrong sname");
		
		if($op=="") return array("s"=>"0","r"=>"undefined option");
		
		$url_string=$this->url."/fore.php?op=".$op;
		
		$post_string="";
		
		if($value_array)
			$post_string.="&value_string=".urlencode(base64_encode(json_encode($value_array)));
			
			
		if($unique_name)
			$post_string.="&unique_name=".urlencode($unique_name);
			
		$post_string=trim($post_string,"&");
			
		return $this->rstr_decode($this->reqCore($url_string,$post_string));
		
	}

	//为setupDB、createIndex、listIndex、deleteIndex、getIndexDetail、turnSwitch提供的请求发送函数。
	protected function req4conf($op,$value_array,$sname,$url) {
		
		if($sname=="" or $url==""){
			$url=$this->url;
			$sname=$this->sname;
		}

		if($op=="") return array("s"=>"0","r"=>"undefined option");
		
		$url_string=$url."/fore.php?op=".$op;
		$post_string="";
		
		foreach($value_array as $key=>$value)
			$post_string.="&".$key."=".urlencode($value);
			
		$post_string=trim($post_string,"&");
			
		return $this->rstr_decode($this->reqCore($url_string,$post_string));
		
	}

	protected function reqCore($url,$post_string){
		$ch = curl_init();
		$timeout = 30;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$file_contents = curl_exec($ch);
		curl_close($ch);
		return $file_contents;
	}
	
	protected function rstr_decode($rstr){//返回值解码
		$return_str=json_decode($rstr,true);
		return $return_str;
	}
}
?>