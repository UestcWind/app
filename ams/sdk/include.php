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

//设置masterservice.inkyun.com
putenv( "MASTERSERVICE_REQUIREURL=http://117.121.25.49/0master/entrence/q_open.php" );

//设置require.php页面的重定向URL

putenv( "REDIRECT_MAINPAGE_SUCCESS_PC=index.php" );
putenv( "REDIRECT_MAINPAGE_FAILED_PC=error.php" );

putenv( "REDIRECT_MAINPAGE_SUCCESS_MAPP=index.php" );
putenv( "REDIRECT_MAINPAGE_FAILED_MAPP=error.php" );

//设置APP的SECRETKEY和APPID

putenv( "INKYUN_APP_APPID=S1-0-4");
putenv( "INKYUN_APP_SECRETKEY=733c9a94031f154760ebf8ca07054dad1858bf981bce312e");
?>