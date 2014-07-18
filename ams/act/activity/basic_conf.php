<?php
//设置一个CSTB中最大记录数
define("MAX_TABLE_CONTENT",5000000);

//设置分配模式RDM|UDDM
define("DISTRIBUTIVE_MODE","RDM");

//设置CSTB格式的MYSQL语句
define("CSTB_MYSQL_STRING","
		ID int(11),
		ActName varchar(128),
		ActTime int(11),
		ActPlace varchar(128),
		ActIntro text,
		SignIndex text,
		UpdateIndex text,
		Tags varchar(128),
		ActType varchar(16),
		CreUser varchar(64),
		BlogID int(11),
		SubSvr int(11)
");

//设置CSTB的数据表引擎
define("CSTB_ENGINE","MYISAM");

//设置CSTB的数据表明前缀
define("CSTB_PREFIX","FD");
?>