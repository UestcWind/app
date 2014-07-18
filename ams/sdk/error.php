<?php
include_once dirname(__FILE__)."inkyun.sdk.php";

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
    <tr><td>ERROR DESCRIPTION</td><td><?php echo $_GET['reason'] ?></td></tr>

</table>

</body>
</html>