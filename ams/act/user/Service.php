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

//本代码是SQL分布式核心开发包（SDC SDK）的核心代码，版权属开发者孟则霖(smartmzl@qq.com)所有。
//集群功能目前提供两种分布式模式，分别是“随机分配模式(Random Distributive Mode,RDM)”和“自定义分配模式(User Defined Distributive Mode,UDDM)”

//    ***随机分配模式***
//    初次运行时程序将创建ServerHDD硬盘表用于储存内容储存数据库(CSDB)的详细信息，每一次初始化或ServerHDD更新
//    时，都会将ServerHDD中的数据同步到ServerIndex缓存表(储存在可负载均衡的SQL服务器)中，以便于高速查找；每一
//    个CSDB中含有多个内容储存数据表(CSTB)，在一个CSTB中都包含带有AUTO_INCREMENT属性的ID，因而每一条记录都含
//    有在所在CSTB中唯一的ID(TABLEID)。当外部程序通过OMSDF插入记录时，OMSDF会返回包含CSDB名、CSTB名和
//    TABLEID的UniqueID，例如：DB1-3-28682。当外部程序需要再次访问该记录时需要提供完整的UniqueID。
//    插入记录过程：OMSDF识别出ServerIndex中可写的服务器列表，随机抽取出一个服务器并建立连接，选择序列最大的
//    CSTB，并插入数据，返回UniqueID。
//    固定模式中CSTB中的最大记录数由MAX_TABLE_CONTENT指定，在一个CSTB中的记录数超过MAX_TABLE_CONTENT后，程
//    序将顺次建立一个新的CSTB。

//    ***自定义分配模式***
//    自定义模式中与ServerHDD和ServerIndex有关的内容与随机模式中相同，不同点是：
//    在Service.php所在目录下含有DistributiveRule.php文件，该文件中的Calculate()方法指定了分配规则。
//    在写入记录时程序调用CalCulate()方法，指定链接服务器名称和数据表序号，并链接到服务器插入记录。
//    在查找记录时程序同样调用CalCulate()方法，指定链接服务器名称和数据表序号，并连接到服务器读取数据。

//    ***改代码的安全性说明***
//    在使用该代码时应该注意将其放置于上层服务器所信任的内网中，而决不能将本段代码暴露在公网中。

include "basic_conf.php";

class serviceConf{
	
	static function getServerInfomation(){
		return array("s"=>"1","MAX_TABLE_CONTENT"=>MAX_TABLE_CONTENT,"DISTRIBUTIVE_MODE"=>DISTRIBUTIVE_MODE,"CSTB_MYSQL_STRING"=>CSTB_MYSQL_STRING,"CSTB_ENGINE"=>CSTB_ENGINE,"CSTB_PREFIX"=>CSTB_PREFIX);
	}
	
//以下代码是将ServerHDD中的索引同步到ServerIndex中
	
	//清空ServerIndex中的数据
	static function emptyServerMemory(){
		$dblink=dbconnect::ConnectDB("index");
		$res=mysql_query("TRUNCATE TABLE server_index",$dblink);
		if($res) return true; else return false;
	}
	
	//将ServerHDD中的数据同步至ServerIndex
	static function refillServerMemory(){ 
		$dblink=dbconnect::ConnectDB("index");
		$res=mysql_query("INSERT INTO server_index SELECT * FROM server_hhd",$dblink);
		if($res) return true; else return false;
	}
	
	//刷新ServerIndex
	static function refreshServerMemory(){ 
		if(self::emptyServerMemory()){
			$return=self::refillServerMemory();
		}
		if($return) return array("s"=>"1"); else return array("s"=>"0");
	}
	
	//初始化ServerIndex
	static function loadServerMemory(){ 
		$return=self::refillServerMemory();
		if($return) return array("s"=>"1"); else return array("s"=>"0");
	}
	
//以下代码是MYSQL服务器索引的相关操作

	static function checkDBStatus($dblink){
		$res=mysql_query("SHOW TABLES LIKE 'server_index'",$dblink);
		if(mysql_num_rows($res)<1) return array("s"=>"0","r"=>"index table is not exist");
			else return array("s"=>"1");
	}

