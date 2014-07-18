<?php
class MarkEngine{
	static function mark($username,$actId,$point){
		$sdk=new SDCToolKit('mark');
		$act[0]=$actId;
		$array=array('key'=>'Act_ID','list'=>$act);
		$mark_str=$sdk->readItemsUDDM($array);
		if(!$mark_str['count'])	return array('s'=>'0','r'=>'活动不存在!');	
		$user_str=$mark_str['list'][0]['MarkIndex'];
		$user_array=explode(',',$user_str);
		if(empty($user_str)){
			$userNums=0;
			$datainfo['modify_keys'][0]='MarkIndex';
			$datainfo['modify_value'][0]=$username.'-'.$point;			
		}else{
			$userNums=count($user_array);
			$datainfo['modify_keys'][0]='MarkIndex';
			$datainfo['modify_value'][0]=$user_str.','.$username.'-'.$point;		
		}
		foreach($user_array as $value){
			$name=explode('-',$value);
			$name=$name[0];
			if($name==$username)
			return array('s'=>'0','r'=>'这个活动您已经打过分了');
		}
		$datainfo['modify_keys'][1]='Average';
		$averageNew=($mark_str['list'][0]['Average']*$userNums+$point)/($userNums+1);
		$datainfo['modify_value'][1]=$averageNew;
		$value_array=array('key'=>array('0'=>'Act_ID'),'list'=>array('0'=>$actId),'modify_keys'=>$datainfo['modify_keys'],'modify_value'=>$datainfo['modify_value']);
		$update=$sdk->updateItemsRDM($value_array);
		if(!$update['count'])	return array('s'=>'0');	 
		$userUpdate=UserEngine::modifyMyRemarkIndex($username,'add',$actId,$point);
		if(!$userUpdate['s'])	return array('s'=>'0');	
		return array('s'=>'1');				
	}
	
	
	static function cancelMark($username,$actId){
		$sdk=new SDCToolKit('mark');
		$act[0]=$actId;
		$array=array('key'=>'Act_ID','list'=>$act);
		$mark_str=$sdk->readItemsUDDM($array);
		if(!$mark_str['count'])	return array('s'=>'0','r'=>'活动不存在!');	
		$user_str=$mark_str['list'][0]['MarkIndex'];
		$user_array=explode(',',$user_str);
		if(empty($user_str)){
			return array('s'=>'0','r'=>'您没有对这个活动打过分');
		}else{
			$userNums=count($user_array);
		}
		foreach($user_array as &$value){
			$mark_item=explode('-',$value);
			$name=$mark_item[0];			
			if($name==$username){
				$cacelPoint=$mark_item[1];
				unset($user_array[$key]);
			}
		}
		$user_str=implode(',',$user_array);
		$datainfo['modify_keys'][0]='MarkIndex';
		$datainfo['modify_value'][0]=$user_str;
		$datainfo['modify_keys'][1]='Average';
		$averageNew=($mark_str['list'][0]['Average']*$userNums-$point)/($userNums-1);
		$datainfo['modify_value'][1]=$averageNew;
		$value_array=array('key'=>array('0'=>'Act_ID'),'list'=>array('0'=>$actId),'modify_keys'=>$datainfo['modify_keys'],'modify_value'=>$datainfo['modify_value']);
		$update=$sdk->updateItemsRDM($value_array);
		if(!$update['count'])	return array('s'=>'0');
		$userUpdate=UserEngine::modifyMyRemarkIndex($username,'cancel',$actId);
		if(!$userUpdate['s'])	return array('s'=>'0');	
		return array('s'=>'1');				
	}
	static function addCare($username,$actId){
		$sdk=new SDCToolKit('mark');
		$act[0]=$actId;
		$array=array('key'=>'Act_ID','list'=>$act);
		$mark_str=$sdk->readItemsUDDM($array);
		if(!$mark_str['count'])	return array('s'=>'0','r'=>'活动不存在!');	
		$user_str=$mark_str['list'][0]['CareIndex'];
		$user_array=explode(',',$user_str);
		if(empty($user_str)){
			$datainfo['modify_keys'][0]='CareIndex';
			$datainfo['modify_value'][0]=$username;				
		}else{
			$datainfo['modify_keys'][0]='CareIndex';
			$datainfo['modify_value'][0]=','.$username;			
			foreach($user_array as $value){
				if($value==$username)
				return array('s'=>'0','r'=>'您已经关注这个活动了');
			}	
		}
		$value_array=array('key'=>array('0'=>'Act_ID'),'list'=>array('0'=>$actId),'modify_keys'=>$datainfo['modify_keys'],'modify_value'=>$datainfo['modify_value']);
		$update=$sdk->updateItemsRDM($value_array);
		if(!$update['count'])	return array('s'=>'0');
		$userUpdate=UserEngine::modifyMyCareIndex($username,'add',$actId);
		if(!$userUpdate['s'])	return array('s'=>'0');	
		return array('s'=>'1');			
	}
	static function cancelCare($username,$indexId){
		$sdk=new SDCToolKit('mark');
		$act[0]=$actId;
		$array=array('key'=>'Act_ID','list'=>$act);
		$mark_str=$sdk->readItemsUDDM($array);
		if(!$mark_str['count'])	return array('s'=>'0','r'=>'活动不存在!');	
		$user_str=$mark_str['list'][0]['CareIndex'];
		$user_array=explode(',',$user_str);
		if(empty($user_str)){
			return array('s'=>'0','r'=>'您还没有关注过这个活动');			
		}else{		
			foreach($user_array as $key=>&$value){
				if($value==$username)
				unset($user_array[$key]);
			}	
		}
		$user_str=implode(',',$user_array);
		$datainfo['modify_keys'][0]='MarkIndex';
		$datainfo['modify_value'][0]=$user_str;
		$value_array=array('key'=>array('0'=>'Act_ID'),'list'=>array('0'=>$actId),'modify_keys'=>$datainfo['modify_keys'],'modify_value'=>$datainfo['modify_value']);
		$update=$sdk->updateItemsRDM($value_array);
		if(!$update['count'])	return array('s'=>'0');	
		$userUpdate=UserEngine::modifyMyCareIndex($username,'cancel',$actId);
		if(!$userUpdate['s'])	return array('s'=>'0');	
		return array('s'=>'1');			
	}			
}
?>