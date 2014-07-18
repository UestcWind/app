<?php
include_once dirname(__FILE__)."/inkyun.sdk.php";

//实例化INKYUN_APP
$inkyunInstance=new inkyunOpen($_COOKIE['INKYUNPLATFORM_USERNAME'],$_COOKIE['INKYUNPLATFORM_USERTOKEN'],"web");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>INKYUN SDK SAMPLE</title>
</head>

<body>

<div style="margin:20px; font-size:25px; text-align:center;">INKYUN APP AUTORIZATION SAMPLE</div>

<table cellpadding="5" border="1" width="70%" align="center" style="max-width:600px;">
    <tr><td colspan="2"><strong>BASIC INFORMATION：</strong></td></tr>
    <tr><td>init_status</td><td><?php echo $inkyunInstance->init_status ?></td></tr>
    <tr><td>init_failed_reason</td><td><?php echo $inkyunInstance->init_failed_reason ?></td></tr>
    <tr><td colspan="2"><strong>AUTHORIZATION INFORMATION：</strong></td></tr>
    <tr><td>APPID</td><td><?php echo $inkyunInstance->app_id ?></td></tr>
    <tr><td>device</td><td><?php echo $inkyunInstance->device ?></td></tr>
    <tr><td>user_token</td><td><?php echo $inkyunInstance->user_token ?></td></tr>
    <tr><td colspan="2"><strong>USER INFORMATION：</strong></td></tr>
    <tr><td>user_name</td><td><?php echo $inkyunInstance->user_name ?></td></tr>
    <tr><td>user_realname</td><td><?php echo $inkyunInstance->user_realname ?></td></tr>
    <tr><td>user_stuid</td><td><?php echo $inkyunInstance->user_stuid ?></td></tr>
    <tr><td>user_campus</td><td><?php echo $inkyunInstance->user_campus ?></td></tr>
    <tr><td>user_campus_title</td><td><?php echo $inkyunInstance->user_campus_title ?></td></tr>
    <tr><td>user_college</td><td><?php echo $inkyunInstance->user_college ?></td></tr>
    <tr><td>user_college_title</td><td><?php echo $inkyunInstance->user_college_title ?></td></tr>
    <tr><td>user_sex</td><td><?php echo $inkyunInstance->user_sex ?></td></tr>
    <tr><td>user_icon</td><td><?php echo $inkyunInstance->user_icon ?></td></tr>
    <tr><td colspan="2"><strong>OTHER FUNCTIONS EXAMPLE：</strong></td></tr>
    <tr><td>userGetAdvInfo(USER006)</td><td><?php echo json_encode($inkyunInstance->inkyun("USER006",array())); ?></td></tr>
</table>

</body>
</html>