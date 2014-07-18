<?php
class UserEngine{	
	static function signup(){
		$username=$_POST['username'];
		$token=time();
		$sdk=new SDCToolKit('user');
		$array=array('sdcname'=>$username,'UseName'=>$username,'UserToken'=>$token,'SignIndex'=>'','CareIndex'=>'','RemarkIndex'=>'');
		$return=$sdk->createItemUDDM($array);
		return $return;			
	}
	static function viewMySigns($username,$page=null){
		$sdk=new SDCToolKit('user');
		$user[0]=$username;		
		$array=array('key'=>'UserName','list'=>$use);
		$signs_str=$sdk->readItemsUDDM($array);
		if(!$signs_str['count'])	return array('s'=>'0');
		$signs=explode(',',$acts_str['list'][0]['SignIndex']);
		$sdk=new SDCToolKit('Sign');//基本活动表
		$array=array('key'=>'SignID','list'=>$signs);
		$return=$sdk->readItemsUDDM($array);
		if(!$return['count'])	return array('s'=>'0');
		return 	array('s'=>'1','count'=>$return['count'],'list'=>$return['list']);
	}
	static function viewMyCares($username,$page=null){
		$sdk=new SDCToolKit('user');
		$user[0]=$username;		
		$array=array('key'=>'UserName','list'=>$use);
		$acts_str=$sdk->readItemsUDDM($array);
		if(!$acts_str['count'])	return array('s'=>'0');
		$acts=explode(',',$acts_str['list'][0]['CareIndex']);
		$sdk=new SDCToolKit('activity');//基本活动表
		$array=array('key'=>'ID','list'=>$acts);
		$return=$sdk->readItemsUDDM($array);
		if(!$return['count'])	return array('s'=>'0');
		return 	array('s'=>'1','count'=>$return['count'],'list'=>$return['list']);
	}
	static function viewMyRemarks($username,$page=null){
		$sdk=new SDCToolKit('user');
		$user[0]=$username;		
		$array=array('key'=>'UserName','list'=>$use);
		$acts_str=$sdk->readItemsUDDM($array);
		if(!$acts_str['count'])	return array('s'=>'0');
		$acts=explode(',',$acts_str['list'][0]['RemarkIndex']);
		$actNums=count($acts);
		$return_array=array();
		for($i=0;$i<$actNums;$i++){
			$act_str=$acts[$i];
			$act_array=explode(',',$act_str);
			$act_id[0]=$act_array[0];
			$act_point=$act_array[1];
			$sdk=new SDCToolKit('activity');//基本活动表
			$array=array('key'=>'ID','list'=>$act_id);
			$return=$sdk->readItemsUDDM($array);
			if(!$return['count'])	return array('s'=>'0');
			$return_array[$i]['act']=$return['list'][0];
			$return_array[$i]['point']=$act_point;
		}
		return 	array('s'=>'1','count'=>$actNums,'list'=>$return_array);
	}
	static function modifyMySignIndex($username,$op,$signId){
		$sdk=new SDCToolKit('user');
		$user[0]=$username;		
		$array=array('key'=>'UserName','list'=>$use);
		$acts_str=$sdk->readItemsUDDM($array);
		if(!$acts_str['count'])	return array('s'=>'0');
		$acts=$acts_str['list'][0]['SignIndex'];
		$acts_array=explode(',',$acts);
		if($op=='add'){
			if(empty($acts)){
				$value_array=array('key'=>array('UserName'),'list'=>array($user),'modify_keys'=>array('SignIndex'),'modify_value'=>array($signId));
				$update=$sdk->updateItemsRDM($value_array);
				if(!$update['count'])	return array('s'=>'0');
				return 	array('s'=>'1','count'=>$update['count']);	
			}else{
				foreach($acts_array as $value){
					if($value==$signId)
					return array('s'=>'0','r'=>'这个活动您已经报过名了');
				}
				$str=$acts.','.$signId;
				$value_array=array('key'=>array('UserName'),'list'=>array($user),'modify_keys'=>array('SignIndex'),'modify_value'=>array($str));
				$update=$sdk->updateItemsRDM($value_array);
				if(!$update['count'])	return array('s'=>'0');
				return 	array('s'=>'1','count'=>$update['count']);					
			}
		}else if($op=='cancel'){
			if(empty($acts)){
				return 	array('s'=>'1','r'=>'还没有参加活动');	
			}else{
				foreach($acts as $key=>&$value){
					if($value==$signId)
					unset($acts[$key]);
				}
				$str=implode(',',$acts);
				$value_array=array('key'=>array('UserName'),'list'=>array($user),'modify_keys'=>array('SignIndex'),'modify_value'=>array($str));
				$update=$sdk->updateItemsRDM($value_array);
				if(!$update['s']['count'])	return array('s'=>'0');
				return 	array('s'=>'1','count'=>$update['count']);		
			}
		}
	}
	static function modifyMyCareIndex($username,$op,$actId){
		$sdk=new SDCToolKit('user');
		$user[0]=$username;		
		$array=array('key'=>'UserName','list'=>$use);
		$acts_str=$sdk->readItemsUDDM($array);
		if(!$acts_str['count'])	return array('s'=>'0');
		$acts=$acts_str['list'][0]['CareIndex'];
		if($op=='add'){
			if(empty($acts)){
				$value_array=array('key'=>array('UserName'),'list'=>array($user),'modify_keys'=>array('CareIndex'),'modify_value'=>array($actId));
				$update=$sdk->updateItemsRDM($value_array);
				if(!$update['count'])	return array('s'=>'0');
				return 	array('s'=>'1','count'=>$update['count']);	
			}else{
				$str=$acts.','.$actId;
				$value_array=array('key'=>array('UserName'),'list'=>array($user),'modify_keys'=>array('CareIndex'),'modify_value'=>array($str));
				$update=$sdk->updateItemsRDM($value_array);
				if(!$update['count'])	return array('s'=>'0');
				return 	array('s'=>'1','count'=>$update['count']);					
			}
		}else if($op=='cancel'){
			if(empty($acts)){
				return 	array('s'=>'1');	
			}else{
				foreach($acts as $key=>&$value){
					if($value==$actId)
					unset($acts[$key]);
				}
				$str=implode(',',$acts);
				$value_array=array('key'=>array('UserName'),'list'=>array($user),'modify_keys'=>array('SignIndex'),'modify_value'=>array($str));
				$update=$sdk->updateItemsRDM($value_array);
				if(!$update['count'])	return array('s'=>'0');
				return 	array('s'=>'1','count'=>$update['count']);		
			}
		}		
	}
	static function modifyMyRemarkIndex($username,$op,$actId,$point=null){
		$sdk=new SDCToolKit('user');
		$user[0]=$username;		
		$array=array('key'=>'UserName','list'=>$use);
		$acts_str=$sdk->readItemsUDDM($array);
		if(!$acts_str['count'])	return array('s'=>'0');
		$acts=$acts_str['list'][0]['RemarkIndex'];
		if($op=='add'){
			if(empty($acts)){
				$markStr=$actId.'-'.$point;
				$value_array=array('key'=>array('UserName'),'list'=>array($user),'modify_keys'=>array('RemarkIndex'),'modify_value'=>array($markStr));
				$update=$sdk->updateItemsRDM($value_array);
				if(!$update['count'])	return array('s'=>'0');
				return 	array('s'=>'1','count'=>$update['count']);	
			}else{
				$str=$acts.','.$actId.'-'.$point;
				$value_array=array('key'=>array('UserName'),'list'=>array($user),'modify_keys'=>array('RemarkIndex'),'modify_value'=>array($str));
				$update=$sdk->updateItemsRDM($value_array);
				if(!$update['count'])	return array('s'=>'0');
				return 	array('s'=>'1','count'=>$update['count']);					
			}
		}else if($op=='cancel'){
			if(empty($acts)){
				return 	array('s'=>'1');	
			}else{
				foreach($acts as $key=>&$value){
					$actMark=explode('-',$value);
					$actMarkId=$actMark[0];
					if($value==$actId)
					unset($acts[$key]);
				}
				$str=implode(',',$acts);
				$value_array=array('key'=>array('UserName'),'list'=>array($user),'modify_keys'=>array('SignIndex'),'modify_value'=>array($str));
				$update=$sdk->updateItemsRDM($value_array);
				if(!$update['count'])	return array('s'=>'0');
				return 	array('s'=>'1','count'=>$update['count']);		
			}
		}			
	}						
}
?>