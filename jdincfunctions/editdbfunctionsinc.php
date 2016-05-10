<?php
###################################################
############editdbfunctionsinc.php#################
###################################################

function editCategories($editType, $validationType)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$explodeArr = array();
	
	// read validation type (PHP or AJAX?)
	//if (isset($_GET['validationType'])){ $validationType = $_GET['validationType']; unset($_GET['validationType']); }
	//unset($_SESSION['errorformvalues']);
	$errorCode = NULL;
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,$validationType); exit;}
	else {} //all ok

	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'editcategory', $_POST['pageid'])){errorHandler(702,$validationType); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok

	//the array $arVals stores the names of all the values of the form
	$arVals = array("name"=>"");			
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("name"=>"");
	//the array $arValsMaxSize stores the names of all the values of the form and the maximum size that each value is allowed to have
	$arValsMaxSize = array("name"=>18);
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with		
	$arValsValidations = array();

	//IMPORTANT for AJAX validation
	if( isset($_POST['inputValue']) && isset($_POST['fieldID']) )
	{
		$inputValue = $_POST['inputValue'];
		$fieldID = $_POST['fieldID'];
		$categoryID = $_POST['ncategoryID'];
		unset($_POST['inputValue']);
		unset($_POST['fieldID']);
		unset($_POST['ncategoryID']);
		
		$explodeArr = explode('_',$fieldID,3);
		$_POST[$explodeArr[1]] = trim($inputValue);
	}//if
	#####
	$inputValue = (get_magic_quotes_gpc()) ? $inputValue : addslashes($inputValue);
	$inputValue = htmlentities($inputValue, ENT_QUOTES, "UTF-8");
	$inputValue = trim($inputValue); 
	#####
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "NULL";} 
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);

		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}// $temp = "NULL"; }
		else{ $_SESSION['formvalues'][$key] = strtolower($val); }// $temp = strtolower($val); }
		
		$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
	}//while
	if($validationType == "ajax")
	{
		$errorCode = $validator->ValidateAJAX($inputValue, $explodeArr[1], $arValsRequired, $arValsMaxSize, $arValsValidations);
		if($errorCode != 0){validateNsubmitXMLresponse($errorCode,$fieldID,'','','category','');}
		
		if($errorCode==0)
		{
			if($arVals['name']!="''")
			{
				$query = "SELECT id AS categoryid, COUNT(*) AS count FROM tags WHERE name=".$arVals['name']." AND type='album' GROUP BY categoryid; ";
				$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'category','true');			
				$resultid = @mysql_result($result,0,'count');				
				if(@mysql_result($result,0,'categoryid')==$categoryID){$resultid=0;}
			}else{$resultid=0;}
				
			if((int)$resultid!=0) { unset($dbobj); validateNsubmitXMLresponse(104,$fieldID,'','','category','');}
			else
			{
				if($editType=='update')
				{
					//Update Album
					$query = "UPDATE tags"
							. " SET name=".$arVals['name']
							. " WHERE id='".$categoryID."'; ";
					$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'category','true');
					unset($dbobj);
					unset($_SESSION['MAINNAVI']); //resets the main navigation options
					$entryVals['entryType']='routine'; $entryVals['valueType']='editCategories'; $entryVals['message']='Updated category.';//: '.$arVals['name'].'.'; 
					editUsersActionLog('insert', $entryVals);
					validateNsubmitXMLresponse(0, $fieldID, $inputValue,'','category','');
				}//if
				elseif($editType=='insert')
				{	
					$arVals['type'] = "'".'album'."'";
					$arVals['description']="''";
					$insert_query = $dbobj->createInsertQuery("tags", $arVals);
					$insertID = $dbobj->executeInsertQuery($insert_query);
					
					unset($dbobj);
					unset($_SESSION['MAINNAVI']); //resets the main navigation options
					$entryVals['entryType']='routine'; $entryVals['valueType']='editCategories'; $entryVals['message']='Inserted category.';//: '.$arVals['name'].'.';
					editUsersActionLog('insert', $entryVals);
					validateNsubmitXMLresponse(0, $fieldID, $inputValue, $insertID,'category','');
				}//elseif
			}//else
		}//if($errorCode==0)
	}//if($validationType == "ajax")
	elseif($validationType == "php"){}//do nothing

}//editCategories($editType, $validationType)

function editAlbums($editType, $validationType)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$explodeArr = array();
	
	// read validation type (PHP or AJAX?)
	//if (isset($_GET['validationType'])){ $validationType = $_GET['validationType']; unset($_GET['validationType']); }
	//unset($_SESSION['errorformvalues']);
	$errorCode = NULL;
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,$validationType); exit;}
	else {} //all ok
	
	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST['csrf'], CSRF_PASS_GEN.'editalbum', $_POST['pageid'])){errorHandler(702,$validationType); exit;} //error Redirect()
	else{ unset($_POST['csrf']); unset($_POST['pageid']); }//all ok
	
	//the array $arVals stores the names of all the values of the form
	$arVals = array("name"=>"","description"=>"","tagid"=>"","coverid"=>"","imagesorder"=>"","visibility"=>"","creationtimestamp"=>"","lastupdatedtimestamp"=>"");			
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("name"=>"");
	//the array $arValsMaxSize stores the names of all the values of the form and the maximum size that each value is allowed to have 
	$arValsMaxSize = array("name"=>24,"description"=>ALBUM_DESCRIPTION_MAX_LENGTH);
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.	
	$arValsValidations = array();

	//IMPORTANT for AJAX validation
	if( isset($_POST['inputValue']) && isset($_POST['fieldID']) )
	{	
		$inputValue = $_POST['inputValue'];
		$fieldID = $_POST['fieldID'];
		$categoryID = $_POST['ncategoryID'];
			if(!preg_match("/^[0-9]([0-9]*)/",$categoryID)){errorHandler(703,$validationType); exit;}
		$albumID = $_POST['nalbumID']; //FOR UPDATE PURPOSES ONLY. OTHERWISE ITS EMPTY
			if(!preg_match("/^[0-9]([0-9]*)/",$albumID)){errorHandler(703,$validationType); exit;}
		$updateDate = convertTimeStamp(date("Y-m-d") . " " . date("H:i:s"),'full');
		unset($_POST['inputValue']);
		unset($_POST['fieldID']);
		unset($_POST['ncategoryID']);
		unset($_POST['nalbumID']);
		
		$arVals['description'] = "";
		$arVals['tagid'] = $categoryID;
		$arVals['coverid'] = 0;
		$arVals['imagesorder'] = 0;
		$arVals['visibility'] = 'true';
		$arVals['creationtimestamp'] = date("Y-m-d") . " " . date("H:i:s"); 
		$arVals['lastupdatedtimestamp'] = date("Y-m-d") . " " . date("H:i:s");

		$explodeArr = explode('_',$fieldID,3);
		$_POST[$explodeArr[1]] = trim($inputValue);
	}//if
	#####
	$inputValue = (get_magic_quotes_gpc()) ? $inputValue : addslashes($inputValue);
	$inputValue = htmlentities($inputValue, ENT_QUOTES, "UTF-8");
	$inputValue = trim($inputValue); 
	#####
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "NULL";}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		
		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}// $temp = "NULL"; }
		else
		{ 
			if($key!='description'){$_SESSION['formvalues'][$key] = strtolower($val);} 
			else{$_SESSION['formvalues'][$key] = $val;}
		}// $temp = strtolower($val); }
	}//while
	
	reset($arVals);
	while (list($key, $val) = each ($arVals))
	{
		if($key!='description'){$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";}
		else{$arVals[$key] = "'" . $arVals[$key] . "'";}
	}//while
	
	if($validationType == "ajax")
	{
		$errorCode = $validator->ValidateAJAX($inputValue, $explodeArr[1], $arValsRequired, $arValsMaxSize, $arValsValidations);
		if($errorCode != 0){validateNsubmitXMLresponse($errorCode,$fieldID,'','','album','');}
		
		if($errorCode==0)
		{	
			//check if the category exists in the DB
			$query = "SELECT COUNT(*) AS count FROM tags WHERE id=".$arVals['tagid']." AND type='album'; ";
			$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'album','true');		
			$resultid = @mysql_result($result,0,'count');
			
			if((int)$resultid==0) { unset($dbobj); validateNsubmitXMLresponse(205,$fieldID,$inputValue,'','album','');}
			else
			{
				//check if the album name already appears in the DB for that category
				if($arVals['name']!="''")
				{
					$query = "SELECT tagid, COUNT(*) AS count, id AS albumid FROM albums WHERE name=".$arVals['name']." AND tagid=".$arVals['tagid']." GROUP BY tagid; ";
					$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'album','true');
					$resultid = @mysql_result($result,0,'count');
					if(@mysql_result($result,0,'albumid')==$albumID){$resultid=0;}
				}else{$resultid=0;}
				
				if((int)$resultid!=0) { unset($dbobj); validateNsubmitXMLresponse(201,$fieldID,'','','album','');}
				else
				{				
					if($editType=='update')
					{
						switch($explodeArr[1])
						{
							case 'description':
								if($arVals['description']=="'(type a description)'"){$arVals['description']="''";}
								
								$query = "UPDATE albums"
									. " SET description=".$arVals['description']
									. " , lastupdatedtimestamp=".$arVals['lastupdatedtimestamp']
									. " WHERE id='".$albumID."' AND tagid=".$arVals['tagid']."; ";
							break;
							case 'name':
								$query = "UPDATE albums"
									. " SET name=".$arVals['name']
									. " , lastupdatedtimestamp=".$arVals['lastupdatedtimestamp']
									. " WHERE id='".$albumID."' AND tagid=".$arVals['tagid']."; ";
							break;
							case 'coverid':
								$query = "UPDATE albums"
									. " SET coverid=".$arVals['coverid']
									. " , lastupdatedtimestamp=".$arVals['lastupdatedtimestamp']
									. " WHERE id='".$albumID."' AND tagid=".$arVals['tagid']."; ";
							break;
							case 'imagesorder':
								$query = "UPDATE albums"
									. " SET imagesorder=".$arVals['imagesorder']
									. " , lastupdatedtimestamp=".$arVals['lastupdatedtimestamp']
									. " WHERE id='".$albumID."' AND tagid=".$arVals['tagid']."; ";
							break;
							case 'tagid':
								$query00 = "UPDATE images"
										. " SET albumtagid=".$arVals['tagid']
										. " WHERE albumid='".$albumID."'; ";
								$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'ajax',$inputValue,$fieldID,'album','true');
								
								$query = "UPDATE albums"
									. " SET tagid=".$arVals['tagid']
									. " , lastupdatedtimestamp=".$arVals['lastupdatedtimestamp']
									. " WHERE id='".$albumID."'; ";
							break;
							default: break;
						}//switch
						$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'album','true');
						
						unset($dbobj);
						unset($_SESSION['MAINNAVI']); //resets the main navigation options
						$entryVals['entryType']='routine'; $entryVals['valueType']='editAlbums'; $entryVals['message']='Updated album.';//: '.$arVals['name'].'.';
						editUsersActionLog('insert', $entryVals);
						validateNsubmitXMLresponse(0,$fieldID,$inputValue,'','album',$updateDate);
					}//if
					elseif($editType=='insert')
					{
						//create the insert query
						$insert_query = $dbobj->createInsertQuery("albums", $arVals);					
						//execute the query
						$insertID = $dbobj->executeInsertQuery($insert_query);
						unset($dbobj);
						unset($_SESSION['MAINNAVI']); //resets the main navigation options
						$entryVals['entryType']='routine'; $entryVals['valueType']='editAlbums'; $entryVals['message']='Inserted album.';//: '.$arVals['name'].'.';
						editUsersActionLog('insert', $entryVals);
						validateNsubmitXMLresponse(0,$fieldID,$inputValue,$insertID,'album',$updateDate);
					}//elseif
				}//else
			}//else
		}//if($errorCode==0)
	}//if($validationType == "ajax")
	elseif($validationType == "php"){}//do nothing
}//editAlbums($editType, $validationType)