	//删除某个MYSQL服务器索引
	static function deleteIndex($server_name){ 
		$dblink=dbconnect::ConnectDB("index");
		
		$dbcheck=self::checkDBStatus($dblink);
		if($dbcheck["s"]<>"1") return $dbcheck;
		
		if($server_name=="") return array("s"=>"0","r"=>"wrong peremeter");
		list($cnt)=mysql_fetch_row(mysql_query("SELECT count(*) FROM server_hhd WHERE server_name='".$server_name."'",$dblink));
		if($cnt<1) return array("s"=>"0","r"=>"server doesn't exist");
		$res=mysql_query("DELETE FROM server_hhd WHERE server_name='".$server_name."'",$dblink);
		if($res){self::refreshServerMemory();return array("s"=>"1");
			}else{ return array("s"=>"0","r"=>"mysql errno:".mysql_errno($dblink));}
	}
	
	//***read_host和write_host的写法***
	//    下面是一个实例：
	//    x.x.x.x:3306 root password database
	//    即IP地址和用户名和密码和数据库之间用空格分隔。
	
	//增加一个MYSQL服务器索引，索引增加成功之后会根据输入的table_count执行一下操作：
	//    若table_count=0，创建一个CSTB。
	//    若table_count>0，不执行创建CSTB操作。
	static function createIndex($server_name,$read_host,$write_host,$read_switch,$write_switch,$table_count){
		$dblink=dbconnect::ConnectDB("index");
		
		$dbcheck=self::checkDBStatus($dblink);
		if($dbcheck["s"]<>"1") return $dbcheck;
		
		if($server_name=="" or $read_host=="" or $write_host=="" or $read_switch=="" or $write_switch=="") return array("s"=>"0","r"=>"peremeter imcomplete");
		list($cnt)=mysql_fetch_row(mysql_query("SELECT count(*) FROM server_hhd WHERE server_name='".$server_name."'",$dblink));
		if($cnt>0) return array("s"=>"0","r"=>"server name exists");
		if(!(boolean)$write_switch) $write_switch=0; else $write_switch=1;
		if(!(boolean)$read_switch) $read_switch=0;else $read_switch=1;
		if(!$table_count || $table_count=="") $table_count=0;
		$res=mysql_query("INSERT INTO server_hhd VALUES('".$server_name."','".$read_host."','".$write_host."','".$read_switch."','".$write_switch."','".$table_count."')",$dblink);
		self::refreshServerMemory();
		if($res){
			if($table_count<1){
				$datalink=self::connectDataServer("write",$server_name,$dblink,"create");
				if(!$datalink) return array("s"=>"0","r"=>"failed to create sql connection");
				$res1=self::createNewDataTable($server_name,$datalink,$dblink);
				self::refreshServerMemory();
				if($res1["s"]=="1") return array("s"=>"1");
					else return $res1;
			}
			return array("s"=>"1");
		}else{ return array("s"=>"0","r"=>"mysql errno:".mysql_errno($res));}
	}
	
	//简单分析储存在Server_index中的write_host和read_host字符串，并输出数组。
	static function analyzeServerString($string){
		$string_=explode(" ",$string);
		if(count($string_)<>4){
			return false;
		}else{
			return array("host"=>$string_["0"],"username"=>$string_["1"],"password"=>$string_["2"],"database"=>$string_["3"]);
		}
	}
	
	//列出一个所有的MYSQL服务器索引
	static function listIndex($source){
		$dblink=dbconnect::ConnectDB("index");
		
		$dbcheck=self::checkDBStatus($dblink);
		if($dbcheck["s"]<>"1") return $dbcheck;
		
		if(strtolower($source)<>"hdd" and strtolower($source)<>"memory") return array("s"=>"0","r"=>"peremeter wrong");
		if(strtolower($source)=="hdd") $table="server_hhd"; else $table="server_index";
		$res=mysql_query("SELECT * FROM ".$table,$dblink);
		$i=0;
		while($res_=mysql_fetch_row($res))
			$return[$i++]=$res_;
		
		return array("s"=>"1","list"=>$return,"count"=>mysql_num_rows($res));
	}
	
