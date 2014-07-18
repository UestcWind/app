<?php
class SignEngine{
	static function addSignItem($actId,$signInfo){
		$sdk=new SDCToolKit('activity');
		$act[0]=$actId;	
		$array=array('key'=>'ID','list'=>$act);
		$signs=$sdk->readItemsUDDM($array);
		if(!$signs['count'])	return array('s'=>'0','r'=>'活动不存在!');		
		$sdk=new SDCToolKit('sign');
		$insert=$sdk->createItemUDDM($unique_name,$signInfo);//$unique_name还没有定义，$signInfo在外层带入
		if(!$insert['s'])	return array('s'=>'0','r'=>$insert['r']);
		$insert_id=mysql_insert_id();
		//修改对应活动的报名项字段
		$actStr=$signs['list'][0]['SignIndex'];
		if(empty($actStr)){
			$actInfo['modify_keys'][0]='SignIndex';
			$actInfo['modify_value'][0]=$insert_id;
		}else{
			$actStr.=','.$insert_id;
			$actInfo['modify_keys'][0]='SignIndex';
			$actInfo['modify_value'][0]=$actStr;
		}
		$userIndex=ActivityEngine::modifyAct($actId,$actInfo);
		if(!$userIndex['count'])	return array('s'=>'0');
		return array('s'=>'1');		
	}
	
