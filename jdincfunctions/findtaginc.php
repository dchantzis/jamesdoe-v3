<?php
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();	
	$dbobj = new JDDBase();
	$tagVarsArr = array();
	
	######################
	//check if the $_GET table has only the value we want, 
	//and the value is of the type we want
	//returns the value we want trimmed
	if(!isset($_GET['tagid'])){redirects(0,'');}
	$getVarType['tagid'] = "([^0-9]+)";
	$validatedVars = $validator->checkGetVariable(1,0,$getVarType);
	$tagID = $validatedVars['tagid'];
	######################	
	
	$query = "SELECT * FROM tags WHERE type='image' AND tags.id='".$tagID."'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','findpostinc','true');
	$num = @mysql_num_rows($result);
	
	if($num == 0){redirects(0,'');}//if
	else{
		$tagVarsArr['id'] = @mysql_result($result,0,'id');
		$tagVarsArr['name'] = @mysql_result($result,0,'name');
		$tagVarsArr['description'] = @mysql_result($result,0,'description');
	}//else
	
	unset($dbobj);
	unset($validator);
?>