	//更改某个MYSQL服务器索引的读/写开关。
	//method=write|read
	//value=true|false
	static function turnSwitch($server_name,$method,$value){
		$dblink=dbconnect::ConnectDB("index");
		
		$dbcheck=self::checkDBStatus($dblink);
		if($dbcheck["s"]<>"1") return $dbcheck;
		
		if(strtolower($method)=="read"){$method_="read_switch";}else if(strtolower($method)=="write"){$method_="write_switch";}
			else{ return array("s"=>"0","r"=>"peremeter wrong"); }
		if((boolean)$value){$value_=1;}else{$value_=0;}
		if($server_name=="") return array("s"=>"0","r"=>"peremeter wrong");
		list($cnt)=mysql_fetch_row(mysql_query("SELECT count(*) FROM server_hhd WHERE server_name='".$server_name."' LIMIT 1",$dblink));
		if($cnt<1) return array("s"=>"0","r"=>"server not found");
		
		$res=mysql_query("UPDATE server_hhd SET ".$method_."='".$value_."' WHERE server_name='".$server_name."'",$dblink);
		if($res){self::refreshServerMemory();return array("s"=>"1");
			}else{ return array("s"=>"0","r"=>"mysq errno:".mysql_errno($dblink));}
	}
	
	//返回某个MYSQL服务器索引的具体信息。
	static function getIndexDetail($server_name){
		$dblink=dbconnect::ConnectDB("index");
		$res=mysql_query("SELECT * FROM server_index WHERE server_name='".$server_name."' LIMIT 1",$dblink);
		if(mysql_num_rows($res)<1) return array("s"=>"0","r"=>"server not found");
		return array("s"=>"1","list"=>mysql_fetch_row($res));
	}
	
	//为指定MYSQL服务器按照顺序创建一个新的CSTB，该函数将调用createNewDataTable()方法。用于前端调用。
	static function createNewTable($server_name){
		$dblink=dbconnect::ConnectDB("index");
		
		$dbcheck=self::checkDBStatus($dblink);
		if($dbcheck["s"]<>"1") return $dbcheck;
		
		$datalink=self::connectDataServer("write",$server_name,$dblink);
		$res1=self::createNewDataTable($server_name,$datalink,$dblink);
		self::refreshServerMemory();
		if($res1["s"]=="1") return array("s"=>"1");
			else return $res1;
	}

//以下代码是配置基于MYSQL服务器连接的代码
	
	//为指定MYSQL服务器按照顺序创建一个新的CSTB。
	//需要传入连接到随机获取的可用MYSQL服务器的链接对象($datalink)和连接到ServerIndex数据表的MYSQL链接对象($dblink)。
	static function createNewDataTable($server_name,$datalink,$dblink){
		if(!$datalink) return array("s"=>"0","r"=>"database connect failed");
		list($table_count)=mysql_fetch_row(mysql_query("SELECT table_count FROM server_hhd WHERE server_name='".$server_name."'",$dblink));
		$new_table_count=$table_count+1;
		if(DISTRIBUTIVE_MODE=="UDDM")
			$create_string="CREATE TABLE ".CSTB_PREFIX.$table_count." (sdcname varchar(128), PRIMARY KEY (sdcname), ".CSTB_MYSQL_STRING.") ENGINE = ".CSTB_ENGINE.";";
		else if(DISTRIBUTIVE_MODE=="RDM")
			$create_string="CREATE TABLE ".CSTB_PREFIX.$table_count." (sdcpid int(11) AUTO_INCREMENT, PRIMARY KEY (sdcpid), ".CSTB_MYSQL_STRING.") ENGINE = ".CSTB_ENGINE.";";
		else return array("s"=>"0","r"=>"distributive mode is not defined");
		$create_table=mysql_query($create_string,$datalink);
		if(!$create_table){
			return array("s"=>"0","r"=>"mysql error number:".mysql_error());
		}else{
			$res=mysql_query("UPDATE server_hhd SET table_count='".$new_table_count."' WHERE server_name='".$server_name."'",$dblink); 	
			self::refreshServerMemory();
			return array("s"=>"1");
		}
	}
	
//以下是连接MYSQL服务器的代码
	
