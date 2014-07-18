<?php
$conn=mysql_connect('localhost','root','925');
if(!$conn){
	die('数据库连接失败,原因:'.mysql_error());
}
$sql1='CREATE DATABASE IF NOT EXISTS actAPP;';
$sql2='USE actAPP;';
$sql3='CREATE TABLE IF NOT EXISTS `Activity` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ActName` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ActTime` int(11) NOT NULL,
  `ActPlace` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `ActIntro` text COLLATE utf8_unicode_ci NOT NULL,
  `SignIndex` text COLLATE utf8_unicode_ci NOT NULL,
  `UpdateIndex` text COLLATE utf8_unicode_ci NOT NULL,
  `Tags` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ActType` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `CreUser` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `BlogID` int(11) NOT NULL,
  `SubSvr` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;';

$sql4='CREATE TABLE IF NOT EXISTS `Index` (
  `IndexID` int(11) NOT NULL AUTO_INCREMENT,
  `IndexType` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `IndexList` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`IndexID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;';

$sql5='CREATE TABLE IF NOT EXISTS `Mark` (
  `ActID` int(11) NOT NULL,
  `CareID` text COLLATE utf8_unicode_ci NOT NULL,
  `Mark` text COLLATE utf8_unicode_ci NOT NULL,
  `Average` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `ActID` (`ActID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';


$sql6='CREATE TABLE IF NOT EXISTS `SignItem` (
  `SignID` int(11) NOT NULL,
  `SignName` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `SignDetail` text COLLATE utf8_unicode_ci NOT NULL,
  `ActID` int(11) NOT NULL,
  `SignStatus` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `SignLimit` int(11) NOT NULL,
  `SignNums` int(11) NOT NULL,
  `SignUsersIndex` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

$sql7='CREATE TABLE IF NOT EXISTS `Update` (
  `ActID` int(11) NOT NULL,
  `UpdateID` int(11) NOT NULL,
  `Content` text COLLATE utf8_unicode_ci NOT NULL,
  `CreateDate` int(11) NOT NULL,
  `images` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `videos` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`UpdateID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';


$sql8='CREATE TABLE IF NOT EXISTS `User` (
  `UserName` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `SignIndex` text COLLATE utf8_unicode_ci NOT NULL,
  `CareIndex` text COLLATE utf8_unicode_ci NOT NULL,
  `RemarkIndex` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `UserName` (`UserName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
for($i=0;$i<8;$i++){
	$sql='sql'.($i+1);
	$sql=$$sql;
	$q=mysql_query($sql);
	$status=0;
	if(!$q){
		echo '第',$i+1,'个sql语句出错了!原因：'.mysql_error();
		$status=1;
	}
}
if(!$status)
echo '创建成功了';

?>