<?php
class IndexEngine{
	static function addActIndex($datainfo){
		$sdk=new SDCToolKit('index');
		$insert=$sdk->createItemUDDM($unique_name,$datainfo);
		if(!$insert['s'])	return array('s'=>'0','r'=>$insert['r']);	else return array('s'=>'1');			
	}
	static function deleteActIndex($indexId){
		$sdk=new SDCToolKit('index');
		$index[0]=$indexId;	
		$array=array('key'=>'IndexID','list'=>$index);
		$index_str=$sdk->readItemsUDDM($array);
		if(!$index_str['count'])	return array('s'=>'0','r'=>'该索引不存在!');	
		$delete=$sdk->deleteItemsUDDM($array);
		if(!$delete['count'])	return array('s'=>'0','r'=>'删除出错了');
		return array('s'=>'1');			
	}
	static function modifyActIndex($indexId,$datainfo){
		$sdk=new SDCToolKit('index');
		$value_array=array('key'=>array('0'=>'IndexID'),'list'=>array('0'=>$indexId),'modify_keys'=>$datainfo['modify_keys'],'modify_value'=>$datainfo['modify_value']);
		$update=$sdk->updateItemsRDM($value_array);
		if(!$update['count'])	return array('s'=>'0');	else return array('s'=>'1');		
	}
	static function viewActIndex($indexId){//还没确定索引怎么设计
		$sdk=new SDCToolKit('index');
		$index[0]=$indexId;	
	}			
}
?>