	//连接到某一个MYSQL服务器，需要传入连接到ServerIndex数据表的MYSQL链接对象($dblink)。
	//返回值是连接到随机获取的可用MYSQL服务器的链接对象($datalink)
	//method=read|write		指定连接服务器的种类为读还是写。
	//server_name			连接服务器的名称。
	static function connectDataServer($method,$server_name,$dblink,$create=""){
		
		if($server_name=="" or $method=="") return false;
		if($method=="write") $method_="write_host"; else if($method=="read") $method_="read_host";  else return false;
		list($res)=mysql_fetch_row(mysql_query("SELECT ".$method_." FROM server_index WHERE server_name='".$server_name."'",$dblink));
		
		$string=self::analyzeServerString($res);

		$datalink = mysql_connect($string["host"],$string["username"],$string["password"],true);
		
		if($create=="create"){
			mysql_query("create database if not exists ".$string["database"]." DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci",$datalink);
		}
		
		mysql_query("set names utf8",$datalink);
		if(mysql_select_db($string["database"],$datalink)) return $datalink;
			else return false;
	}
	
//以下是随机分配模式(RDM)的数据库连接代码。

	//通过ID获取数据时分析所在位置
	//RDM模式下，只有在进行非插入操作时才会进行位置分析。
	//你只用输入ID号，将会分析出所在MYSQL服务器和所在表
	static function analyzePositionRDM($id){
		
		if(DISTRIBUTIVE_MODE<>"RDM") return false;
		
		$id_=split("-",$id);
		if(count($id_)==3){
			return array("server"=>$id_["0"],"cstb"=>$id_["1"],"id"=>$id_["2"]);
		}else{
			return false;
		}
	}
	
	//获取某个服务器的最近的CSTB，该模式只在insert模式下使用。
	static function getLastTableRDM($server_name,$dblink){
		
		if(DISTRIBUTIVE_MODE<>"RDM") return false;
		
		list($res)=mysql_fetch_row(mysql_query("SELECT table_count FROM server_index WHERE server_name='".$server_name."'",$dblink));
		return $res-1;
	}

	//该函数的作用是在RDM下随机抽取可用MYSQL服务器，执行插入记录操作时，随机抽取可用MYSQL服务器，并返回MYSQ连接对象。
	//该函数需要调用connectDataServer()函数。
	//该函数需要传入连接到ServerIndex数据表的MYSQL链接对象($dblink)。
	//返回值是连接到随机获取的可用MYSQL服务器的链接对象($datalink)。
	//指定连接类型（method）是指定MYSQL请求类型，分为write(DELETE\UPDATE)，read(SELECT)，insert(INSERT)。
	static function connectServerRDM($method,$position,$dblink){
		
		if(DISTRIBUTIVE_MODE<>"RDM") return false;
		
		if($method=="read"){
			return self::connectDataServer("read",$position["server"],$dblink);
		}else if($method=="insert"){
			
			$res=mysql_query("SELECT server_name FROM server_index WHERE write_switch=1",$dblink);
			if(!$res) return false;
			$svr_count=mysql_num_rows($res);
			if($svr_count<1) return false;
			$rand=mt_rand(0,$svr_count-1);
			return mysql_result($res,$rand,"server_name");

		}else if($method=="write"){
			return self::connectDataServer("write",$position["server"],$dblink);
		}
		
	}
	
	//在RDM模式下创建一个条目，你需要提供unique_name和一个包含所需字段数据的数组。
	static function createItemRDM(array $value){
		
		if(DISTRIBUTIVE_MODE<>"RDM") return array("s"=>"0","r"=>"distributive mode dose not match");

		$dblink=dbconnect::ConnectDB("index");
		$rand_name=self::connectServerRDM("insert",NULL,$dblink);
		$datalink=self::connectDataServer("write",$rand_name,$dblink);
		

		
		$value_string="";
		foreach($value as $key=>$val){
			$value_string.="'".mysql_real_escape_string($val)."' ,";
		}
		
		$value_string="0 ,".$value_string;
		
		$tbl=self::getLastTableRDM($rand_name,$dblink);
		
		$res=mysql_query("INSERT INTO ".CSTB_PREFIX.$tbl." VALUES( ".trim($value_string,",")." )",$datalink);
		
		if($res)
			return array("s"=>"1","id"=>$rand_name."-".$tbl."-".mysql_insert_id($datalink));
		else
			return array("s"=>"0","r"=>mysql_error());
			
	}

