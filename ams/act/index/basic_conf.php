<?php
//设置一个CSTB中最大记录数
define("MAX_TABLE_CONTENT",5000000);

//设置分配模式RDM|UDDM
define("DISTRIBUTIVE_MODE","RDM");

//设置CSTB格式的MYSQL语句
define("CSTB_MYSQL_STRING","
		IndexID int(11),
		IndexType varchar(128),
		IndexList text
");

//设置CSTB的数据表引擎
define("CSTB_ENGINE","MYISAM");

//设置CSTB的数据表明前缀
define("CSTB_PREFIX","FD");
?>