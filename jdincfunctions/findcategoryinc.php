<?php
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();	
	$dbobj = new JDDBase();
	$categoryVarsArr = array();

	######################
	//check if the $_GET table has only the value we want, 
	//and the value is of the type we want
	//returns the value we want trimmed
	if(!isset($_GET['categoryid'])){redirects(0,'');}
	$getVarType['categoryid'] = "([^0-9]+)";
	$validatedVars = $validator->checkGetVariable(1,0,$getVarType);
	$categoryID = $validatedVars["categoryid"];
	######################	

	$query = "SELECT * FROM tags WHERE tags.id='".$categoryID."' AND type='album'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','findcategoryinc','true');
	$num = @mysql_num_rows($result);

	if($num == 0){redirects(0,'');}//if
	else{
		$categoryVarsArr['id'] = $categoryID;
		$categoryVarsArr['name'] = @mysql_result($result,0,'name');
		$categoryVarsArr['description'] = @mysql_result($result,0,'description');
		$categoryVarsArr['display_description'] = nl2br(@mysql_result($result,0,'description'));
	}//else
	unset($dbobj);
	unset($validator);
?>