	//在RDM模式下执行MYSQL操作
	//该函数需要op指定操作类型
	//$value时需要提供的参数数组的json字符串
	static function executeQueryRDM($op,$value){
		
		if(DISTRIBUTIVE_MODE<>"RDM") return array("s"=>"0","r"=>"distributive mode dose not match");
		
		$dblink=dbconnect::ConnectDB("index");
		switch($op){
			case "removeValue":
				//在此操作下$value数组的书写格式应该是：
				//$a["list"][i]="xxx";    指定检索的字段值
				//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)，该字段必须是unique_id的字段名
				//$a["modify_value"][i][j]="xxx";    指定需要push的字段值(可以设置多个字段)
				//其中i表示检索字段计数，j表示修改字段计数
				
				$count=0;
				for($i=0;$i<count($value["list"]);$i++){
					if($value["list"][$i]=="" ) continue;
					
					$position=self::analyzePositionRDM($value["list"][$i]);
					$datalink=self::connectServerRDM("write",$position,$dblink);
					
					$update_string="";
					for($j=0;$j<count($value["modify_keys"][$i]);$j++){
						$mkey=$value["modify_keys"][$i][$j];
						$mvalue=$value["modify_value"][$i][$j];
						$update_string.=$mkey."=TRIM(REPLACE(".$mkey.",'".mysql_real_escape_string($mvalue)."',' ')) ,";
					}
					
					mysql_query("UPDATE ".CSTB_PREFIX.$position["cstb"]." SET ".trim($update_string,",")." WHERE sdcpid = '".$position["id"]."' LIMIT 1",$datalink);
					$res=mysql_affected_rows($datalink);
					if($res>0)
						$count++;
					
				}
				
				$return=array("s"=>"1","count"=>$count);
				
				break;
			case "pushValue":
				//在此操作下$value数组的书写格式应该是：
				//$a["list"][i]="xxx";    指定检索的字段值
				//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)，该字段必须是unique_id的字段名
				//$a["modify_value"][i][j]="xxx";    指定需要push的字段值(可以设置多个字段)
				//其中i表示检索字段计数，j表示修改字段计数
				
				$count=0;
				for($i=0;$i<count($value["list"]);$i++){
					if($value["list"][$i]=="" ) continue;
					
					$position=self::analyzePositionRDM($value["list"][$i]);
					$datalink=self::connectServerRDM("write",$position,$dblink);
					
					$update_string="";
					for($j=0;$j<count($value["modify_keys"][$i]);$j++){
						$mkey=$value["modify_keys"][$i][$j];
						$mvalue=$value["modify_value"][$i][$j];
						$update_string.=$mkey."=concat_ws(' ','".mysql_real_escape_string($mvalue)."',".$mkey.") ,";
					}
					
					mysql_query("UPDATE ".CSTB_PREFIX.$position["cstb"]." SET ".trim($update_string,",")." WHERE sdcpid = '".$position["id"]."' LIMIT 1",$datalink);
					$res=mysql_affected_rows($datalink);
					if($res>0)
						$count++;
					
				}
				
				$return=array("s"=>"1","count"=>$count);
				
				break;
				
			case "updateItems":
				//在此操作下$value数组的书写格式应该是：
				//$a["list"][i]="xxx";    指定检索的字段值
				//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)，该字段必须是unique_id的字段名
				//$a["modify_value"][i][j]="xxx";    指定需要修改的字段值(可以设置多个字段)
				//其中i表示检索字段计数，j表示修改字段计数
				
				$count=0;
				for($i=0;$i<count($value["list"]);$i++){
					if($value["list"][$i]=="" ) continue;
					
					$position=self::analyzePositionRDM($value["list"][$i]);
					$datalink=self::connectServerRDM("write",$position,$dblink);
					
					$update_string="";
					for($j=0;$j<count($value["modify_keys"][$i]);$j++){
						$update_string.=$value["modify_keys"][$i][$j]."='".mysql_real_escape_string($value["modify_value"][$i][$j])."' ,";
					}
					
					mysql_query("UPDATE ".CSTB_PREFIX.$position["cstb"]." SET ".trim($update_string,",")." WHERE sdcpid = '".$position["id"]."' LIMIT 1",$datalink);
					$res=mysql_affected_rows($datalink);
					if($res>0)
						$count++;
					
				}
				
				$return=array("s"=>"1","count"=>$count);
				
				break;
			case "deleteItems":
				//在此操作下$value数组的书写格式应该是：
				//$a[i]="xxx";    指定检索的ID列表
				$count=0;
				
				for($i=0;$i<count($value);$i++){
					if($value[$i]=="") continue;
					
					$position=self::analyzePositionRDM($value[$i]);
					$datalink=self::connectServerRDM("write",$position,$dblink);

					mysql_query("DELETE FROM ".CSTB_PREFIX.$position["cstb"]." WHERE sdcpid = '".$position["id"]."' LIMIT 1",$datalink);
					$res=mysql_affected_rows($datalink);
					if($res>0)
						$count++;
					
				}
				
				$return=array("s"=>"1","count"=>$count);
				
				break;
			case "readItems":
				//在此操作下$value数组的书写格式应该是：
				//$a[i]="xxx";    指定检索的字段值
				
				$count=0;
				for($i=0;$i<count($value);$i++){
					if($value[$i]=="") continue;
					
					$position=self::analyzePositionRDM($value[$i]);
					$datalink=self::connectServerRDM("read",$position,$dblink);
					$res=mysql_query("SELECT * FROM ".CSTB_PREFIX.$position["cstb"]." WHERE sdcpid = '".$position["id"]."' LIMIT 1",$datalink);
					if(mysql_num_rows($res)<1) continue;
					
					$res_=mysql_fetch_assoc($res);
					foreach($res_ as $key=>$value_){
						if($key=="sdcpid") $value_=$value[$i];
						$return_string[$count][$key]=$value_;
					}
					
					$count++;
					
				}
				
				$return=array("s"=>"1","count"=>$count,"list"=>$return_string);
				
				break;
			default :
				return array("s"=>"0","undefined option");
		}
		
		return $return;
		
	}

//以下是自定义分配模式(UDDM)的数据库连接代码。