//uploadImages()
function uploadImages()
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$file = array();
	$arVals = array();
	$uploadImagesResults = array();
	$errorCode = NULL;
	$albumID = NULL;
	$categoryID = NULL;
	$redirectPage = NULL;
	$uploadedImageIDs = '';
	$pageID = $_POST["pageid"];
	$postID = $_POST["postid"]; unset($_POST["postid"]);
	$newImagesOrder = 0;
	
	if($pageID=='mmeditalbums'){$redirectPage=11;}
	elseif($pageID=='mmeditposts'){$redirectPage=12;}
	elseif($pageID=='mmeditwelcomepage'){$redirectPage=19;}
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,'php'); exit;}
	else{} //all ok
		
	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'uploadimage', $_POST['pageid'])){errorHandler(702,'php'); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
	
	$albumID = $_POST['albumid']; unset($_POST['albumid']);
	$categoryID = $_POST['categoryid']; unset($_POST['categoryid']);
	
	reset($_FILES);
	$tempcounter=0;
	while (list($key, $val) = each ($_FILES))
	{	
		$errorCode = $validator->uploadFilesErrorMessages($_FILES[$key]['error']);
		if($tempcounter==5 || $errorCode==194){unset($_FILES[$key]);}
		$tempcounter++;
	}//while
	
	//if this flag turns to '1' then there's at least 1 image to be uploaded. Else Abort.
	reset($_FILES);
	$temp_flag = 0;
	while (list($key, $val) = each ($_FILES))
	{
		$uploadImagesResults[$key]['name'] = $_FILES[$key]['name'];
		$uploadImagesResults[$key]['size'] = $_FILES[$key]['size'];
		
		$errorCode = $validator->uploadFilesErrorMessages($_FILES[$key]['error']);
		if($errorCode == 0){$temp_flag = 1;}//all ok
		else{ $uploadImagesResults[$key]['error'] = $errorCode; unset($_FILES[$key]);}//error
	}//while
	//if(!$temp_flag){echo '2'; exit;}//{redirects($redirectPage,'?flg='.$errorCode);}
	
	//find which image file formats are supported
	$query00 = "SELECT id, extension, mimetype FROM fileformats";
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'php','','','','true');
	$num00 = @mysql_num_rows($result00);//num

	if(isset($num00))
	{
		for($i=0; $i<$num00; $i++)
		{
			$accepted_file_formats[$i]['id'] = mysql_result($result00,$i,'id');
			$accepted_file_formats[$i]['extension'] = mysql_result($result00,$i,'extension');
			$accepted_file_formats[$i]['mimetype'] = mysql_result($result00,$i,'mimetype');
		}//
	}//if
	else{redirects($redirectPage,'?flg=106');} //No file types in the database//else
	
	reset($_FILES);
	while (list($key, $val) = each ($_FILES))
	{
		$temp = 0;
		if($_FILES[$key]['error']!=0)
		{
			$uploadImagesResults[$key]['error'] = $_FILES[$key]['error'];
			unset($_FILES[$key]);
			continue;
		}//{continue;}
		for($j=0; $j<count($accepted_file_formats); $j++)
		{
			if(IMAGES_FF_CHECK == 'extension')
			{
				if($accepted_file_formats[$j]['extension'] == find_file_extension($_FILES[$key]['name']))
				{
					$temp = 1;//image file type is accepted
					$arVals[$key]['formatid'] = $accepted_file_formats[$j]["id"];
				}//
			}//if
			elseif(IMAGES_FF_CHECK == 'mimetype')
			{
				if($accepted_file_formats[$j]['mimetype'] == $_FILES[$key]['type'])
				{
					$temp = 1;//image file type is accepted
					$arVals[$key]['formatid'] = $accepted_file_formats[$j]['id'];
				}//	
			}//else
		}//for
		if($temp == 0){ $uploadImagesResults[$key]['error'] = 105; unset($_FILES[$key]);} // { redirects($redirectPage,'?flg=105'); }// //file type not accepted.
	}//while

	//overide the results of the previous while if the global variable PRESERVE_ORIGINAL_IMAGE_FILETYPE is set to 'false'
	if(strtolower(PRESERVE_ORIGINAL_IMAGE_FILETYPE) == 'false')
	{
		reset($arVals);
		while(list($key,$val) = each($arVals))
		{
			for($j=0; $j<count($accepted_file_formats); $j++)
			{if(UPLOADED_IMAGES_FILETYPE == $accepted_file_formats[$j]['extension']){$arVals[$key]['formatid'] = $accepted_file_formats[$j]['id'];}}
		}//
	}//


	###########################
	if($pageID == 'mmeditposts')
	{
		//if this is an images uploaded from the page mmeditposts.
		//Check if a category for all the posts images exists. If it doesn't then create it.
		$check_query01 = "SELECT id AS categoryid FROM tags WHERE type='album' AND description='postsalbum'; ";
		$check_result01 = @mysql_query($check_query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','','true');
		$check_num01 = @mysql_num_rows($check_result01);
		
		if($check_num01==0)
		{
			//CREATE IT
			$tempArr['name'] = "'"."posts images"."'";
			$tempArr['description'] = "'postsalbum'";
			$tempArr['type'] = "'".'album'."'";
			$insertquery = $dbobj->createInsertQuery("tags", $tempArr); 
			$insertid = $dbobj->executeInsertQuery($insertquery);
			$categoryID = $insertid;
		}
		else{$categoryID = @mysql_result($check_result01,0,'categoryid');}
		
		//Check if an album for all the posts images exists. If it doesn't then create it.
		$check_query02 = "SELECT id AS albumid FROM albums WHERE tagid='".$categoryID."' ; ";
		$check_result02 = @mysql_query($check_query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','','true');
		$check_num02 = @mysql_num_rows($check_result02);
		unset($tempArr);
		if($check_num02==0)
		{
			//CREATE IT
			$tempArr['name'] = "posts images";
			$tempArr['description'] = "posts images album";
			$tempArr['tagid'] = $categoryID;
			$tempArr['coverid'] = 0;
			$tempArr['imagesorder'] = 0;
			$tempArr['visibility'] = 'false';
			$tempArr['creationtimestamp'] = date("Y-m-d") . " " . date("H:i:s");
			$tempArr['lastupdatedtimestamp'] = date("Y-m-d") . " " . date("H:i:s");
			reset($tempArr); while (list($key, $val) = each ($tempArr)){$tempArr[$key] = "'" . strtolower($tempArr[$key]) . "'";}//while
			
			$insertquery = $dbobj->createInsertQuery("albums", $tempArr);
			$insertid = $dbobj->executeInsertQuery($insertquery);
			$albumID = $insertid;
			$entryVals['entryType']='routine'; $entryVals['valueType']='uploadImages'; $entryVals['message']='Inserted new post image.';
		}
		else{$albumID = @mysql_result($check_result02,0,'albumid');}
	}//if($pageID == 'mmeditposts')
	###########################
	###########################
	###########################
	if($pageID == 'mmeditwelcomepage')
	{
		//if this is an images uploaded from the page mmeditposts.
		//Check if a category for all the posts images exists. If it doesn't then create it.
		$check_query01 = "SELECT id AS categoryid FROM tags WHERE type='album' AND description='welcomepageimagesalbum'; ";
		$check_result01 = @mysql_query($check_query01) or dbErrorHandler(802,mysql_error(),$check_result01,'php','','','','true');
		$check_num01 = @mysql_num_rows($check_result01);
		
		if($check_num01==0)
		{
			//CREATE IT
			$tempArr['name'] = "'"."welcome page images"."'";
			$tempArr['description'] = "'welcomepageimagesalbum'";
			$tempArr['type'] = "'".'album'."'";
			$insertquery = $dbobj->createInsertQuery("tags", $tempArr); 
			$insertid = $dbobj->executeInsertQuery($insertquery);
			$categoryID = $insertid;
		}
		else{$categoryID = @mysql_result($check_result01,0,'categoryid');}
		
		//Check if an album for all the posts images exists. If it doesn't then create it.
		$check_query02 = "SELECT id AS albumid FROM albums WHERE tagid='".$categoryID."' ; ";
		$check_result02 = @mysql_query($check_query02) or dbErrorHandler(802,mysql_error(),$check_result02,'php','','','','true');
		$check_num02 = @mysql_num_rows($check_result02);
		unset($tempArr);
		if($check_num02==0)
		{
			//CREATE IT
			$tempArr['name'] = "welcome page images";
			$tempArr['description'] = "welcome page images album";
			$tempArr['tagid'] = $categoryID;
			$tempArr['coverid'] = 0;
			$tempArr['imagesorder'] = 0;
			$tempArr['visibility'] = 'false';
			$tempArr['creationtimestamp'] = date("Y-m-d") . " " . date("H:i:s");
			$tempArr['lastupdatedtimestamp'] = date("Y-m-d") . " " . date("H:i:s");
			reset($tempArr); while (list($key, $val) = each ($tempArr)){$tempArr[$key] = "'" . strtolower($tempArr[$key]) . "'";}//while
			
			$insertquery = $dbobj->createInsertQuery("albums", $tempArr);
			$insertid = $dbobj->executeInsertQuery($insertquery);
			$albumID = $insertid;
			
			$entryVals['entryType']='routine'; $entryVals['valueType']='uploadImages'; $entryVals['message']='Inserted new welcome page image.';
		}
		else{$albumID = @mysql_result($check_result02,0,'albumid');}
	}//if($pageID == 'mmeditwelcomepage')
	###########################
	
	
	reset($_FILES);
	while (list($key, $val) = each ($_FILES))
	{
		if(IMAGES_UPLOAD_TYPE == 'database')
		{	
			$field_name = $key; //name of they type=file in the form
			$file = upload_to_database($field_name,$redirectPage); //function returns array
		
			$arVals[$key]['name'] = "'NULL'";
			$arVals[$key]['tags'] = "'NULL'";
			$arVals[$key]['dateofcreation'] = "'NULL'";
			$arVals[$key]['description'] = "'NULL'";
			$arVals[$key]['submitiontimestamp'] = "'" . date("Y-m-d") . " " . date("H:i:s") . "'";
			$arVals[$key]['albumid'] = "'" . $albumID . "'";
			$arVals[$key]['formatid'] = "'" . $arVals[$key]['formatid'] . "'";
			$arVals[$key]['filename'] = "'" . $file['filename'] . "'";
			$arVals[$key]['filesize'] = "'" . $file['filesize'] . "'";
			$arVals[$key]['filecontent'] = "'" . $file['filecontent'] . "'";
			$arVals[$key]['fileurl'] = "'NULL'";
			$arVals[$key]['albumtagid'] = "'".$categoryID."'";
			$arVals[$key]['uploadtype'] = "'" . 'database' . "'";		
		}//if database
		if(IMAGES_UPLOAD_TYPE == 'fileserver')
		{	
			$upload_directory = IMAGES_UPLOAD_DIR;
			$partial_file_name = ""; //removeExtension($_FILES[$key]['name']) . "_";
			$field_name = $key; //name of they type=file in the form
			$file = uploadToFileserver($field_name,$upload_directory,$partial_file_name,$redirectPage,'image'); //Upload the file. Function returns array
		
			if(isset($file['error'])){ $uploadImagesResults[$key]['error'] = $file['error']; continue; }
			
			$arVals[$key]['name'] = "''";
			$arVals[$key]['tags'] = "''";
			$arVals[$key]['dateofcreation'] = "''";

			$arVals[$key]['description'] = "''";
			$arVals[$key]['submitiontimestamp'] = "'" . date("Y-m-d") . " " . date("H:i:s") . "'";
			$arVals[$key]['albumid'] = "'" . $albumID . "'";
			$arVals[$key]['formatid'] = "'" . $arVals[$key]['formatid'] . "'";
			$arVals[$key]['filename'] = "'" . $file['filename'] . "'";
			$arVals[$key]['filesize'] = "'" . $file['filesize'] . "'";
			$arVals[$key]['filecontent'] = "'NULL'";
			$arVals[$key]['fileurl'] = "'" . $file['fileurl'] . "'";
			$arVals[$key]['albumtagid'] = "'".$categoryID."'";
			$arVals[$key]['uploadtype'] = "'" . 'fileserver' . "'";			
		}// fileserver
		$insert_query = $dbobj->createInsertQuery("images", $arVals[$key]); 
		$insert_id = $dbobj->executeInsertQuery($insert_query);
		
		$entryVals['entryType']='routine'; $entryVals['valueType']='uploadImages'; $entryVals['message']='Inserted new image.';
		
		$uploadedImageIDs .= $insert_id.'.::.';
	}//while
	if($uploadedImageIDs!='')
	{
		//UPDATE THE IMAGES ORDER OF THE ALBUM
		$search_query = "SELECT imagesorder FROM albums WHERE id='".$albumID."' AND tagid='".$categoryID."'; ";
		$search_result = @mysql_query($search_query) or dbErrorHandler(802,mysql_error(),$search_result,'php','','','','true');
		$search_num = @mysql_num_rows($search_result);
		
		$newImagesOrder = $uploadedImageIDs;
		if($search_num!=0)
		{
			$oldImagesOrder = mysql_result($search_result,0,'imagesorder'); 
			if(($oldImagesOrder=='0')||($oldImagesOrder=='')){$newImagesOrder=$uploadedImageIDs;}
			else{$newImagesOrder=$oldImagesOrder.$uploadedImageIDs;}
		}
		
		$update_query = "UPDATE albums"
					." SET lastupdatedtimestamp='".date("Y-m-d") . " " . date("H:i:s")."', imagesorder='".$newImagesOrder."'"
					." WHERE id='".$albumID."' AND tagid='".$categoryID."'; ";
		$update_result = @mysql_query($update_query) or dbErrorHandler(802,mysql_error(),$update_result,'php','','','','true');
		
		//CREATE OR UPDATE THE IMAGE POSTS
		if($pageID == 'mmeditposts')
		{
			$selectpostquery = "SELECT images FROM posts WHERE id='".$postID."';";
			$selectpostresult = @mysql_query($selectpostquery) or dbErrorHandler(802,mysql_error(),$selectpostresult,'php','','','','true');
			$selectpostnum = @mysql_num_rows($selectpostresult);
			if($selectpostnum!=0){$uploadedImageIDsOld = @mysql_result($selectpostresult,0,'images');}
			$postsimages = $uploadedImageIDsOld.$uploadedImageIDs;
			$updatepostquery = "UPDATE posts SET images='".$postsimages."', submitiontimestamp='".date("Y-m-d")." ".date("H:i:s")."' WHERE id='".$postID."'; ";
			$updatepostresult = @mysql_query($updatepostquery) or dbErrorHandler(802,mysql_error(),$updatepostresult,'php','','','','true');
		}
		elseif($pageID == 'mmeditwelcomepage'){}//do nothing
		else{editImagesUpdatePosts($uploadedImageIDs);}
	}	

	unset($dbobj);
	editUsersActionLog('insert', $entryVals);
	$_SESSION['uploadImagesResults'] = $uploadImagesResults;
	//save_to_usersactionlog("uploadImages()");
	unset($_SESSION['MAINNAVI']); //resets the main navigation options
	if($pageID == 'mmeditalbums'){redirects($redirectPage,'?albumid='.$albumID.'&categoryid='.$categoryID);}
	elseif($pageID == 'mmeditposts'){redirects($redirectPage,'');}
	elseif($pageID == 'mmeditwelcomepage'){redirects($redirectPage,'');}
}//uploadImages()


function editImages($editType, $validationType)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$explodeArr = array();
	
	// read validation type (PHP or AJAX?)
	//if (isset($_GET['validationType'])){ $validationType = $_GET['validationType']; unset($_GET['validationType']); }
	//unset($_SESSION['errorformvalues']);
	$errorCode = NULL;
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,$validationType); exit;}
	else {} //all ok
	
	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'editimage', $_POST['pageid'])){errorHandler(702,$validationType); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
	
	//the array $arVals stores the names of all the values of the form
	$arVals = array("name"=>"","description"=>"","tags"=>"","albumid"=>"","albumtagid"=>"","album"=>"");		
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("album"=>"");
	//the array $arValsMaxSize stores the names of all the values of the form and the maximum size that each value is allowed to have 
	$arValsMaxSize = array("name"=>25,"description"=>IMAGE_DESCRIPTION_MAX_LENGTH);
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.	
	$arValsValidations = array();

	//IMPORTANT for AJAX validation
	if( isset($_POST['inputValue']) && isset($_POST['fieldID']) )
	{
		$inputValue = $_POST['inputValue'];
		$fieldID = $_POST['fieldID'];
		$imageID = $_POST['nimageID'];
			if(!preg_match("/^[0-9]([0-9]*)/",$imageID)){errorHandler(703,$validationType); exit;}
		$categoryID = $_POST['ncategoryID'];
			if(!preg_match("/^[0-9]([0-9]*)/",$categoryID)){errorHandler(703,$validationType); exit;}
		$albumID = $_POST['nalbumID'];
			if(!preg_match("/^[0-9]([0-9]*)/",$albumID)){errorHandler(703,$validationType); exit;}
		unset($_POST['inputValue']);
		unset($_POST['fieldID']);
		unset($_POST['nimageID']);
		unset($_POST['ncategoryID']);
		unset($_POST['nalbumID']);
		
		$arVals['name'] = "";
		$arVals['description'] = "";
		$arVals['tags'] = "";
		$arVals['albumid'] = $albumID;
		$arVals['albumtagid'] = $categoryID;

		$explodeArr = explode('_',$fieldID,3);
		$_POST[$explodeArr[1]] = trim($inputValue);
	}//if
	#####
	$inputValue = (get_magic_quotes_gpc()) ? $inputValue : addslashes($inputValue);
	$inputValue = htmlentities($inputValue, ENT_QUOTES, "UTF-8");
	$inputValue = trim($inputValue); 
	#####
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "NULL";} 
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		
		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}// $temp = "NULL"; }
		else
		{ 
			if($key!='description'){$_SESSION['formvalues'][$key] = strtolower($val);} 
			else{$_SESSION['formvalues'][$key] = $val;}
		}// $temp = strtolower($val); }
	}//while
	
	reset($arVals);
	while (list($key, $val) = each ($arVals))
	{
		if($key!='description'){$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";}
		else{$arVals[$key] = "'" . $arVals[$key] . "'";}
	}//while

	if($validationType == "ajax")
	{
		$errorCode = $validator->ValidateAJAX($inputValue, $explodeArr[1], $arValsRequired, $arValsMaxSize, $arValsValidations);
		if($errorCode != 0){validateNsubmitXMLresponse($errorCode,$fieldID,'','','image','');}
		
		if($errorCode==0)
		{
			//check if the image name already appears in the DB for that category
			if($arVals['name']!="''")
			{
				$query = "SELECT COUNT(*) AS count, id AS imageid FROM images WHERE name=".$arVals['name']." GROUP BY imageid; ";
				$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'image','true');
				$resultid = @mysql_result($result,0,'count');
				if(@mysql_result($result,0,'imageid')==$imageID){$resultid=0;}
			}else{$resultid=0;}
				
			if((int)$resultid!=0) { unset($dbobj); validateNsubmitXMLresponse(202,$fieldID,'','','image','');}
			else
			{
				if($arVals['name']=="'(type a name)'"){$arVals['name']="''";}
				if($arVals['description']=="'(type a description)'"){$arVals['description']="''";}
				
				switch($explodeArr[1])
				{
					case 'description':
						$query = "UPDATE images"
						. " SET description=".$arVals['description']
						. " WHERE id='".$imageID."' AND albumid=".$arVals['albumid']." AND albumtagid=".$arVals['albumtagid']."; ";
					break;
					case 'name':
						$query = "UPDATE images"
						. " SET name=".$arVals['name']
						. " WHERE id='".$imageID."' AND albumid=".$arVals['albumid']." AND albumtagid=".$arVals['albumtagid']."; ";
					break;
					case 'album':
						$query00 = "SELECT albums.tagid AS category, albums.imagesorder AS imagesorder FROM albums WHERE albums.id=".$arVals['album']."; ";
						$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'ajax',$inputValue,$fieldID,'image','true');
						$newImageCategory = "'".mysql_result($result00,0,'category')."'";
						$newImagesOrder = "'".mysql_result($result00,0,'imagesorder').$imageID.".::."."'";
						
						if($arVals['album']=='0'){$arVals['album']=$arVals['albumid']; $newImageCategory=$arVals['albumtagid'];}
						
						$query001 = "UPDATE albums SET imagesorder=".$newImagesOrder." WHERE id=".$arVals['album']."; ";
						$result001 = @mysql_query($query001) or die("error in query "+$query001);
						
						$query = "UPDATE images"
							. " SET albumid=".$arVals['album'].", albumtagid=".$newImageCategory
							. " WHERE id='".$imageID."' AND albumid=".$arVals['albumid']." AND albumtagid=".$arVals['albumtagid']."; ";
							
					break;
					case 'tags':
						if($arVals['tags']=="'null'"){$arVals['tags']='';}				
						$arVals['tags']=substr($arVals['tags'],1,-1);
						if(substr($arVals['tags'],-1)!=','){$arVals['tags'].=',';}
						$explodeTagsArr = explode(',',$arVals['tags']);
						unset($explodeTagsArr[sizeof($explodeTagsArr)-1]);
						$explodeTagsArr = array_unique($explodeTagsArr);
						sort($explodeTagsArr);
						
						$tagsIDArr = array(); //the IDs of the tags to be assigned to the image
						$tempTagsDB = array();

						$query00 = "SELECT id, name FROM tags WHERE type='image' ORDER BY name ASC;";
						$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'ajax',$inputValue,$fieldID,'image','true');
						$num00 = @mysql_num_rows($result00);
						
						if($num00!=0)
						{
							for($i=0; $i<$num00; $i++)
							{
								$tempTagsDB[$i]['id']=mysql_result($result00,$i,'id');
								$tempTagsDB[$i]['name']= trim(strtolower(mysql_result($result00,$i,'name')));
							}//for
						}//if	
						
						$k=0;
						$num09 = sizeof($explodeTagsArr);
						for($j=0; $j<$num09; $j++)
						{
							for($i=0; $i<$num00; $i++)
							{
								if(trim(strtolower($explodeTagsArr[$j]))==$tempTagsDB[$i]['name'])
								{
									$tagsIDArr[$k] = $tempTagsDB[$i]['id']; $k++; unset($explodeTagsArr[$j]);
								}//if
							}
						}//for
						
						//add the new tags in the DB
						reset($explodeTagsArr);
						while (list($key, $val)=each ($explodeTagsArr))
						{	
							if(trim($val)==''){continue;}
							$tempArVals['name']="'".trim(strtolower($val))."'";
							$tempArVals['description']="''";
							$tempArVals['type']="'image'";
												
							$insert_query = $dbobj->createInsertQuery("tags", $tempArVals);
							$insertID = $dbobj->executeInsertQuery($insert_query);
							$tagsIDArr[$k] = $insertID; $k++;
						}//
						
						$arVals['tags']='';
						$tagsIDArr = array_unique($tagsIDArr);
						sort($tagsIDArr);
						for($i=0; $i<sizeof($tagsIDArr); $i++){ $arVals['tags'] .= $tagsIDArr[$i].'.::.'; }
						//if($arVals['tags']==''){}
						$arVals['tags']="'".$arVals['tags']."'";
						
						$query = "UPDATE images"
							. " SET tags=".$arVals['tags']
							. " WHERE id='".$imageID."' AND albumid=".$arVals['albumid']." AND albumtagid=".$arVals['albumtagid']."; ";
					break;
					default:
					break;
				}//switch
				
				$result = @mysql_query($query) or die("error in query "+$query);
					
				$albumUpdateDate = date("Y-m-d")." ".date("H:i:s");
				$update_query = "UPDATE albums SET lastupdatedtimestamp='".$albumUpdateDate."' WHERE id=".$arVals['albumid']." AND tagid=".$arVals['albumtagid']."; ";
				$update_result = @mysql_query($update_query) or dbErrorHandler(802,mysql_error(),$update_query,'ajax',$inputValue,$fieldID,'image','true');
				
				if($explodeArr[1]=='album')
				{
					$update_query = "UPDATE albums SET lastupdatedtimestamp='".$albumUpdateDate."' WHERE id=".$arVals['album']." AND tagid=".$newImageCategory."; ";
					$update_result = @mysql_query($update_query) or dbErrorHandler(802,mysql_error(),$update_query,'ajax',$inputValue,$fieldID,'image','true');
				}
				
				unset($dbobj);
				
				$entryVals['entryType']='routine'; $entryVals['valueType']='editImages'; $entryVals['message']='Updated image.';//: '.$arVals['name'].'.';
				editUsersActionLog('insert', $entryVals);
				
				validateNsubmitXMLresponse(0,$fieldID,$inputValue,'','image',convertTimeStamp($albumUpdateDate,'full'));
			}//else
		}//if($errorCode==0)
	}//if($validationType == "ajax")
	elseif($validationType == "php"){}//do nothing
}//editImages($editType, $validationType)

