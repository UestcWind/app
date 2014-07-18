<?php
function GetRealIP(){
	error_reporting (E_ERROR | E_WARNING | E_PARSE);
	if($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]){
		$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
	}elseif($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]){
		$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
	}elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]){
		$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
	}elseif (getenv("HTTP_X_FORWARDED_FOR")){
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}elseif (getenv("HTTP_CLIENT_IP")){
		$ip = getenv("HTTP_CLIENT_IP");
	}elseif (getenv("REMOTE_ADDR")){
		$ip = getenv("REMOTE_ADDR");
	}else{
		$ip = "Unknown";
	}
	return $ip;
}
function calcuPage($allcount,$page,$perpage){
	$allpage=ceil($allcount/$perpage);
	if($page<$allpage){$range["start"]=($page-1)*$perpage;$range["end"]=$page*$perpage;}
	else if($page==$allpage){$range["start"]=($page-1)*$perpage;$range["end"]=$allcount;}
	else{$range["start"]=($allpage-1)*$perpage;$range["end"]=$allcount;}
	
	return array("start"=>$range["start"],"end"=>$range["end"],"pagecount"=>$allpage);
}
function file_get_content($url,$post_filed) {
	$ch = curl_init();
	$timeout = 30;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_filed);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	return $file_contents;
}
function anyToUTF8($string){
	return $string;
}
function qstr_encode(array $array){//发送请求编码
	return base64_encode(json_encode($array));
}
function qstr_decode($qstr){//接受请求解码
	return json_decode(base64_decode($qstr),true);
}
function rstr_encode(array $array){//返回值编码
	$return_str=isset($_GET['callback'])?$_GET['callback']."(".json_encode($array).")":json_encode($array);
	return $return_str;
}
function rstr_decode($rstr){//返回值解码
	$return_str=json_decode($rstr,true);
	return $return_str;
}
?>