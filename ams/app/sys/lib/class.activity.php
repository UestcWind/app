<?php
class Activity{
	public $ID;
	public $ActName;
	public $ActTime;
	public $ActPlace;
	public $ActIntro;
	public $SignIndex;
	public $UpdateIndex;
	public $Tags;
	public $ActType;
	public $CreUser;
	public $BlogID;
	public $SubSvr;
	
	function __constructor($activity=null){
		if(isset($activity)){
			if(is_array($activity)){
				$this->ID=$activity['ID'];
				$this->ActName=$activity['ActName'];
				$this->ActTime=$activity['ActTime'];
				$this->ActPlace=$activity['ActPlace'];
				$this->ActIntro=$activity['ActIntro'];
				$this->SignIndex=$activity['SignIndex'];
				$this->UpdateIndex=$activity['UpdateIndex'];
				$this->Tags=$activity['Tags'];
				$this->ActType=$activity['ActType'];
				$this->CreUser=$activity['CreUser'];
				$this->BlogID=$activity['BlogID'];
				$this->SubSvr=$activity['SubSvr'];
			}
		}
	}
	
}
?>