function deleteImages()
{
	require_once('jddbase.class.php');
	require_once('validate.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$imageID = $_POST['imageid'];
		if(!preg_match("/^[0-9]([0-9]*)/",$imageID)){errorHandler(703,$validationType); exit;}
	$albumID = $_POST['albumid'];
		if(!preg_match("/^[0-9]([0-9]*)/",$albumID)){errorHandler(703,$validationType); exit;}
	$categoryID = $_POST['categoryid'];
		if(!preg_match("/^[0-9]([0-9]*)/",$categoryID)){errorHandler(703,$validationType); exit;}
	$pageID = $_POST['pageid'];
	$coverChange = '';
	$newAlbumImagesOrderChange = '';
	
	$errorCode = NULL;
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'deleteimage', $_POST['pageid'])){errorHandler(702,'ajax'); exit;}
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
		
	$arVals = array("imageid"=>"","albumid"=>"","categoryid"=>"");

	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "";}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
	}//while

	//FIND THE IMAGE
	$query = "SELECT * FROM images WHERE id=".$arVals['imageid']." AND albumid=".$arVals['albumid']." AND albumtagid=".$arVals['categoryid']."; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true');
	$num = @mysql_num_rows($result);
	if(!isset($result)||($num==0)){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true'); exit;}//error in query OR image not found
	else{$imageURL = @mysql_result($result,0,'fileurl');}
	//DELETE IMAGE FROM FILESERVER
	$status = deleteFromFileserver($imageURL);
	if($status){}//all ok
	else{unset($dbobj); unset($validator); errorHandler(602,'ajax'); exit;}//error unable to delete image from fileserver
	
	//DELETE IMAGE FROM DATABASE
	$query = "DELETE FROM images WHERE id=".$arVals['imageid']." AND albumid=".$arVals['albumid']." AND albumtagid=".$arVals['categoryid']."; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true');

	//CHECK IF THE IMAGE WE ARE DELETING IS THE ALBUM COVER
	$check_query00 = "SELECT count(*) AS count FROM albums, images WHERE albums.coverid = images.id AND images.id=".$arVals['imageid'].";";
	$check_result00 =  @mysql_query($check_query00) or dbErrorHandler(802,mysql_error(),$check_query00,'ajax','','','','true');
	$check_num00 = @mysql_num_rows($check_result00);
	if($check_num00==0){if(mysql_result($check_result00,0,'count')!=0); $coverChange=", coverid='0'"; }
	

	//GET the images order from the album
	$check_query01 = "SELECT imagesorder FROM albums WHERE id=".$arVals['albumid']."; ";
	$check_result01 = @mysql_query($check_query01) or dbErrorHandler(802,mysql_error(),$check_query01,'ajax','','','','true');
	$check_num01 = @mysql_num_rows($check_result01);
	if($check_num01!=0)
	{
		$oldAlbumImagesOrder=mysql_result($check_result01,0,'imagesorder');
		if($oldAlbumImagesOrder!=0)
		{	
			$oldAlbumImagesOrder = str_replace($imageID.".::.",'',$oldAlbumImagesOrder);
			$newAlbumImagesOrderChange =", imagesorder='".$oldAlbumImagesOrder."'";
		}
	}

	//UPDATE ALBUMS
	$albumUpdateDate = date("Y-m-d")." ".date("H:i:s");
	$update_query = "UPDATE albums SET lastupdatedtimestamp='".$albumUpdateDate."'".$coverChange.$newAlbumImagesOrderChange." WHERE id=".$arVals['albumid']." AND tagid=".$arVals['categoryid']."; ";
	$update_result = @mysql_query($update_query) or dbErrorHandler(802,mysql_error(),$update_query,'ajax','','','','true');

	unset($dbobj);
	unset($validator);
	if($result==1){
		$value = $imageID.'.::.'.$albumID.'.::.'.$categoryID;
		unset($_SESSION['MAINNAVI']); //resets the main navigation options
		
		$entryVals['entryType']='routine'; $entryVals['valueType']='deleteImages'; $entryVals['message']='Deleted image.';
		editUsersActionLog('insert', $entryVals);
		
		deleteElementsXMLresponse($value,'image',$pageID); exit;
	} //all ok
	else{dbErrorHandler(802,mysql_error(),$update_query,'ajax','','','','true'); exit;}//error with the query
}//deleteImages()
function deleteAlbums()
{
	require_once('jddbase.class.php');
	require_once('validate.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$albumID = $_POST['albumid'];
		if(!preg_match("/^[0-9]([0-9]*)/",$albumID)){errorHandler(703,$validationType); exit;}
	$categoryID = $_POST['categoryid'];
		if(!preg_match("/^[0-9]([0-9]*)/",$categoryID)){errorHandler(703,$validationType); exit;}
	$pageID = $_POST['pageid'];

	$errorCode = NULL;
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'deletealbum', $_POST['pageid'])){errorHandler(702,'ajax'); exit;}
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
		
	$arVals = array("albumid"=>"","categoryid"=>"");

	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "";}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
	}//while
	
	//FIND THE IMAGES OF THE ALBUM
	$query = "SELECT id, fileurl FROM images WHERE albumid=".$arVals['albumid']." AND albumtagid=".$arVals['categoryid']."; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true');
	$num = @mysql_num_rows($result);
	if(!isset($result)){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true'); exit;}//error in query abort all actions
	elseif($num==0){}//no images in the database
	else
	{
		if(isset($num)&&$num!=0)
		{
			for($i=0; $i<$num; $i++)
			{
				$imageID = @mysql_result($result,$i,'id');
				$arVals['imageid'] = "'".$imageID."'";
				$imageURL = @mysql_result($result,$i,'fileurl');
					
				//DELETE IMAGES FROM FILESERVER
				$status = deleteFromFileserver($imageURL);
				if($status){}//all ok
				else{$errorCode=602;}//error unable to delete image from fileserver
				//DELETE IMAGES FROM DATABASE
				$deletequery = "DELETE FROM images WHERE id=".$arVals['imageid']." AND albumid=".$arVals['albumid']." AND albumtagid=".$arVals['categoryid']."; ";
				$deleteresult = @mysql_query($deletequery) or dbErrorHandler(802,mysql_error(),$deletequery,'ajax','','','','true');
				if($deleteresult==1){}//all ok
				else{$errorCode=802; $errorQuery=$deletequery;}//error with the query
			}
		}
		else{}//do nothing
	}
	//DELETE ALBUM FROM DATABASE
	if($errorCode!=NULL)
	{
		unset($dbobj); unset($validator);
		if($errorCode==602){errorHandler(603,'ajax'); exit;}
		elseif($errorCode==802){errorHandler(603,'ajax'); exit;}
	}//error
	else
	{
		$query = "DELETE FROM albums WHERE id=".$arVals['albumid']." AND tagid=".$arVals['categoryid']."; ";
		$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true');
	
		unset($dbobj);
		unset($validator);
		if($result==1){
			$value = $albumID.'.::.'.$categoryID;
			unset($_SESSION['MAINNAVI']); //resets the main navigation options
			$entryVals['entryType']='routine'; $entryVals['valueType']='deleteAlbums'; $entryVals['message']='Deleted Album.';
			editUsersActionLog('insert', $entryVals);
			deleteElementsXMLresponse($value,'album',$pageID); exit;
		} //all ok
		else{dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true'); exit;}//error with the query
	}//else
}//deleteAlbums($albumID)
function deleteCategories()
{
	require_once('jddbase.class.php');
	require_once('validate.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$categoryID = $_POST['categoryid'];
		if(!preg_match("/^[0-9]([0-9]*)/",$categoryID)){errorHandler(703,$validationType); exit;}
	$pageID = $_POST['pageid'];

	$errorCode = NULL;
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'deletecategory', $_POST['pageid'])){errorHandler(702,'ajax'); exit;}
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
		
	$arVals = array("categoryid"=>"");

	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "";}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
	}//while

	//FIND THE ALBUMS OF THE CATEGORY
	$query00 = "SELECT id FROM albums WHERE tagid=".$arVals['categoryid']."; ";
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'ajax','','','','true');
	$num00 = @mysql_num_rows($result00);
	if(!isset($result00)){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query00,'ajax','','','','true'); exit;}//error in query abort all actions
	elseif($num00==0){}//no albums in the database
	else
	{
		if(isset($num00)&&$num00!=0)
		{
			for($i=0; $i<$num00; $i++)
			{
				$albumID = @mysql_result($result00,$i,'id');
				$arVals['albumid'] = "'".$albumID."'";

				//FIND THE IMAGES OF THE CATEGORY
				$query = "SELECT id, fileurl FROM images WHERE albumid=".$arVals['albumid']." AND albumtagid=".$arVals['categoryid']."; ";
				$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true');
				$num = @mysql_num_rows($result);			

				if(!isset($result)){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true'); exit;}//error in query abort all actions			
				if($num==0){}//no images in the database
				else
				{
					if(isset($num)&&$num!=0)
					{
						for($j=0; $j<$num; $j++)
						{
							$imageID = @mysql_result($result,$j,'id');
							$arVals['imageid'] = "'".$imageID."'";
							$imageURL = @mysql_result($result,$j,'fileurl');
									
							//DELETE IMAGES FROM FILESERVER
							$status = deleteFromFileserver($imageURL);
							if($status){}//all ok
							else{$errorCode=602;}//error unable to delete image from fileserver
							//DELETE IMAGES FROM DATABASE
							$deletequery = "DELETE FROM images WHERE id=".$arVals['imageid']." AND albumid=".$arVals['albumid']." AND albumtagid=".$arVals['categoryid']."; ";
							$deleteresult = @mysql_query($deletequery) or dbErrorHandler(802,mysql_error(),$deletequery,'ajax','','','','true');
							if($deleteresult==1){}//all ok
							else{$errorCode=802; $errorQuery=$deletequery;}//error with the query			
						}//for
					}//if
					else{}//do nothing
				}//else


				//DELETE THE ALBUM FROM THE DATABASE
				if($errorCode!=NULL)
				{
					unset($dbobj); unset($validator);
					if($errorCode==602){errorHandler(603,'ajax'); exit;}
					elseif($errorCode==802){errorHandler(603,'ajax'); exit;}
				}//error
				else
				{
					$deletequery = "DELETE FROM albums WHERE id=".$arVals['albumid']." AND tagid=".$arVals['categoryid']."; ";
					$deleteresult = @mysql_query($deletequery) or dbErrorHandler(802,mysql_error(),$deletequery,'ajax','','','','true');
					if($deleteresult==1){} //all ok
					else{$errorCode=802; $errorQuery=$deletequery;}//error with the query
				}//else

			}//for
			//$errorCode = NULL; 
		}//if
		else{}//do nothing
	}//else
	$deletequery00 = "DELETE FROM tags WHERE id=".$arVals['categoryid']." AND type='album'; ";
	$deleteresult00 = @mysql_query($deletequery00) or dbErrorHandler(802,mysql_error(),$deletequery00,'ajax','','','','true');
	unset($dbobj);
	unset($validator);
	unset($_SESSION['MAINNAVI']); //resets the main navigation options
	if($deleteresult00==1)
	{
		$entryVals['entryType']='routine'; $entryVals['valueType']='deleteCategories'; $entryVals['message']='Deleted Category.';
		editUsersActionLog('insert', $entryVals);		
		$value=$categoryID;		
		deleteElementsXMLresponse($value,'category',$pageID); exit;
	} //all ok
	else{dbErrorHandler(802,mysql_error(),$deletequery00,'ajax','','','','true'); exit;}//error with the query
}//deleteCategories($categoryID)

