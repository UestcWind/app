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

include_once "easy_achieve.php";
include_once "connect.cls.php";
include_once "dbconnect.cls.php";
include_once "Service.php";

$analyze_query=new ConnectLAN;
$return=$analyze_query->executeQueryLAN();

die(rstr_encode($return));
?>