	//通过对一个unique_name进行分析（调用DistributiveRule类），得出该unique_name应当存储的server_name。
	//返回值是$position数组，包含server_name\cstb\id
	//注意！与RDM不同，在UDDM模式中，write\read\insert三种方式都应该应用该函数进行定位。
	static function analyzePositionUDDM($unique_name){
		
		if(preg_match("/^[0-9a-zA-Z]{1,18}\-[0-9]{1,18}\-\*$/",$unique_name)){
		
			$tmp=split("-",$unique_name);
			$position["list_type"]="show_all";
			$position["server"]=$tmp[0];
			$position["cstb"]=$tmp[1];
			
			return $position;
			
		}else{
			
			if(DISTRIBUTIVE_MODE<>"UDDM") return false;
			
			if(!file_exists("DistributiveRule.php")) return false;
			include_once "DistributiveRule.php";
			
			$position=DistributeCalculation::Calculate($unique_name);
			$position["list_type"]="normal";
			
			return $position;
			
		}
		
	}

	//指定操作类型之后，并在条目已经存在的情况下输入unique_name，若没有创建条目，则不应该使用本函数，而应该使用createItemUDDM()，连接到指定的数据库。
	static function connectServerUDDM($method,$position,$dblink){
		
		if(DISTRIBUTIVE_MODE<>"UDDM") return false;
		
		if($method=="read"){
			return self::connectDataServer("read",$position["server"],$dblink);
		}else if($method=="insert"){
			return self::connectDataServer("write",$position["server"],$dblink);
		}else if($method=="write"){
			return self::connectDataServer("write",$position["server"],$dblink);
		}
		
	}
	
	//在UDDM模式下创建一个条目，你需要提供unique_name和一个包含所需字段数据的数组。
	static function createItemUDDM($unique_name,array $value){
		
		if(DISTRIBUTIVE_MODE<>"UDDM") return array("s"=>"0","r"=>"distributive mode dose not match");
		
		$position=self::analyzePositionUDDM($unique_name);
		
		$dblink=dbconnect::ConnectDB("index");
		$datalink=self::connectDataServer("write",$position["server"],$dblink);
		
		$value_string="";
		foreach($value as $key=>$val){
			$value_string.="'".mysql_real_escape_string($val)."' ,";
		}
		
		list($cnt)=mysql_fetch_row(mysql_query("SELECT count(*) FROM ".CSTB_PREFIX.$position["cstb"]." WHERE sdcname='".$unique_name."' LIMIT 1",$datalink));
		if($cnt>0) return array("s"=>"0","r"=>"unique name repeats");
		
		$res=mysql_query("INSERT INTO ".CSTB_PREFIX.$position["cstb"]." VALUES ('".mysql_real_escape_string($unique_name)."',".trim($value_string,",").")",$datalink);
		
		if($res)
			return array("s"=>"1");
		else
			return array("s"=>"0","r"=>mysql_errno($res));
			
	}
	
