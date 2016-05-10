<?php
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();	
	$dbobj = new JDDBase();
	$profileVarsArr = array();
	
	$profileID = 0;
	
	$query = "SELECT * FROM profiles WHERE id=".$profileID."; ";
	
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','findprofileinc','true');
	$num = @mysql_num_rows($result);

	if($num == 0){redirects(0,'');}//if
	else{
		$profileVarsArr['id'] = $profileID;
		$profileVarsArr['username'] = @mysql_result($result,0,'username');
		$profileVarsArr['email'] = @mysql_result($result,0,'email');
		$profileVarsArr['blog'] = @mysql_result($result,0,'blog');
		$profileVarsArr['welcomepagetext'] = @mysql_result($result,0,'welcomepagetext');
		$profileVarsArr['display_welcomepagetext'] = nl2br(@mysql_result($result,0,'welcomepagetext'));
		$profileVarsArr['information'] = @mysql_result($result,0,'information');
		$profileVarsArr['display_information'] = nl2br(@mysql_result($result,0,'information'));
	
		if( $profileVarsArr['welcomepagetext']=='' || strtolower($profileVarsArr['welcomepagetext'])=="null" )
			{$profileVarsArr['welcomepagetext']='(type some welcoming text)';}
		if( $profileVarsArr['information']=='' || strtolower($profileVarsArr['information'])=="null" )
			{$profileVarsArr['information']='(type your profile information)';}
		
		if( $profileVarsArr['display_welcomepagetext']=='' || strtolower($profileVarsArr['display_welcomepagetext'])=="null" )
			{$profileVarsArr['display_welcomepagetext']='';}
		if( $profileVarsArr['display_information']=='' || strtolower($profileVarsArr['display_information'])=="null" )
			{$profileVarsArr['display_information']='';}
			
		$explodedArr = explode('@',$profileVarsArr['email'],2);
		$explodedArr[0] = explode('.',$explodedArr[0],10);
		$explodedArr[1] = explode('.',$explodedArr[1],10);
		
		$counter=0;
		reset($explodedArr[0]);
		while (list($key, $val) = each ($explodedArr[0])){
			$counter++; 
			if(count($explodedArr[0])>1){if($counter==count($explodedArr[0])){$emailAddress.=$val;} else{$emailAddress.=$val." <span class='highlight'>dot</span> ";}}
			else{$emailAddress.=$val;}
		}
		$emailAddress.=" <span class='highlight'>at</span> ";
		$counter=0;
		reset($explodedArr[1]);
		while (list($key, $val) = each ($explodedArr[1])){
			$counter++;
			if(count($explodedArr[1])>1){if($counter==count($explodedArr[1])){$emailAddress.=$val;}else{$emailAddress.=$val." <span class='highlight'>dot</span> ";}}
			else{$emailAddress.=$val;}
		}
		
		
	}//else

	unset($dbobj);
	unset($validator);
?>