function editImagesUpdatePosts($uploadedImageIDs)
{
	require_once('jddbase.class.php');

	$dbobj = new JDDBase();
	$nowDate = date("Y-m-d");
	$nowTimestamp = date("Y-m-d")." ".date("H:i:s");
	$explodeDateArr = array();
	$explodeImageIDsArr = array();
	$dbImageIDs = array();
	$createNewPostEntry = 0;
	
	$arVals = array("headline"=>"","body"=>"","type"=>"","tags"=>"","images"=>"","creationtimestamp"=>"","submitiontimestamp"=>"");			
	
	$query01 = "SELECT * FROM posts WHERE type='imagesupdate' ORDER BY creationtimestamp DESC; ";
	$query02 = "SELECT id FROM images ORDER BY id ASC;";
	
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','','true');
	$num01 = @mysql_num_rows($result01);
	
	if($num01==0){$createNewPostEntry=1;}//create new entry
	elseif($num01!=0)
	{
		$tempPostID = mysql_result($result01,0,'id');
		$postVars[$tempPostID]['headline'] = mysql_result($result01,0,'headline');
		$postVars[$tempPostID]['body'] = mysql_result($result01,0,'body');
		$postVars[$tempPostID]['type'] = mysql_result($result01,0,'type');
		$postVars[$tempPostID]['tags'] = mysql_result($result01,0,'tags');
		$postVars[$tempPostID]['images'] = mysql_result($result01,0,'images');
		$postVars[$tempPostID]['creationtimestamp'] = mysql_result($result01,0,'creationtimestamp');
		$postVars[$tempPostID]['submitiontimestamp'] = mysql_result($result01,0,'submitiontimestamp');
		
		$explodeDateArr = explode(' ',$postVars[$tempPostID]['submitiontimestamp']);
		
		if($explodeDateArr[0]!=$nowDate){$createNewPostEntry=1;}//create new entry
		elseif($explodeDateArr[0]==$nowDate)
		{
			//update entry
			$createNewPostEntry=0;
			$explodeImageIDsArr = explode('.::.',$postVars[$tempPostID]['images']);
			
			$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','','true');
			$num02 = @mysql_num_rows($result02);
			
			if($num02==0){}//do nothing
			elseif($num02!=0)
			{
				for($i=0; $i<$num02; $i++){$tempDBimageID = mysql_result($result02,$i,'id'); $dbImageIDs[$tempDBimageID]=1;}//for
				reset($dbImageIDs);
				for($j=0; $j<count($explodeImageIDsArr); $j++)
				{
					if(!isset($dbImageIDs[$explodeImageIDsArr[$j]])){unset($explodeImageIDsArr[$j]);}
				}//for
			}//
			
			$newPostImageIDs='';
			for($h=0; $h<count($explodeImageIDsArr); $h++){if(isset($explodeImageIDsArr[$h])){$newPostImageIDs .= $explodeImageIDsArr[$h].'.::.';}}
			$arVals['images'] = "'".$newPostImageIDs.$uploadedImageIDs."'";
			
			$nowTimestamp = "'".$nowTimestamp."'";
			$update_query = "UPDATE posts"
							. " SET images=".$arVals['images']
							. " , submitiontimestamp=".$nowTimestamp
							. " WHERE id='".$tempPostID."'; ";
			$update_result = @mysql_query($update_query) or dbErrorHandler(802,mysql_error(),$update_query,'php','','','','true');
		}//update entry
	}//
	
	if($createNewPostEntry==1)
	{	
		$arVals["headline"] = "'".'Images Update #'.($num01+1)."'";
		$arVals["body"] = "'".''."'";
		$arVals["type"] = "'".'imagesupdate'."'";
		$arVals["tags"] = "'".''."'";
		$arVals["images"] = "'".$uploadedImageIDs."'";
		$arVals["creationtimestamp"] = "'".$nowTimestamp."'";
		$arVals["submitiontimestamp"] = "'".$nowTimestamp."'";
		
		$insert_query = $dbobj->createInsertQuery("posts", $arVals);
		$insertID = $dbobj->executeInsertQuery($insert_query);
	}
	
	unset($dbobj);
}//editImagesUpdatePosts($uploadedImageIDs)

