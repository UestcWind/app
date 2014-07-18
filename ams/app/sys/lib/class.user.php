<?php
class User{
	public $UserName;
	public $UserToken;
	public $SignIndex;
	public $CareIndex;
	public $RemarkIndex;
	
	function __constructor($user=null){
		if(isset($user)){
			if(is_array($user)){
				$this->UserName=$user['UserName'];
				$this-$UserToken;$user['$UserToken;'];
				$this->SignIndex=$user['SignIndex'];
				$this->CareIndex=$user['CareIndex'];
				$this->RemarkIndex=$user['RemarkIndex'];
			}
		}
	}
	
}
?>