	//在UDDM模式下执行MYSQL操作
	//该函数需要op指定操作类型
	//$value时需要提供的参数数组的json字符串
	static function executeQueryUDDM($op,$value){
		
		if(DISTRIBUTIVE_MODE<>"UDDM") return array("s"=>"0","r"=>"distributive mode dose not match");
		
		$dblink=dbconnect::ConnectDB("index");
		switch($op){
			case "removeValue":
				//在此操作下$value数组的书写格式应该是：
				//$a["list"][i]="xxx";    指定检索的字段值
				//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)，该字段必须是unique_id的字段名
				//$a["modify_value"][i][j]="xxx";    指定需要remove的字段值(可以设置多个字段)
				//其中i表示检索字段计数，j表示修改字段计数
				
				$count=0;
				for($i=0;$i<count($value["list"]);$i++){
					if($value["list"][$i]=="" ) continue;
					
					$position=self::analyzePositionUDDM($value["list"][$i]);
					if($position["list_type"]<>"normal") return array("s"=>"0","r"=>$position["list_type"]." list type can not be used here");
					
					$datalink=self::connectServerUDDM("write",$position,$dblink);
					
					$update_string="";
					for($j=0;$j<count($value["modify_keys"][$i]);$j++){
						$mkey=$value["modify_keys"][$i][$j];
						$mvalue=$value["modify_value"][$i][$j];
						$update_string.=$mkey."=TRIM(REPLACE(".$mkey.",'".mysql_real_escape_string($mvalue)."',' ')) ,";
					}
					
					mysql_query("UPDATE ".CSTB_PREFIX.$position["cstb"]." SET ".trim($update_string,",")." WHERE sdcname = '".$value["list"][$i]."' LIMIT 1",$datalink);

					$res=mysql_affected_rows($datalink);
					if($res>0)
						$count++;
					
				}

				$return=array("s"=>"1","count"=>$count);
				
				break;
			case "pushValue":
				//在此操作下$value数组的书写格式应该是：
				//$a["list"][i]="xxx";    指定检索的字段值
				//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)，该字段必须是unique_id的字段名
				//$a["modify_value"][i][j]="xxx";    指定需要push的字段值(可以设置多个字段)
				//其中i表示检索字段计数，j表示修改字段计数
				
				$count=0;
				for($i=0;$i<count($value["list"]);$i++){
					if($value["list"][$i]=="" ) continue;
					
					$position=self::analyzePositionUDDM($value["list"][$i]);
					if($position["list_type"]<>"normal") return array("s"=>"0","r"=>$position["list_type"]." list type can not be used here");
					
					$datalink=self::connectServerUDDM("write",$position,$dblink);
					
					$update_string="";
					for($j=0;$j<count($value["modify_keys"][$i]);$j++){
						$mkey=$value["modify_keys"][$i][$j];
						$mvalue=$value["modify_value"][$i][$j];
						$update_string.=$mkey."=concat_ws(' ','".mysql_real_escape_string($mvalue)."',".$mkey.") ,";
					}
					
					mysql_query("UPDATE ".CSTB_PREFIX.$position["cstb"]." SET ".trim($update_string,",")." WHERE sdcname = '".$value["list"][$i]."' LIMIT 1",$datalink);

					$res=mysql_affected_rows($datalink);
					if($res>0)
						$count++;
					
				}

				$return=array("s"=>"1","count"=>$count);
				
				break;
			case "updateItems":
				//在此操作下$value数组的书写格式应该是：
				//$a["list"][i]="xxx";    指定检索的sdcname字段值
				//$a["modify_keys"][i][j]="xxx";    指定需要修改的字段名(可以设置多个字段)
				//$a["modify_value"][i][j]="xxx";    指定需要修改的字段值(可以设置多个字段)
				//其中i表示检索字段计数，j表示修改字段计数
				
				$count=0;
				for($i=0;$i<count($value["list"]);$i++){
					if($value["list"][$i]=="") continue;
					
					$position=self::analyzePositionUDDM($value["list"][$i]);
					if($position["list_type"]<>"normal") return array("s"=>"0","r"=>$position["list_type"]." list type can not be used here");
					
					$datalink=self::connectServerUDDM("write",$position,$dblink);
					
					$update_string="";
					for($j=0;$j<count($value["modify_keys"][$i]);$j++){
						$update_string.=$value["modify_keys"][$i][$j]."='".$value["modify_value"][$i][$j]."' ,";
					}
					
					$res=mysql_query("UPDATE ".CSTB_PREFIX.$position["cstb"]." SET ".trim($update_string,",")." WHERE sdcname = '".$value["list"][$i]."' LIMIT 1",$datalink);
					
					$res=mysql_affected_rows($datalink);
					if($res>0)
						$count++;
					
				}
				
				$return=array("s"=>"1","count"=>$count);
				
				break;
			case "deleteItems":
				//在此操作下$value数组的书写格式应该是：
				//$a[i]="xxx";    指定检索的字段值
				$count=0;
				for($i=0;$i<count($value);$i++){
					if($value[$i]=="") continue;
					
					$position=self::analyzePositionUDDM($value[$i]);
					if($position["list_type"]<>"normal") return array("s"=>"0","r"=>$position["list_type"]." list type can not be used here");
					
					$datalink=self::connectServerUDDM("write",$position,$dblink);
					$res=mysql_query("DELETE FROM ".CSTB_PREFIX.$position["cstb"]." WHERE sdcname = '".$value[$i]."' LIMIT 1",$datalink);
					
					$res=mysql_affected_rows($datalink);
					if($res>0)
						$count++;
					
				}
				
				$return=array("s"=>"1","count"=>$count);
				
				break;
			case "readItems":
				//在此操作下$value数组的书写格式应该是：名
				//$a[i]="xxx";    指定检索的字段值
				$list_type_show_all=false;
				$list_type_show_all_position;
				
				$count=0;
				for($i=0;$i<count($value);$i++){
					if($value[$i]=="") continue;
					
					$position=self::analyzePositionUDDM($value[$i]);
					if($position["list_type"]=="show_all"){
						$list_type_show_all=true;
						$count=0;
						unset($return_string);
						$list_type_show_all_position=$position;
						break;
					}
					
					$datalink=self::connectServerUDDM("read",$position,$dblink);
					
					$res=mysql_query("SELECT * FROM ".CSTB_PREFIX.$position["cstb"]." WHERE sdcname = '".$value[$i]."' LIMIT 1",$datalink);
					if(mysql_num_rows($res)<1) continue;
					
					$res_=mysql_fetch_assoc($res);
					
					foreach($res_ as $key=>$value_){
						if($key=="sdcname") $value_=$value[$i];
						$return_string[$count][$key]=$value_;
					}
					
					$count++;
					
				}
				
				if($list_type_show_all){
					
					$datalink=self::connectServerUDDM("read",$list_type_show_all_position,$dblink);
					$res=mysql_query("SELECT * FROM ".CSTB_PREFIX.$list_type_show_all_position["cstb"],$datalink);
					
					while($res_=mysql_fetch_assoc($res)){
						foreach($res_ as $key=>$value_){
							$return_string[$count][$key]=$value_;
						}
						
						$count++;
						
					}
					
				}
				
				$return=array("s"=>"1","count"=>$count,"list"=>$return_string);
				
				break;
			default :
				return array("s"=>"0","undefined option");
		}
		
		return $return;
		
	}
	
}
class basicSetup{
	static function createDB(){
		
//验证数据库是否存在。若不存在则创建数据库

		
$dblink=dbconnect::ConnectDB("index","create");

$create_string="CREATE TABLE server_index 
(
server_name varchar(16),
read_host varchar(128),
write_host varchar(128),
read_switch int(1),
write_switch int(1),
table_count int(11),
UNIQUE KEY(server_name)
)ENGINE = MEMORY;";
$create_table1=mysql_query($create_string,$dblink);

$create_string="CREATE TABLE server_hhd 
(
server_name varchar(16),
read_host varchar(128),
write_host varchar(128),
read_switch int(1),
write_switch int(1),
table_count int(11),
UNIQUE KEY(server_name)
)ENGINE = MYISAM;";
$create_table2=mysql_query($create_string,$dblink);

if($create_table1 and $create_table2){
	return array("s"=>"1");
}else{
	return array("s"=>"0","r"=>"mysql error number:".mysql_errno($dblink));
}

	}
}
?>