function editPosts($editType, $validationType)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();

	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,$validationType); exit;}
	else {} //all ok

	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'editpost', $_POST['pageid'])){errorHandler(702,$validationType); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok


	//the array $arVals stores the names of all the values of the form
	$arVals = array("headline"=>"","body"=>"","type"=>"","tags"=>"","images"=>"","submitiontimestamp"=>"","creationtimestamp"=>"");			
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("headline"=>"");
	//the array $arValsMaxSize stores the names of all the values of the form and the maximum size that each value is allowed to have
	$arValsMaxSize = array("headline"=>41,"body"=>POST_BODY_MAX_LENGTH);
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with		
	$arValsValidations = array();

	//IMPORTANT for AJAX validation
	if( isset($_POST['inputValue']) && isset($_POST['fieldID']) )
	{
		$inputValue = $_POST['inputValue'];
		$fieldID = $_POST['fieldID'];
		$postID = $_POST['npostID'];
			if(!preg_match("/^[0-9]([0-9]*)/",$postID)){errorHandler(703,$validationType); exit;}
		$updateDate = convertTimeStamp(date("Y-m-d") . " " . date("H:i:s"),'short');
		unset($_POST['inputValue']);
		unset($_POST['fieldID']);
		unset($_POST['npostID']);
		
		$arVals['body'] = "";
		$arVals['type'] = "newspost";
		$arVals['tags'] = "";
		$arVals['creationtimestamp'] = date("Y-m-d") . " " . date("H:i:s"); 
		$arVals['submitiontimestamp'] = date("Y-m-d") . " " . date("H:i:s");
		
		$explodeArr = explode('_',$fieldID,3);
		$_POST[$explodeArr[1]] = trim($inputValue);
	}//if
	#####
	$inputValue = (get_magic_quotes_gpc()) ? $inputValue : addslashes($inputValue);
	$inputValue = htmlentities($inputValue, ENT_QUOTES, "UTF-8");
	$inputValue = trim($inputValue); 
	#####
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "NULL";} 
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);

		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}// $temp = "NULL"; }
		else
		{ 
			if($key!='body'){$_SESSION['formvalues'][$key] = strtolower($val);} 
			else{$_SESSION['formvalues'][$key] = $val;}
		}// $temp = strtolower($val); }
	}//while

	reset($arVals);
	while (list($key, $val) = each ($arVals))
	{
		if($key!='body'){$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";}
		else{$arVals[$key] = "'" . $arVals[$key] . "'";}
	}//while

	if($validationType == "ajax")
	{
		$errorCode = $validator->ValidateAJAX($inputValue, $explodeArr[1], $arValsRequired, $arValsMaxSize, $arValsValidations);
		if($errorCode != 0){validateNsubmitXMLresponse($errorCode,$fieldID,'','','post','');}
		
		if($errorCode==0)
		{
			if($arVals['headline']!="''")
			{
				$query = "SELECT id AS postid, COUNT(*) AS count FROM posts WHERE headline=".$arVals['headline']." GROUP BY postid; ";
				$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'post','true');
				$resultid = @mysql_result($result,0,'count');
				if(@mysql_result($result,0,'postid')==$postID){$resultid=0;}
			}else{$resultid=0;}
			
			if((int)$resultid!=0) { unset($dbobj); validateNsubmitXMLresponse(104,$fieldID,'','','post','');}
			else
			{
				if($editType=='update')
				{
					//Update Post
					switch($explodeArr[1])
					{
						case 'headline':
							$query = "UPDATE posts"
							. " SET headline=".$arVals['headline']
							. " , submitiontimestamp=".$arVals['submitiontimestamp']
							. " WHERE id='".$postID."'; ";
							break;
						case 'body':
							if($arVals['body']=="'(type your text here)'"){$arVals['body']="''";}
							$query = "UPDATE posts"
							. " SET body=".$arVals['body']
							. " , submitiontimestamp=".$arVals['submitiontimestamp']
							. " WHERE id='".$postID."'; ";
							break;
						default: break;
					}//switch
					$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'post','true');
					
					unset($dbobj);
					$entryVals['entryType']='routine'; $entryVals['valueType']='editPosts'; $entryVals['message']='Updated Post.';//: '.$arVals['headline'].'.';
					editUsersActionLog('insert', $entryVals);	
					validateNsubmitXMLresponse(0, $fieldID, $inputValue,'','post',$updateDate);
				}//if
				elseif($editType=='insert')
				{
					$insert_query = $dbobj->createInsertQuery("posts", $arVals);
					$insertID = $dbobj->executeInsertQuery($insert_query);
					
					unset($dbobj);
					$entryVals['entryType']='routine'; $entryVals['valueType']='editPosts'; $entryVals['message']='Inserted Post.';//: '.$arVals['headline'].'.';
					editUsersActionLog('insert', $entryVals);
					validateNsubmitXMLresponse(0, $fieldID, $inputValue, $insertID,'post',$updateDate);
				}//elseif
			}//else
			
		}//if($errorCode==0)
	}//if($validationType == "ajax")
	elseif($validationType == "php"){}//do nothing
}//editPosts($editType, $validationType)

