<?php
class ActivityEngine{
	static function addAct($actinfo){	
		$sdk=new SDCToolKit('activity');
		$insert=$sdk->createItemUDDM($unique_name,$actinfo);//$unique_name还没有定义，$actinfo在外层带入
		if(!$insert['s'])	return array('s'=>'0','r'=>$insert['r']);	else return array('s'=>'1');
		$insert_id=mysql_insert_id();
		//同时添加进打分表
		$sdk=new SDCToolKit('mark');	
		$unique_name1=null;
		$markinfo=array('Act_ID'=>$insert_id,'CareIndex'=>'','MarkIndex'=>'','Average'=>'0');
		$insert=$sdk->createItemUDDM($unique_name1,$markinfo);
		if(!$insert['s'])	return array('s'=>'0','r'=>$insert['r']);	else return array('s'=>'1');
		//同时添加进活动主页索引表，但索引还没想好怎么设计。。	
	}
	static function modifyAct($actId,$actinfo){
		$sdk=new SDCToolKit('activity');
		$value_array=array('key'=>array('0'=>'ID'),'list'=>array('0'=>$actId),'modify_keys'=>$actinfo['modify_keys'],'modify_value'=>$actinfo['modify_value']);
		$update=$sdk->updateItemsRDM($value_array);
		if(!$update['count'])	return array('s'=>'0');	else return array('s'=>'1');		
	}
	static function deleteAct($id){
		$sdk=new SDCToolKit('activity');
		$actId[0]=$id;	
		$array=array('key'=>'ID','list'=>$actId);
		$act=$sdk->readItemsUDDM($array);
		if(!$act['count'])	return array('s'=>'0','r'=>'活动不存在!');	
		$delete=$sdk->deleteItemsUDDM($array);
		if(!$delete['count'])	return array('s'=>'0','r'=>'删除出错了');
		return array('s'=>'1');				
	}
	static function showActById($actId){
		$sdk=new SDCToolKit('activity');
		$actId[0]=$id;	
		$array=array('key'=>'ID','list'=>$actId);
		$act=$sdk->readItemsUDDM($array);
		if(!$act['count'])	return array('s'=>'0','r'=>'活动不存在!');	
		$return['act']=$act['list'][0];
		$signs=SignEngine::listSignsByAct($actId);
		if(!$signs['s'])	return array('s'=>'0');	
		$return['signs']=$signs['list'];
		return array('s'=>'1','list'=>$return[0]);
	}
	static function listActs($where=null){
		//还没想好
		$sdk=new SDCToolKit('index');
		$index[0]=$id;	
		$array=array('key'=>'IndexID','list'=>$actId);
		$acts_str=$sdk->readItemUDDM($pere);
		if($acts_str['count']==0)
		return array('s'=>'0','r'=>'该校园暂时没有活动举行');
	}	
	static function modifySignIndex($actId,$op,$signId){
		
	}	
	static function viewUpdates($actId){
		
	}
	static function modifyUpdateIndex($actId,$op,$updateId){
		
	}							
}
?>