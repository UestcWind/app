<?php
class SignItem{
	public $SignID;
	public $SignName;
	public $SignDetail;
	public $ActID;
	public $SignStatus;
	public $SignLimit;
	public $SignNums;
	public $SignUserIndex;
	
	function __constructor($sign=null){
		if(isset($sign)){
			if(is_array($sign)){
				$this->SignID=$sign['SignID'];
				$this->SignName=$sign['SignName'];
				$this->SignDetail=$sign['SignDetail'];
				$this->ActID=$sign['ActID'];
				$this->SignStatus=$sign['SignStatus'];
				$this->SignLimit=$sign['SignLimit'];
				$this->SignNums=$sign['SignNums'];
				$this->SignUserIndex=$sign['SignUserIndex'];
			}
		}
	}
	
}
?>