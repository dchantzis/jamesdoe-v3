<?php
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$displayImageUpdatePosts='visible';
	if((adminLoggedIn()==0)||(!isset($_SESSION['mmeditcommentsVisited']))){$settingsArr = loadSettings(); $displayImageUpdatePosts = $settingsArr['postsimagesupdates'];}
	if($displayImageUpdatePosts=='visible'){$sqlFragment = "";}
	elseif($displayImageUpdatePosts=='invisible'){$sqlFragment = " WHERE type='newspost' ";}//else

	$validator = new Validate();	
	$dbobj1 = new JDDBase();
	$postVarsArr = array();
	$postImagesCategoryID=''; 
	$postImagesAlbumID='';
	
	if(isset($_GET['mostrecent'])){$query = "SELECT * FROM posts ".$sqlFragment." ORDER BY creationtimestamp DESC; ";}
	else
	{
		######################
		//check if the $_GET table has only the value we want, 
		//and the value is of the type we want
		//returns the value we want trimmed
		if(!isset($_GET['postid'])){redirects(0,'');}
		$getVarType['postid'] = "([^0-9]+)";
		$validatedVars = $validator->checkGetVariable(1,0,$getVarType);
		$postID = $validatedVars["postid"];
		######################	
		$query = "SELECT * FROM posts ".$sqlFragment." ORDER BY creationtimestamp DESC; "; //$query = "SELECT * FROM posts WHERE posts.id='".$postID."'; ";
	}

	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','findpostinc','true');
	$num = @mysql_num_rows($result);

	################
	/*
	#####USED TO NEED THIS TO KNOW TO WHICH ALBUM AND CATEGORY TO REDIRECT FOR IMAGE DISPLAY
	#####DON'T NEED IT ANYMORE, IMAGES CAN CHANGE ALBUMS AND CATEGORIES
	#####if this code fragment is used, then the <span>s at the bottom of posts.php should be used too.
	
	//GET THE CategoryID and the AlbumID that store the images of a newspost
	$query01 = "SELECT id FROM tags WHERE tags.type='album' AND tags.description='postsalbum'; ";
	$result01 = @mysql_query($query01) or die("error in query "+$query01);
	$num01 = @mysql_num_rows($result01);
	if($num01!=0)
	{
		$postImagesCategoryID = @mysql_result($result01,0,'id');
		$query02 = "SELECT id FROM albums WHERE albums.tagid='".$postImagesCategoryID."'; ";
		$result02 = @mysql_query($query02) or die("error in query "+$query02);
		$num02 = @mysql_num_rows($result02);
		if($num02!=0){$postImagesAlbumID = @mysql_result($result02,0,'id');}
	}
	*/
	################
	if($num == 0){redirects(0,'');}//if
	else{
		if(isset($_GET['mostrecent']))
			{if($num<3){}else{$num=7;}}
		for($i=0; $i<$num; $i++)
		{
			if(isset($_GET['mostrecent'])){$tempPostID=0; $postID=0;}
			else{$tempPostID = @mysql_result($result,$i,'id');}
			
			if($tempPostID == $postID)
			{
				$selectedPostPointer = $i;
				//$postVarsArr[$i]['id'] = $postID; //selected post ID
				$postVarsArr[$i]['id'] = @mysql_result($result,$i,'id');
				$prevPostID = @mysql_result($result,($i+1),'id');//previous post ID
				$nextPostID = @mysql_result($result,($i-1),'id');//next post ID
			
				$postVarsArr[$i]['headline'] = @mysql_result($result,$i,'headline');
				$postVarsArr[$i]['body'] = @mysql_result($result,$i,'body');
				$postVarsArr[$i]['display_body'] = nl2br(@mysql_result($result,$i,'body'));
				$postVarsArr[$i]['type'] = @mysql_result($result,$i,'type');
				$postVarsArr[$i]['tags'] = @mysql_result($result,$i,'tags');
				$postVarsArr[$i]['images'] = @mysql_result($result,$i,'images');
				$postVarsArr[$i]['submitiontimestamp'] = convertTimeStamp(@mysql_result($result,$i,'submitiontimestamp'),'full');
				$postVarsArr[$i]['creationtimestamp'] = convertTimeStamp(@mysql_result($result,$i,'creationtimestamp'),'full');
				$postVarsArr[$i]['shortcreationtimestamp'] = convertTimeStamp(@mysql_result($result,$i,'creationtimestamp'),'shortdaynmonth');
				$postVarsArr[$i]['reallylongcreationtimestamp'] = convertTimeStamp(@mysql_result($result,$i,'creationtimestamp'),'reallylong');
				$postVarsArr[$i]['commentscounter'] = '0';
				$postVarsArr[$i]['commentscounterphrase'] = 'comments'; 
				
				$postVarsArr[$i]['imageslargethumbnails'] = getXHTMLPostsImages($postVarsArr[$i]['images'],'largethumbnails');
				$postVarsArr[$i]['imagesthumbnails'] = getXHTMLPostsImages($postVarsArr[$i]['images'],'thumbnails');
				//$postVarsArr['body'] = strReplaceCount('<br />','<br />'.$postVarsArr['images'],$postVarsArr['body'],1);
				if($postVarsArr[$i]['body']=='' || strtolower($postVarsArr[$i]['body'])=='null'){$postVarsArr[$i]['body']='';}
				//break;
			}//if
		}
	}//else
	
	$dbobj2 = new JDDBase(); //create another element to call the instructor. a connection to the DB is called from the function getXHTMLPostsImages();
	
	$query03 = "SELECT postid, count(*) AS counter FROM comments GROUP BY postid; ";
	$result03 = @mysql_query($query03) or dbErrorHandler(802,mysql_error(),$query03,'php','','','findpostinc','true');
	$num03 = @mysql_num_rows($result03);
	if($num03!=0)
	{
		for($i=0; $i<$num03; $i++)
		{
			$tempPostID = @mysql_result($result03,$i,'postid');
			$commentsCounter = @mysql_result($result03,$i,'counter');
			for($j=0; $j<$num; $j++)
			{
				if($tempPostID == $postVarsArr[$j]['id'])
					{if($commentsCounter==1){$postVarsArr[$j]['commentscounterphrase']='comment'; }
					$postVarsArr[$j]['commentscounter'] = $commentsCounter;}
			}
		}//for
	}//if
	reset($postVarsArr);	
	
	
	if(!isset($_GET['mostrecent']))
		{ $num=1; $swapArr = $postVarsArr[$selectedPostPointer]; unset($postVarsArr); $postVarsArr[0] = $swapArr; unset($swapArr); }
	
	unset($dbobj1); unset($dbobj2);
	unset($validator);

?>