	static function deleteSignItem($signId){
		$sdk=new SDCToolKit('sign');
		$sign[0]=$signId;	
		$array=array('key'=>'SignID','list'=>$sign);
		$signs=$sdk->readItemsUDDM($array);
		if(!$signs['count'])	return array('s'=>'0','r'=>'报名项不存在!');	
		//删除已经报名的用户的报名索引项的对应活动项
		$userIndex=$signs['list'][0]['SignUserIndex'];
		$user_array=explode(',',$userIndex);
		foreach($user_array as $value){
			$userIndex=UserEngine::modifyMySignIndex($value,'cancel',$signId);
		 	if(!$userIndex['count'])	return array('s'=>'0');
		}
		$delete=$sdk->deleteItemsUDDM($array);
		if(!$delete['count'])	return array('s'=>'0','r'=>'删除出错了');
		return array('s'=>'1');			
	}
	static function modifySignItem($signId,$signInfo){
		$sdk=new SDCToolKit('sign');
		$value_array=array('key'=>array('0'=>'SignID'),'list'=>array('0'=>$signId),'modify_keys'=>$signInfo['modify_keys'],'modify_value'=>$signInfo['modify_value']);
		$update=$sdk->updateItemsRDM($value_array);
		if(!$update['count'])	return array('s'=>'0');	else return array('s'=>'1');
	}
	static function signUp($username,$signId){
		$userIndex=UserEngine::modifyMySignIndex($username,'add',$signId);
		if(!$userIndex['count'])	return array('s'=>'0');		
		$sdk=new SDCToolKit('sign');
		$user[0]=$username;		
		$sign[0]=$signId;	
		$array=array('key'=>'SignID','list'=>$sign);
		$signs=$sdk->readItemsUDDM($array);
		if(!$signs['count'])	return array('s'=>'0');
		$signs_str=$signs['list'][0]['SignUserIndex'];
		$nums=$signs['list'][0]['SignNums'];
		$limit=$signs['list'][0]['SignLimit'];
		if($nums==$limit)	return array('s'=>'0','r'=>'名额已经满了');
		if(empty($signs_str)){
			$value_array=array('key'=>array('0'=>'SignID'),'list'=>array('0'=>$sign),'modify_keys'=>array('0'=>'SignUserIndex','1'=>'SignNums'),'modify_value'=>array('0'=>$signId,'1'=>$nums+1));
			$update=$sdk->updateItemsRDM($value_array);
			if(!$update['count'])	return array('s'=>'0');
			return 	array('s'=>'1','count'=>$update['count']);	
		 }else{
			$str=$signs_str.','.$signId;
			$value_array=array('key'=>array('0'=>'SignID'),'list'=>array('0'=>$sign),'modify_keys'=>array('0'=>'SignUserIndex','1'=>'SignNums'),'modify_value'=>array('0'=>$str,'1'=>$nums+1));
			$update=$sdk->updateItemsRDM($value_array);
			if(!$update['count'])	return array('s'=>'0');	
			return 	array('s'=>'1','count'=>$update['count']);					
		 }
		 return array('s'=>'1','count'=>$userIndex['count']);	
	}
	static function cancelSign($username,$signId){
		$sdk=new SDCToolKit('sign');
		$user[0]=$username;		
		$sign[0]=$signId;	
		$array=array('key'=>'SignID','list'=>$sign);
		$signs=$sdk->readItemsUDDM($array);
		if(!$signs['count'])	return array('s'=>'0');
		$signs_str=$signs['list'][0]['SignUserIndex'];
		$nums=$signs['list'][0]['SignNums'];
		$limit=$signs['list'][0]['SignLimit'];
		if($nums==0)	return array('s'=>'0','r'=>'还没有报名');
		if(strlen($signs_str)==1){
			$value_array=array('key'=>array('0'=>'SignID'),'list'=>array('0'=>$sign),'modify_keys'=>array('0'=>'SignUserIndex','1'=>'SignNums'),'modify_value'=>array('0'=>'','1'=>$nums-1));
			$update=$sdk->updateItemsRDM($value_array);
			if(!$update['count'])	return array('s'=>'0');
			return 	array('s'=>'1','count'=>$update['count']);	
		 }else{
			 	$signs_array=explode(',',$signs_str);
				foreach($signs_array as $key=>&$value){
					if($value==$signId)
					unset($signs_array[$key]);
				}
				$str=implode(',',$signs_array);
			$value_array=array('key'=>array('0'=>'SignID'),'list'=>array('0'=>$sign),'modify_keys'=>array('0'=>'SignUserIndex','1'=>'SignNums'),'modify_value'=>array('0'=>$str,'1'=>$nums-1));
			$update=$sdk->updateItemsRDM($value_array);
			if(!$update['count'])	return array('s'=>'0');	
			return 	array('s'=>'1','count'=>$update['count']);					
		 }
		 $userIndex=UserEngine::modifyMySignIndex($username,'cancel',$signId);
		 if(!$userIndex['count'])	return array('s'=>'0');
		 return array('s'=>'1','count'=>$userIndex['count']);	
	}	
	static function viewSignById($signId){
		$sdk=new SDCToolKit('sign');
		$sign[0]=$signId;		
		$array=array('key'=>'SignID','list'=>$sign);
		$sign_str=$sdk->readItemsUDDM($array);
		if(!$sign_str['count'])	return array('s'=>'0');
		return 	array('s'=>'1','list'=>$return['list']);		
	}
	static function viewSignUsers($signId){
		$sdk=new SDCToolKit('sign');
		$sign[0]=$signId;		
		$array=array('key'=>'SignID','list'=>$sign);
		$sign_str=$sdk->readItemsUDDM($array);
		if(!$sign_str['count'])	return array('s'=>'0');
		$users_str=$sign_str['list'][0]['SignUserIndex'];
		$users_array=explode(',',$users_str);
		$sdk=new SDCToolKit('user');
		$array=array('key'=>'UserName','list'=>$users_array);
		$sign_str=$sdk->readItemsUDDM($array);
		if(!$sign_str['count'])	return array('s'=>'0');
		return 	array('s'=>'1','list'=>$sign_str['list']);				
	}
	static function listSignsByAct($actId){
		$sdk=new SDCToolKit('sign');
		$act_Id[0]=$actId;		
		$array=array('key'=>'ActID','list'=>$act_Id);
		$sign_str=$sdk->readItemsUDDM($array);
		if(!$sign_str['count'])	return array('s'=>'0');
		$signs_array=$sign_str['list'];
		return 	array('s'=>'1','list'=>$signs_array);				
	}	
	static function checkSignStatus($signId){
		$sdk=new SDCToolKit('sign');
		$sign[0]=$signId;		
		$array=array('key'=>'SignID','list'=>$sign);
		$sign_str=$sdk->readItemsUDDM($array);
		if(!$sign_str['count'])	return array('s'=>'0');
		$status=$sign_str['list'][0]['SignStatus'];
		if($status)		return 	array('s'=>'1'); else return array('s'=>'0');	
			
	}						
}
?>