function deletePosts()
{
	require_once('jddbase.class.php');
	require_once('validate.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$postID = $_POST['postid'];
		if(!preg_match("/^[0-9]([0-9]*)/",$postID)){errorHandler(703,$validationType); exit;}
	$pageID = $_POST['pageid'];
	$explodeImageIDsArr = array();

	$errorCode = NULL;
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'deletepost', $_POST['pageid'])){errorHandler(702,'ajax'); exit;}
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
		
	$arVals = array("postid"=>"");

	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "";}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
	}//while

	//FIND IF A POST WITH THIS ID EXISTS
	$query000 = "SELECT id, images, type FROM posts WHERE posts.id='".$postID."'; ";
	$result000 = @mysql_query($query000) or dbErrorHandler(802,mysql_error(),$query000,'ajax','','','','true');
	$num000 = @mysql_num_rows($result000);
	if(!isset($result000)){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query000,'ajax','','','','true'); exit;}//error in query, abort all actions
	//elseif($num000==0){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query000,'ajax','','','','true'); exit;}//no such post in the database, abort all actions
	else{$postImages = @mysql_result($result000,0,'images'); $explodeImageIDsArr = explode('.::.',$postImages); $postType=@mysql_result($result000,0,'type');}

	//FIND THE CATEGORY THAT STORES THE POSTS IMAGES
	$query00 = "SELECT id FROM tags WHERE tags.description='postsalbum'; ";
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'ajax','','','','true');
	$num00 = @mysql_num_rows($result00);
	if(!isset($result00)){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query00,'ajax','','','','true'); exit;}//error in query abort all actions
	elseif($num00==0){}//no such category in the database
	else
	{
		if(isset($num00)&&$num00!=0)
		{
			$categoryID = @mysql_result($result00,0,'id');
			//FIND THE ALBUM THAT STORES THE POSTS IMAGES
			$query01 = "SELECT id FROM albums WHERE tagid='".$categoryID."'; ";
			$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'ajax','','','','true');
			$num01 = @mysql_num_rows($result01);
			if(!isset($result01)){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query01,'ajax','','','','true'); exit;}//error in query abort all actions
			if($num01==0){}//no images in the database
			else
			{
				if(isset($num01)&&$num01!=0)
				{
					$albumID = @mysql_result($result01,0,'id');
					//FIND THE IMAGES CONNECTED WITH THE POST IN QUESTION
					$query02 = "SELECT id, fileurl FROM images";
					$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'ajax','','','','true');
					$num02 = @mysql_num_rows($result02);
					if(!isset($result02)){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query02,'ajax','','','','true'); exit;}//error in query abort all actions
					if($num02==0){}//no images in the database
					else
					{
						if(isset($num02)&&$num02!=0)
						{
							for($j=0; $j<$num02; $j++)
							{
								$imageID = @mysql_result($result02,$j,'id');
								$imageURL = @mysql_result($result02,$j,'fileurl');
								
								//CHECK IF THIS IMAGE IS ASSOSIATED WITH THE POST IN QUESTION
								for($k=0; $k<count($explodeImageIDsArr); $k++)
								{
									if($imageID==$explodeImageIDsArr[$k])
									{
										if($postType=='newspost')
										{
											//DELETE IMAGES FROM FILESERVER
											$status = deleteFromFileserver($imageURL);
											if($status){}//all ok
											else{$errorCode=602;}//error unable to delete image from fileserver
											//DELETE IMAGES FROM DATABASE
											$deletequery = "DELETE FROM images WHERE id='".$imageID."' AND albumid='".$albumID."' AND albumtagid='".$categoryID."'; ";
											$deleteresult = @mysql_query($deletequery) or dbErrorHandler(802,mysql_error(),$deletequery,'ajax','','','','true');
											if($deleteresult==1){}//all ok
											else{$errorCode=802; $errorQuery=$deletequery;}//error with the query
										}//
									}
									else{}
								}//for			
							}//for		
						}//if
						else{}//do nothing
					}//else
				}//if
			}//else
		}//if
	}//else
	$deletequery00 = "DELETE FROM posts WHERE id='".$postID."'; ";// AND type='newspost'; ";
	$deleteresult00 = @mysql_query($deletequery00) or dbErrorHandler(802,mysql_error(),$deletequery00,'ajax','','','','true');
	unset($dbobj);
	unset($validator);
	if($deleteresult00==1)
	{
		$entryVals['entryType']='routine'; $entryVals['valueType']='deletePosts'; $entryVals['message']='Deleted Post.';
		editUsersActionLog('insert', $entryVals);
		$value=$postID; deleteElementsXMLresponse($value,'post',$pageID); exit;
	} //all ok
	else{dbErrorHandler(802,mysql_error(),$deletequery00,'ajax','','','','true'); exit;}//error with the query
}//deletePosts()

function editComments($editType,$validationType,$postID)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$explodeArr = array();
	$pageID = $_POST['pageid'];
	$errorsVarsArr = array();
	
	// read validation type (PHP or AJAX?)
	//if (isset($_GET['validationType'])){ $validationType = $_GET['validationType']; unset($_GET['validationType']); }
	//unset($_SESSION['errorformvalues']);
	$errorCode = NULL;

	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,$validationType); exit;}
	else {} //all ok

	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'submitcomment', $_POST['pageid'])){errorHandler(702,'ajax'); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
	
	//the array $arVals stores the names of all the values of the form
	$arVals = array("name"=>"","email"=>"","website"=>"","reply"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("name"=>"","email"=>"","reply"=>"");
	//the array $arValsMaxSize stores the names of all the values of the form and the maximum size that each value is allowed to have 
	$arValsMaxSize = array("name"=>"100","email"=>"100","website"=>"100","reply"=>POST_COMMENT_MAX_LENGTH);
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.	
	$arValsValidations = array("email"=>"/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/","website"=>"/(http:\/\/)?([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/");
	
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "NULL";}
		if ($key=='website')
			{if(($val=='your website (not required)')|| ($val=='http://www.'))
				{$val='NULL'; unset($arValsValidations['website']);}}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		
		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}
		else
		{ 
			if($key!='reply'){$_SESSION['formvalues'][$key] = strtolower($val);} 
			else{$_SESSION['formvalues'][$key] = $val;}
		}//
	}//while
	unset($_POST);
	$arVals['submitiontimestamp'] = date("Y-m-d") . " " . date("H:i:s");
	$arVals['postid'] = $postID;
	
	reset($arVals);
	while (list($key, $val) = each ($arVals))
	{
		if($key!='reply'){$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";}
		else{$arVals[$key] = "'" . $arVals[$key] . "'";}
	}//while

	if($validationType == "php")
	{
		$errorsVarsArr = $validator->ValidatePHP($arValsRequired, $arValsMaxSize, $arValsValidations);
		if($errorsVarsArr['errorCode'] != 0)
			{validateNsubmitXMLresponseComments($errorsVarsArr,'','','comments',$pageID);}
	
		if($errorsVarsArr['errorCode'] == 0)
		{
			if($editType=='update'){}//if
			elseif($editType=='insert')
			{
				//create the insert query
				$insert_query = $dbobj->createInsertQuery("comments", $arVals);					
				//execute the query
				$insertID = $dbobj->executeInsertQuery($insert_query);
				unset($dbobj);
				$arVals=removeQuotes($arVals);
				$arVals['reply'] = stripslashes($arVals['reply']);
				//$arVals['reply'] = nl2br($arVals['reply']);
				unset($_SESSION['ADMINSIDEBAR']);
				$entryVals['entryType']='routine'; $entryVals['valueType']='editComments'; $entryVals['message']='Inserted new comment.';
				editUsersActionLog('insert', $entryVals);
				validateNsubmitXMLresponseComments('',$arVals,$insertID,'comments',$pageID);
			}//elseif
		}
	}//if($validationType == "php")
	elseif($validationType == "ajax"){}//do nothing
}//editComments($editType,$validationType,$postID)

