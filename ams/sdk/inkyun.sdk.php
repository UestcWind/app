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

include_once dirname(__FILE__)."/include.php";

class inkyunOpen{
	
	var $init_status;
	var $init_failed_reason;
	
	var $app_secret;
	var $app_id;
	var $device;
	var $user_token;
	var $user_name;
	var $user_realname;
	var $user_stuid;
	var $user_campus;
	var $user_campus_title;
	var $user_college;
	var $user_college_title;
	var $user_sex;
	var $user_icon;
	
	function __construct($username,$token,$device){
		$this->app_id=getenv("INKYUN_APP_APPID");
		$this->app_secret=getenv("INKYUN_APP_SECRETKEY");
		$this->device=$device;
		
		$this->user_name=$username;
		$this->user_token=$token;
		
		$check_status=$this->checkUserIdentityStatus($username,$token,$device);
		
		if($check_status["s"]<>"1"){
			$this->init_status=false;
			$this->init_failed_reason=$check_status["r"];
		}else{
			$this->init_status=true;
			$this->init_failed_reason="";
		}
		
	}
	
	function checkUserIdentityStatus($username){

		$return=$this->sendRequire("USER005",array("icon_size"=>"70"));
		
		if($return["s"]=="1"){
		
			$this->user_realname=$return["data"]["realname"];
			$this->user_stuid=$return["data"]["stuid"];
			$this->user_campus=$return["data"]["campus"];
			$this->user_campus_title=$return["data"]["campus_title"];
			$this->user_college=$return["data"]["college"];
			$this->user_college_title=$return["data"]["college_title"];
			$this->user_sex=$return["data"]["sex"];
			$this->user_icon=$return["data"]["usericon"];
			
		}
		
		return $return;
		
	}
	
	function inkyun($op,$peremeter_array){
		
		if($op=="") return array("s"=>"0","r"=>"please enter operation code");
		
		return $this->sendRequire($op,$peremeter_array);
		
	}
	
	protected function sendRequire($op,$peremeter_array){
		
		$query_string="&op=".$op;
		
		foreach($peremeter as $key=>$value){
			$query_string=$query_string."&".$key."=".urlencode($value);
		}
		
		return $this->rstr_decode($this->reqCore(getenv("MASTERSERVICE_REQUIREURL"),"LOGAPPID=".$this->app_id."&LOGSECRETKEY=".$this->app_secret."&LOGUSER=".$this->user_name."&LOGAUTHTOKEN=".$this->user_token."&LOGDEVICE=".$this->device.$query_string));
		
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