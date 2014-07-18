<?php
//require "sdk/setup.php";
require '../../sdc/SDC.sdk.php';

function rstr_encode(array $array){//返回值编码
	$return_str=isset($_GET['callback'])?$_GET['callback']."(".json_encode($array).")":json_encode($array);
	return $return_str;
}

$op=$_GET['op'];

switch($op){
	case "signup":
		$res=UserEngine::signup();
		$return=$res;
		break;
	case "viewActsByCampus":			
		isset($_GET['page'])?$page=$_GET['page']:$page=1;
		//isset($_GET['type'])?$type=$_GET['type']:$type='campus-'.$mycampus;
		$res1=UserEngine::viewActs($page);
		$return=$res1;
		break;
	case "viewMySigns":
		$res1=$newtool->query("S0036",array("unit_id"=>$_GET['unit_id'],"username"=>$_GET["username"],"auth"=>$_GET["auth"]),NULL);
		$return=$res1;
		break;
	case "viewMyCares":
		$res1=$newtool->query("S0035",array("unit_id"=>$_GET['unit_id'],"method"=>$_GET["method"],"username"=>$_GET["username"],"auth"=>$_GET["auth"]),NULL);
		$return=$res1;
		break;
	case "viewMyRemarks":
		$res1=$newtool->query("S0028",array("unit_id"=>$_GET['unit_id']),NULL);
		$return=$res1;
		break;
		
	case "modifyMySignIndex":
		$res1=$newtool->query("S0032",array("unit_id"=>$_GET['unit_id']),NULL);
		$return=$res1;
		break;
	case "modifyMyCareIndex":
		$res1=$newtool->query("S0027",array("unit_id"=>$_GET['unit_id']),NULL);
		$unitname=$res1["result"]["unitname"];
		$college_=$res1["result"]["college"];
		$father_unit=$res1["result"]["father_unit"];
		$unit_brief=$res1["result"]["unit_brief"];
		if($college_<>""){$res2=$newtool->query("S0016",array("abbr"=>$college_),NULL);$college=$res2["name"];
			}else{ $college="未指定学院";}
		$res3=$newtool->query("S0031",array("unit_id"=>$_GET['unit_id']),NULL);
		if($res1["s"]=="0") $return=array("s"=>"0","r"=>$res1["r"]);
		else if($res2["s"]=="0") $return=array("s"=>"0","r"=>$res2["r"]);
		else if($res3["s"]=="0") $return=array("s"=>"0","r"=>$res3["r"]);
		else {$return=array("s"=>"1","college"=>$college,"unitname"=>$unitname,"unit_brief"=>$unit_brief,"father_unit"=>$father_unit,"auth"=>$res3["auth"]);}
		break;
	case "modifyMyRemarkIndex":
		$res1=$newtool->query("S0027",array("unit_id"=>$_GET['unit_id']),NULL);
		$res2=$newtool->query("S0029",array("unit_id"=>$_GET['unit_id'],"username"=>$_GET['username']),NULL);
		if($res2["s"]=="0") $return=array("s"=>"0","r"=>$res2["r"]);
		else if($res1["s"]=="0") $return=array("s"=>"0","r"=>$res1["r"]);
		else {$unitname=$res1["result"]["unitname"];$return=array("s"=>"1","unitname"=>$unitname,"auth"=>$res2["auth"]);}
		break;
		
		
	case "addAct":
		$res1=$newtool->query("S0022",NULL,NULL);
		$return=$res1;
		break;
	case "modifyAct":
		$res1=$newtool->query("S0034",array("unit_id"=>$_GET['unit_id']),NULL);
		$return=$res1;
		break;
	case "deleteAct":
		$res1=$newtool->query("S0018",array("name"=>$_GET['name'],"iconsrc"=>$_GET['iconsrc'],"admin"=>$_GET['admin']),NULL);
		$return=$res1;
		break;
	case "viewActById":
		$res1=$newtool->query("S0025",array("father_unit"=>$_GET['father_unit'],"college"=>$_GET['college']),NULL);
		$return=$res1;
		break;
	case "viewActs":
		$res1=$newtool->query("S0033",array("name"=>$_GET['name'],"brief"=>$_GET['unit_brief'],"tag"=>$_GET['unit_tag'],"father_unit"=>$_GET['father_unit'],"college"=>$_GET['college']),NULL);
		$return=$res1;
		break;
		
	case "viewSignItems":
		if($_GET['college']=="ALLCOLLEGE"){$college="未指定学院";$display="unit_college";$res3=$newtool->query("S0022",NULL,NULL);
			}else{ $res1=$newtool->query("S0016",array("abbr"=>$_GET['college']),NULL);$college=$res1["name"];$display="college_display";}
		if($_GET['father_unit']=="root" or $_GET['father_unit']==""){$unitname="根目录";
			}else{ $res2=$newtool->query("S0027",array("unit_id"=>$_GET['father_unit']),NULL);$unitname=$res2["result"]["unitname"];}

		$return=array("s"=>"1","college"=>$college,"unitname"=>$unitname,"display"=>$display,"colleges"=>$res3);
		break;
	case "modifySignIndex":
		$res1=$newtool->query("S0033",array("name"=>$_GET['name'],"brief"=>$_GET['unit_brief'],"tag"=>$_GET['unit_tag'],"father_unit"=>$_GET['father_unit'],"college"=>$_GET['college']),NULL);
		$return=$res1;
		break;
	case "viewUpdates":
		$res1=$newtool->query("S0033",array("name"=>$_GET['name'],"brief"=>$_GET['unit_brief'],"tag"=>$_GET['unit_tag'],"father_unit"=>$_GET['father_unit'],"college"=>$_GET['college']),NULL);
		$return=$res1;
		break;
	case "modifyUpdateIndex":
		$res1=$newtool->query("S0033",array("name"=>$_GET['name'],"brief"=>$_GET['unit_brief'],"tag"=>$_GET['unit_tag'],"father_unit"=>$_GET['father_unit'],"college"=>$_GET['college']),NULL);
		$return=$res1;
		break;
		
		
	case "viewActs":
		$res1=$newtool->query("S0033",array("name"=>$_GET['name'],"brief"=>$_GET['unit_brief'],"tag"=>$_GET['unit_tag'],"father_unit"=>$_GET['father_unit'],"college"=>$_GET['college']),NULL);
		$return=$res1;
		break;
	case "viewActs":
		$res1=$newtool->query("S0033",array("name"=>$_GET['name'],"brief"=>$_GET['unit_brief'],"tag"=>$_GET['unit_tag'],"father_unit"=>$_GET['father_unit'],"college"=>$_GET['college']),NULL);
		$return=$res1;
		break;										
		
	default:
		$return=array("s"=>"0","r"=>"wrong option");
}
die(rstr_encode($return));
?>