function deleteComments()
{
	require_once('jddbase.class.php');
	require_once('validate.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$commentID = $_POST['commentid'];
		if(!preg_match("/^[0-9]([0-9]*)/",$commentID)){errorHandler(703,$validationType); exit;}
	$pageID = $_POST['pageid'];

	$errorCode = NULL;
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'deletecomment', $_POST['pageid'])){errorHandler(702,'ajax'); exit;}
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
		
	$arVals = array("commentid"=>"");

	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "";}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
	}//while

	//FIND IF A POST WITH THIS ID EXISTS
	$query00 = "SELECT id FROM comments WHERE id='".$commentID."'; ";
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','','true');
	$num00 = @mysql_num_rows($result00);
	if(!isset($result00)){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query00,'ajax','','','','true'); exit;}//error in query, abort all actions
	elseif($num00==0){unset($dbobj); unset($validator); dbErrorHandler(802,mysql_error(),$query00,'ajax','','','','true'); exit;}//no such post in the database, abort all actions

	$deletequery00 = "DELETE FROM comments WHERE id='".$commentID."'; ";
	$deleteresult00 = @mysql_query($deletequery00) or dbErrorHandler(802,mysql_error(),$deletequery00,'ajax','','','','true');
	unset($dbobj);
	unset($validator);
	if($deleteresult00==1)
	{
		$entryVals['entryType']='routine'; $entryVals['valueType']='deleteComments'; $entryVals['message']='Deleted comment.';
		editUsersActionLog('insert', $entryVals);
		$value=$commentID;
		deleteElementsXMLresponse($value,'comment',$pageID); exit;
	} //all ok
	else{dbErrorHandler(802,mysql_error(),$deletequery00,'ajax','','','','true'); exit;}//error with the query
}//deleteComments()


function editAlbumVisibility()
{
	require_once('jddbase.class.php');
	require_once('validate.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$validationType = 'ajax';
	
	// read validation type (PHP or AJAX?)
	$errorCode = NULL;
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,$validationType); exit;}
	else {} //all ok

	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'editalbumvisibility', $_POST['pageid'])){errorHandler(702,$validationType); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
	
	//the array $arVals stores the names of all the values of the form
	$arVals = array("visibility"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("visibility"=>"");
	//the array $arValsMaxSize stores the names of all the values of the form and the maximum size that each value is allowed to have 
	$arValsMaxSize = array();
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.	
	$arValsValidations = array();

	//IMPORTANT for AJAX validation
	if( isset($_POST['inputValue']) && isset($_POST['fieldID']) )
	{	
		$inputValue = $_POST['inputValue'];
		$fieldID = $_POST['fieldID'];
		$categoryID = $_POST['ncategoryID'];
			if(!preg_match("/^[0-9]([0-9]*)/",$categoryID)){errorHandler(703,$validationType); exit;}
		$albumID = $_POST['nalbumID'];
			if(!preg_match("/^[0-9]([0-9]*)/",$albumID)){errorHandler(703,$validationType); exit;}
		$visibilityStatus = $_POST['nvisibilityStatus'];
		$updateDate = convertTimeStamp(date("Y-m-d") . " " . date("H:i:s"),'full');
		unset($_POST['inputValue']);
		unset($_POST['fieldID']);
		unset($_POST['ncategoryID']);
		unset($_POST['nalbumID']);
		
		if($visibilityStatus=='makevisible'){$visibilityStatus='true';}
		else if($visibilityStatus=='makeinvisible'){$visibilityStatus='false';}
		
		$explodeArr = explode('_',$fieldID,3);
		$_POST[$explodeArr[1]] = trim($visibilityStatus);
	}//if
	#####
	$inputValue = (get_magic_quotes_gpc()) ? $inputValue : addslashes($inputValue);
	$inputValue = htmlentities($inputValue, ENT_QUOTES, "UTF-8");
	$inputValue = trim($inputValue); 
	#####
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "NULL";}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		
		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}
		else {$_SESSION['formvalues'][$key] = $val;}
	}//while
	
	reset($arVals);
	while (list($key, $val) = each ($arVals))
	{
		$arVals[$key] = "'" . $arVals[$key] . "'";
	}//while
	
	if($validationType == "ajax")
	{
		//check if the category exists in the DB
		$query00 = "SELECT COUNT(*) AS count FROM albums WHERE tagid='".$categoryID."' AND id='".$albumID."'; ";
		$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'ajax',$inputValue,$fieldID,'albumvisibility','true');
		$resultid00 = @mysql_result($result00,0,'count');
		
		//notice the first parameter in validateNsubmitXMLresponse. '303' is for error
		if((int)$resultid00==0) { unset($dbobj); unset($validator); validateNsubmitXMLresponse(303,$fieldID,$inputValue,303,'albumvisibility',$updateDate); exit;}
		else
		{
			$query = "UPDATE albums"
					. " SET visibility=".$arVals['visibility']
					. " WHERE tagid='".$categoryID."' AND id='".$albumID."'; ";
			$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'albumvisibility','true');
			unset($dbobj);
			unset($validator);
			unset($_SESSION['MAINNAVI']); //resets the main navigation options
			$entryVals['entryType']='routine'; $entryVals['valueType']='editAlbumVisibility'; $entryVals['message']='Updated album visibility.';
			editUsersActionLog('insert', $entryVals);
			validateNsubmitXMLresponse(0,$fieldID,$inputValue,0,'albumvisibility',$updateDate);
		}
	}//if($validationType == "ajax")
	elseif($validationType == "php"){}//do nothing
	
}//editAlbumVisibility()

function editSettings()
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$explodeArr = array();
	$validationType = 'ajax';
	
	$errorCode = NULL;
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,$validationType); exit;}
	else {} //all ok
	
	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST['csrf'], CSRF_PASS_GEN.'togglecheckbox', $_POST['pageid'])){errorHandler(702,$validationType); exit;} //error Redirect()
	else{ unset($_POST['csrf']); unset($_POST['pageid']); }//all ok

	//the array $arVals stores the names of all the values of the form
	$arVals = array("postscommentsstatus"=>"","sitehomepage"=>"","postsimagesupdates"=>"","linksstatus"=>"");			
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array();
	//the array $arValsMaxSize stores the names of all the values of the form and the maximum size that each value is allowed to have 
	$arValsMaxSize = array();
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.	
	$arValsValidations = array();

	//IMPORTANT for AJAX validation
	if( isset($_POST['inputValue']) && isset($_POST['fieldID']) )
	{	
		$inputValue = $_POST['inputValue'];
		$fieldID = $_POST['fieldID'];
		$profileID = $_POST['profileID'];
			if(!preg_match("/^[0-9]([0-9]*)/",$profileID)){errorHandler(703,$validationType); exit;}
		$updateDate = convertTimeStamp(date("Y-m-d") . " " . date("H:i:s"),'full');
		unset($_POST['inputValue']);
		unset($_POST['fieldID']);
		unset($_POST['profileID']);
		
		$explodeArr = explode('_',$fieldID,3);
		switch($explodeArr[1])
		{
			case "comments": $fieldName='postscommentsstatus'; $_POST['postscommentsstatus'] = trim($inputValue); break;
			case "homepage": $fieldName='sitehomepage'; $_POST['sitehomepage'] = trim($inputValue); break;
			case "imagesupdates": $fieldName='postsimagesupdates'; $_POST['postsimagesupdates'] = trim($inputValue); break;
			case "links": $fieldName='linksstatus'; $_POST['linksstatus'] = trim($inputValue); break;
			default: break;
		}		
	}//if
	#####
	$inputValue = (get_magic_quotes_gpc()) ? $inputValue : addslashes($inputValue);
	$inputValue = htmlentities($inputValue, ENT_QUOTES, "UTF-8");
	$inputValue = trim($inputValue); 
	#####
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "NULL";}
		switch($key)
		{
			case "postscommentsstatus":
				if($inputValue=='checked'){$val='active';}
				elseif($inputValue=='unchecked'){$val='inactive';}
				break;
			case "sitehomepage":
				if($inputValue=='checked'){$val='newssection';}
				elseif($inputValue=='unchecked'){$val='welcomepage';}
				break;
			case "postsimagesupdates":
				if($inputValue=='checked'){$val='visible';}
				elseif($inputValue=='unchecked'){$val='invisible';}
				break;
			case "linksstatus":
				if($inputValue=='checked'){$val='visible';}
				elseif($inputValue=='unchecked'){$val='invisible';}
				break;
			default: 
				break;
		}		
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		
		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}
		else {$_SESSION['formvalues'][$key] = $val;}
	}//while

	reset($arVals);
	while (list($key, $val) = each ($arVals))
	{
		$arVals[$key] = "'" . $arVals[$key] . "'";
	}//while
	
	if($validationType == "ajax")
	{
		//check if the category exists in the DB
		$query00 = "SELECT COUNT(*) AS count FROM profiles WHERE id='".$profileID."'; ";
		$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'ajax',$inputValue,$fieldID,'togglesettingscheckbox','true');		
		$resultid00 = @mysql_result($result00,0,'count');
		
		//notice the first parameter in validateNsubmitXMLresponse. '304' is for error
		if((int)$resultid00==0) { unset($dbobj); unset($validator); validateNsubmitXMLresponse(304,$fieldID,$inputValue,304,'togglesettingscheckbox',$updateDate); exit;}
		else
		{
			$query = "UPDATE settings"
					. " SET ".$fieldName."=".$arVals[$fieldName]
					. " WHERE profileid='".$profileID."'; ";
			$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax',$inputValue,$fieldID,'togglesettingscheckbox','true');
			unset($dbobj);
			unset($validator);
			unset($_SESSION['MAINNAVI']); //resets the main navigation options
			$entryVals['entryType']='routine'; $entryVals['valueType']='editSettings'; $entryVals['message']='Updated site settings.';
			editUsersActionLog('insert', $entryVals);
			validateNsubmitXMLresponse(0,$fieldID,$inputValue,0,'togglesettingscheckbox',$updateDate);
		}
	}//if($validationType == "ajax")
	elseif($validationType == "php"){}//do nothing
}//editSettings()

