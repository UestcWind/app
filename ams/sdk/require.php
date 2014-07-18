<?php
//This page is the back-end of the SOL.
//-------------------------------------
//'SOL PROJECT' IS THE ABBREVIATION OF 'STUDENT ONLINE PROJECT'.
//NOBODY SHOULD READ\COPY\SELL THE CODES WITHOUT DEVELOPER'S PERMISSION IN ANY WAY.
//THIS PAGE IS SPECIALLY RUNNING FOR SOL
//DEVELOPER INFORMATION:
//DEVELOPER	: Meng Zelin.
//E-MAIL	: SMARTMZL@QQ.COM
//OWNWAY GROUP (C) 2013
//HTTP://WWW.OWNWAY.NET

include_once "inkyun.sdk.php";

$username	=	$_POST['username'];
$token		=	$_POST['token'];
$device		=	$_POST['device'];

//验证token并获取基本信息
$inkyunInstance=new inkyunOpen($username,$token,$device);

if($inkyunInstance->init_status){
	
	//开发者使用自己的方式记录验证信息
	//你可以通过设置cookie或者session来保存数据，也可以通过下面的重定向方式将数据传送给其他页面。
	
	setcookie("INKYUNPLATFORM_USERTOKEN",$token,0,"/");
	setcookie("INKYUNPLATFORM_USERNAME",$username,0,"/");
	
	//重定向到应用主页
	if($device=="mapp")
		die("<script>window.location='".getenv("REDIRECT_MAINPAGE_SUCCESS_MAPP")."'</script>");
	else if($device=="web")
		die("<script>window.location='".getenv("REDIRECT_MAINPAGE_SUCCESS_PC")."'</script>");
	
}else{
	
	//TOKEN或username错误
	$error_reason=$inkyunInstance->init_failed_reason;
	if($device=="mapp")
		die("<script>window.location='".getenv("REDIRECT_MAINPAGE_FAILED_MAPP")."?reason=".$error_reason."'</script>");
	else if($device=="web")
		die("<script>window.location='".getenv("REDIRECT_MAINPAGE_FAILED_PC")."?reason=".$error_reason."'</script>");

}
?>