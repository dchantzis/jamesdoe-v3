<?php
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();	
	$dbobj = new JDDBase();
	$albumVarsArr = array();

	######################
	//check if the $_GET table has only the value we want, 
	//and the value is of the type we want
	//returns the value we want trimmed
	if(!isset($_GET['albumid']) || !isset($_GET['categoryid'])){redirects(0,'');}
	$getVarType['albumid'] = "([^0-9]+)";
	$getVarType['categoryid'] = "([^0-9]+)";
	$validatedVars = $validator->checkGetVariable(2,0,$getVarType);
	$albumID = $validatedVars['albumid'];
	$categoryID = $validatedVars["categoryid"];
	######################	

	$query = "SELECT albums.id, albums.name, albums.description, albums.tagid, albums.coverid, albums.creationtimestamp, albums.lastupdatedtimestamp, albums.imagesorder, albums.visibility, tags.name AS categoryname, tags.description AS tagsdescription"
			." FROM albums, tags WHERE albums.tagid = tags.id AND albums.tagid='".$categoryID."' AND albums.id='".$albumID."'; ";
	$query99 = "SELECT images.id AS coverid, images.fileurl AS coverthumb FROM images";
	
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','findalbuminc','true');
	$num = @mysql_num_rows($result);
	$result99 = @mysql_query($query99) or dbErrorHandler(802,mysql_error(),$query99,'php','','','findalbuminc','true');
	$num99 = @mysql_num_rows($result99);

	if($num == 0){redirects(0,'');}//if
	else{
		$albumVarsArr['id'] = $albumID;
		$albumVarsArr['name'] = @mysql_result($result,0,'name');
		$albumVarsArr['description'] = @mysql_result($result,0,'description');
		$albumVarsArr['display_description'] = nl2br(@mysql_result($result,0,'description'));
		$albumVarsArr['tagid'] = @mysql_result($result,0,'tagid');
		$albumVarsArr['tagname'] = @mysql_result($result,0,'categoryname');
		$albumVarsArr['coverid'] = @mysql_result($result,0,'coverid');
		$albumVarsArr['coverthumb'] = "defaultcover.png";
		$albumVarsArr['imagesorder'] = @mysql_result($result,0,'imagesorder');
		$albumVarsArr['visibility'] = @mysql_result($result,0,'visibility');
		$albumVarsArr['tagsdescription'] = @mysql_result($result,0,'tagsdescription');
		$albumVarsArr['creationtimestamp'] = convertTimeStamp(@mysql_result($result,0,'creationtimestamp'),'full');
		$albumVarsArr['lastupdatedtimestamp'] = convertTimeStamp(@mysql_result($result,0,'lastupdatedtimestamp'),'full');
		$albumVarsArr['albumcoverfullpath'] = './jdimages/thumbnails/'.$albumVarsArr['coverthumb'];
		
		if(($albumVarsArr['visibility']=='false')&&(adminLoggedIn()==0)){redirects(0,'');}
		//if(($albumVarsArr['tagsdescription']=='postsalbum')&&(DISPLAY_POSTS_IMAGES=='false')){redirects(0,'');}
		
		if( $albumVarsArr['description']=='' || strtolower($albumVarsArr['description'])=="null" )
		{$albumVarsArr['description']='(type a description)';}
				
		if($num99!=0){for($i=0; $i<$num99; $i++){if($albumVarsArr['coverid']==@mysql_result($result99,$i,'coverid')){
			$albumVarsArr['coverthumb']=@mysql_result($result99,$i,'coverthumb');
			$albumVarsArr['albumcoverfullpath'] = './jdimages/thumbnails/'.$albumVarsArr['coverthumb'];}}}
	}//else
	unset($dbobj);
	unset($validator);
?>