function editProfiles()
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$explodeArr = array();
	$validationType = 'ajax';
	$pageID = $_POST['pageid'];
	
	// read validation type (PHP or AJAX?)
	//if (isset($_GET['validationType'])){ $validationType = $_GET['validationType']; unset($_GET['validationType']); }
	//unset($_SESSION['errorformvalues']);
	$errorCode = NULL;
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,$validationType); exit;}
	else {} //all ok
	
	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST['csrf'], CSRF_PASS_GEN.'editprofile', $_POST['pageid'])){errorHandler(702,$validationType); exit;} //error Redirect()
	else{ unset($_POST['csrf']); unset($_POST['pageid']); }//all ok

	//the array $arVals stores the names of all the values of the form
	$arVals = array("username"=>"","email"=>"","blog"=>"","welcomepagetext"=>"","information"=>"");			
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("username"=>"","email"=>"");
	//the array $arValsMaxSize stores the names of all the values of the form and the maximum size that each value is allowed to have 
	$arValsMaxSize = array("username"=>100,"email"=>100,"blog"=>100,"welcomepagetext"=>"2500","information"=>"4000");
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.	
	$arValsValidations = array("email"=>"/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/","blog"=>"/(http:\/\/)?([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/");

	//IMPORTANT for AJAX validation
	if( isset($_POST['inputValue']) && isset($_POST['fieldID']) )
	{	
		$inputValue = $_POST['inputValue'];
		$fieldID = $_POST['fieldID'];
		$profileID = $_POST['profileID'];
			if(!preg_match("/^[0-9]([0-9]*)/",$profileID)){errorHandler(703,$validationType); exit;}
		$updateDate = convertTimeStamp(date("Y-m-d") . " " . date("H:i:s"),'full');
		unset($_POST['inputValue']);
		unset($_POST['fieldID']);
		unset($_POST['profileID']);

		$explodeArr = explode('_',$fieldID,3);
		$_POST[$explodeArr[1]] = trim($inputValue);
		$fieldName = $explodeArr[1];
	}//if
	#####
	$inputValue = (get_magic_quotes_gpc()) ? $inputValue : addslashes($inputValue);
	$inputValue = htmlentities($inputValue, ENT_QUOTES, "UTF-8");
	$inputValue = trim($inputValue); 
	#####
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "NULL";}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		
		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}
		else{ $_SESSION['formvalues'][$key] = $val;}
	}//while
	
	reset($arVals);
	while (list($key, $val) = each ($arVals)){$arVals[$key]="'".$arVals[$key]."'";}//while


	if($validationType == "ajax")
	{
		$errorCode = $validator->ValidateAJAX($inputValue, $explodeArr[1], $arValsRequired, $arValsMaxSize, $arValsValidations);
		if($errorCode != 0){validateNsubmitXMLresponse($errorCode,$fieldID,'','','profile','');}
		
		if($errorCode==0)
		{
			//check if the profile exists in the DB
			$query00 = "SELECT COUNT(*) AS count FROM profiles WHERE id='".$profileID."'; ";
			$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'ajax',$inputValue,$fieldID,'profile','true');		
			$resultid00 = @mysql_result($result00,0,'count');
					
			//notice the first parameter in validateNsubmitXMLresponse. '305' is for error
			if((int)$resultid00==0){unset($dbobj); unset($validator); validateNsubmitXMLresponse(305,$fieldID,$inputValue,305,'profile',$updateDate); exit;}
			else
			{
				$query = "UPDATE profiles"
						. " SET ".$fieldName."=".$arVals[$fieldName]
						. " WHERE id='".$profileID."'; ";
				$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query00,'ajax',$inputValue,$fieldID,'profile','true');
				unset($dbobj);
				unset($validator);
				$entryVals['entryType']='routine'; $entryVals['valueType']='editProfiles'; $entryVals['message']='Updated profile.';
				editUsersActionLog('insert', $entryVals);
				validateNsubmitXMLresponse(0,$fieldID,$inputValue,0,'profile',$updateDate);
			}//else
		}//if($errorCode==0)
	}//if($validationType == "ajax")
	elseif($validationType == "php"){}//do nothing
}//editProfiles()

function cleanOrphanImageTags($tagsArr)
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	
	if(count($tagsArr)==0){return 0;}
		
	reset($tagsArr);
	while (list($key, $val) = each ($tagsArr))
	{
		$deleteQuery=" DELETE FROM tags WHERE id='".$key."'; ";
		$deleteresult = @mysql_query($deleteQuery) or dbErrorHandler(802,mysql_error(),$query00,'php','','','','true');
		if($deleteresult==1){}//all ok
		else{}//error with the query
	}//while
	$entryVals['entryType']='routine'; $entryVals['valueType']='cleanOrphanImageTags'; $entryVals['message']='Deleted orphan tags.';
	editUsersActionLog('insert', $entryVals);
	unset($dbobj);
}//cleanOrphanImageTags($tagsArr)

function editUsersActionLog($editType, $entryVals)
{
	if(SAVE_DB_ACTIONS=='off'){return 1;}
	if(SAVE_DB_ERRORS=='off'){if($entryVals['entryType']=='error'){return 1;}}
	
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$arrVals = array();
	$profileID = 0;
	$entryString='';
	
	
	if($entryVals['entryType']=='error')
	{
		$entryString.="<ul class='errorentries'>";
		$entryString.="<li class='errortypes'>";
			$entryString.=strtoupper(substr($entryVals['validationType'],0,1)).$entryVals['errorCode'].": ".$entryVals['message']." (".$entryVals['valueType'].")";
		$entryString.="</li>";
		if($entryVals['query']!='-'){$entryString.="<li class='dberrorquery'>".$entryVals['query']."</li>";}
		if($entryVals['dbError']!='-'){$entryString.="<li class='dberrortypes'>".$entryVals['dbError']."</li>";}
		$entryString.="</ul>";
	}
	elseif($entryVals['entryType']=='comment'){}
	elseif($entryVals['entryType']=='routine'){$entryString.=$entryVals['message'];}
	
	$arrVals['userid'] = $profileID;
	$arrVals['function'] = $entryVals['valueType'];
	$arrVals['message'] = $entryString;
	$arrVals['entrytype'] = $entryVals['entryType'];
	$arrVals['actiondatetime'] = date("Y-m-d")." ".date("H:i:s");
	if(adminLoggedIn()==1){$arrVals['userprivileges']="masteruser";}
		elseif(adminLoggedIn()==0){$arrVals['userprivileges']="commonuser";}
	if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){ $arrVals['userip']=$_SERVER["HTTP_X_FORWARDED_FOR"]; }
    	else { $arrVals['userip']=$_SERVER["REMOTE_ADDR"]; }
		
	reset($arrVals);	
	while (list($key, $val) = each ($arrVals))
	{	
		if (trim($val) == "") { $val = "NULL";} 
		$arrVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		//$arrVals[$key] = htmlentities($arrVals[$key], ENT_QUOTES, "UTF-8");
		$arrVals[$key] = "'".$arrVals[$key]."'";
	}

	$insert_query = $dbobj->createInsertQuery("usersactionlog", $arrVals);
	$insertID = $dbobj->executeInsertQuery($insert_query);

	unset($dbobj);
	unset($validator);
}//editUsersActionLog()

function deleteUsersActionLogEntries($usersActionLogType)
{
	require_once('jddbase.class.php');
	require_once('validate.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$pageID = $_POST['pageid'];

	if(($usersActionLogType!='routine')&&($usersActionLogType!='error')){$usersActionLogType='*';}

	$errorCode = NULL;
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'deleteentries', $_POST['pageid'])){errorHandler(702,'ajax'); exit;}
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok

	$deletequery = "DELETE FROM usersactionlog WHERE entrytype='".$usersActionLogType."'; ";
	$deleteresult = @mysql_query($deletequery) or dbErrorHandler(802,mysql_error(),$deletequery,'ajax','','','','true');
	unset($dbobj);
	unset($validator);
	if($deleteresult==1)
	{
		$value=$usersActionLogType;
		deleteElementsXMLresponse($value,'usersactionlog',$pageID); exit;
	} //all ok
	else{dbErrorHandler(802,mysql_error(),$deletequery,'ajax','','','','true'); exit;}//error with the query
}//deleteUsersActionLogEntries($usersActionLogType)

function contactJD($validationType)
{
	require_once('validate.class.php');
	
	$validator = new Validate();
	$explodeArr = array();
	$pageID = $_POST['pageid'];
	$errorsVarsArr = array();
	
	// read validation type (PHP or AJAX?)
	//if (isset($_GET['validationType'])){ $validationType = $_GET['validationType']; unset($_GET['validationType']); }
	//unset($_SESSION['errorformvalues']);
	$errorCode = NULL;

	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,$validationType); exit;}
	else {} //all ok

	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'submitcontactjd', $_POST['pageid'])){errorHandler(702,'ajax'); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
	
	//the array $arVals stores the names of all the values of the form
	$arVals = array("name"=>"","email"=>"","regarding"=>"","message"=>"","cc"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("name"=>"","email"=>"","message"=>"");
	//the array $arValsMaxSize stores the names of all the values of the form and the maximum size that each value is allowed to have 
	$arValsMaxSize = array("name"=>"100","email"=>"100","regarding"=>"100","message"=>CONTACT_MESSAGE_MAX_LENGTH);
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.	
	$arValsValidations = array("email"=>"/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/");
	
	if($_POST['cc']=='true'){$_POST['cc']='true';}
	else if($_POST['cc']=='false'){$_POST['cc']='false';}
	
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "NULL";}
		if ($key=='regarding'){if($val=='[regarding][not required]'){$val='NULL';}}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
		
		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}
		else
		{ 
			if($key!='message'){$_SESSION['formvalues'][$key] = strtolower($val);} 
			else{$_SESSION['formvalues'][$key] = $val;}
		}//
	}//while
	unset($_POST);
	$arVals['submitiontimestamp'] = date("Y-m-d") . " " . date("H:i:s");

	reset($arVals);
	while (list($key, $val) = each ($arVals))
	{
		if($key!='reply'){$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";}
		else{$arVals[$key] = "'" . $arVals[$key] . "'";}
	}//while

	$errorsVarsArr = $validator->ValidatePHP($arValsRequired, $arValsMaxSize, $arValsValidations);
	if($errorsVarsArr['errorCode'] != 0)
		{validateNsubmitXMLresponseContactJD($errorsVarsArr,'','','info',$pageID);}
	
	if($errorsVarsArr['errorCode'] == 0)
	{
		$arVals=removeQuotes($arVals);
		sendEmail($arVals);
		validateNsubmitXMLresponseContactJD('',$arVals,$insertID,'info',$pageID);
	}//if
	
}//contactJD($validationType)

function sendEmail($emailVals)
{
	$emailVals['message'] = strtoupper($emailVals['name']).' (from '.$emailVals['email'].') '.' said, '
			."\r\n"
			."\r\n"
			. '"'.nl2br($emailVals['message']).'"';
	
	// If any lines are larger than 120 characters, we will use wordwrap()
	$emailVals['message'] = wordwrap($emailVals['message'],100);
	if(!isset($emailVals['regarding'])||($emailVals['regarding']=='null')){$emailVals['regarding']='[no subject] (message from jamesdoe.com)';}
		else{$emailVals['regarding']=$emailVals['regarding'].' (message from jamesdoe.com)';}
	
	// add additional headers...
	$headers = "From: mail@".SERVER_NAME."\r\n" .
	   "Reply-To: mail@".SERVER_NAME."\r\n" .
	   "X-Mailer: PHP/".phpversion();
	// Send the email...
	mail(PROFILE_EMAIL,$emailVals['regarding'],$emailVals['message'],$headers);
	mail(SECONDARY_EMAIL,$emailVals['regarding'],$emailVals['message'],$headers);

	if($emailVals['cc']=='true')
	{
		$emailVals['message'] = 'Message you\'ve sent to mail@jamesdoe.com: '
			."\r\n"
			."\r\n"
			."\r\n"
			.$emailVals['message'];
			
		// Send the email...
		mail($emailVals['email'],$emailVals['regarding'],$emailVals['message'],$headers);
	}
}//sendEmail($emailVals)
?>