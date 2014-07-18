<?php
class UpdateEngine{
	static function addUpdate($datainfo){
		$sdk=new SDCToolKit('update');
		$insert=$sdk->createItemUDDM($unique_name,$datainfo);//$unique_name还没有定义，$datainfo在外层带入
		if(!$insert['s'])	return array('s'=>'0','r'=>$insert['r']);	else return array('s'=>'1');			
	}
	static function deleteUpdate($updId){
		$sdk=new SDCToolKit('update');
		$update[0]=$updId;	
		$array=array('key'=>'UpdateID','list'=>$update);
		$index_str=$sdk->readItemsUDDM($array);
		if(!$index_str['count'])	return array('s'=>'0','r'=>'该更新项不存在!');	
		$delete=$sdk->deleteItemsUDDM($array);
		if(!$delete['count'])	return array('s'=>'0','r'=>'删除出错了');
		return array('s'=>'1');				
	}
	static function viewUpdateById($updId){
		$sdk=new SDCToolKit('index');
		$index[0]=$updId;	
		$array=array('key'=>'UpdateID','list'=>$index);
		$index_str=$sdk->readItemsUDDM($array);
		if(!$index_str['count'])	return array('s'=>'0','r'=>'活动不存在!');	
		//$imgs=$index_str['list'][0]['images'];
		//$videos=$index_str['list'][0]['video'];
		return array('s'=>'1','list'=>$index_str['list'][0]);		
	}
	static function listUpdatesByActId($actId){
		$sdk=new SDCToolKit('index');
		$index[0]=$actId;	
		$array=array('key'=>'actID','list'=>$index);
		$index_str=$sdk->readItemsUDDM($array);
		if(!$index_str['count'])	return array('s'=>'0','r'=>'活动不存在!');	
		return array('s'=>'1','count'=>$index_str['count'],'list'=>$index_str['list']);			
	}					
}
?>