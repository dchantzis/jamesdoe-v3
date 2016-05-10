<?php
function validateNsubmitXMLresponse($errorCode,$fieldID,$fieldValue,$insertID,$valueType,$updateDate)
{
	//if($fieldID==''){$fieldID='null';}
	if($fieldValue==''){$fieldValue='null';}
	if($insertID==''){$insertID='null';}
	if($updateDate==''){$updateDate='null';}
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . '<response>';
	$response .= '<responsetype>'.'routine'.'</responsetype>'
				.'<valuetype>'.$valueType.'</valuetype>'
				.'<result>'.$errorCode.'</result>'
				.'<fieldid>'.$fieldID.'</fieldid>'
				.'<fieldvalue>'.$fieldValue.'</fieldvalue>'
				.'<insertid>'.$insertID.'</insertid>'
				.'<updatedate>'.$updateDate.'</updatedate>';
	$response .= '</response>';
	
	// generate the response
	if(ob_get_length()) { ob_clean(); }
	header('Content-Type: text/xml');
	echo $response;
}//validateNsubmitXMLresponse($errorCode,$fieldID,$fieldValue,$insertID,$valueType,$updateDate)
function deleteElementsXMLresponse($value,$valueType,$pageID)
{
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . '<response>';
	$response .= '<responsetype>'.'routine'.'</responsetype>'
				.'<value>'.$value.'</value>'
				.'<valuetype>'.$valueType.'</valuetype>'
				.'<page>'.$pageID.'</page>';
	$response .='</response>';
	
	// generate the response
	if(ob_get_length()) { ob_clean(); }
	header('Content-Type: text/xml');
	echo $response;
}//deleteElementsXMLresponse($value,$valueType,$pageID);
function searchNsuggestXMLresponse($errorCode,$fieldID,$suggestionsArr)
{
	$response ='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . '<response>';
	$response .= '<responsetype>'.'routine'.'</responsetype>'
				.'<result>'.$errorCode.'</result>'
				.'<fieldid>'.$fieldID.'</fieldid>'
				.'<bla>'.count($suggestionsArr).'</bla>';
	reset($suggestionsArr);
	while (list($key, $val) = each ($suggestionsArr))
	{
		$response .= '<suggestion>'.$suggestionsArr[$key].'</suggestion>';
	}//while
	$response .='</response>';
	// generate the response
	if(ob_get_length()) { ob_clean(); }
	header('Content-Type: text/xml');
	echo $response;
}//searchNsuggestXMLresponse($errorCode,$fieldID,$suggestionsArr)
function getPostElementsXMLresponse($errorCode,$arrVals,$type)
{
	$response ='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . '<response>';
	$response .= '<responsetype>'.'routine'.'</responsetype>'
				.'<result>'.$errorCode.'</result>'
				.'<type>'.$type.'</type>';
	
	reset($arrVals);
	if($type=='months')
		{while (list($key, $val) = each ($arrVals)){$response .= "<postmonth id='".$key."'>".$arrVals[$key]."</postmonth>";}}
	elseif($type=='headlines')
		{while (list($key, $val) = each ($arrVals)){$response .= "<postday id='".$key."' title='".$arrVals[$key]['day']."' class='".$arrVals[$key]['id']."'>".$arrVals[$key]['headline']."</postday>";}}
	
	$response .='</response>';
	// generate the response
	if(ob_get_length()) { ob_clean(); }
	header('Content-Type: text/xml');
	echo $response;
}//getPostElementsXMLresponse($errorCode,$arrVals,$type)
function validateNsubmitXMLresponseComments($errorsVarsArr,$commentVarsArr,$insertID,$valueType,$pageID)
{
	if($insertID==''){$insertID = 'null';}
	if((!isset($commentVarsArr))||($commentVarsArr==''))
	{
		$commentVarsArr['name'] = 'null';
		$commentVarsArr['email'] = 'null';
		$commentVarsArr['website'] = 'null';
		$commentVarsArr['submitiontimestamp'] = 'null';
		$commentVarsArr['reply'] = 'null';
	}
	if((!isset($errorsVarsArr))||($errorsVarsArr==''))
	{
		$errorsVarsArr['errorCode'] = 0;
		$errorsVarsArr['errorFieldID'] = 'null';
		$errorsVarsArr['errorFieldValue'] = 'null';
	}
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . '<response>';
	$response .= '<responsetype>'.'routine'.'</responsetype>'
				.'<valuetype>'.$valueType.'</valuetype>'
				.'<result>'.$errorsVarsArr['errorCode'].'</result>'
				.'<fieldid>'.$errorsVarsArr['errorFieldID'].'</fieldid>'
				.'<fieldvalue>'.$errorsVarsArr['errorFieldValue'].'</fieldvalue>'
				.'<insertid>'.$insertID.'</insertid>'
				.'<commentname>'.$commentVarsArr['name'].'</commentname>'
				.'<commentemail>'.$commentVarsArr['email'].'</commentemail>'
				.'<commentwebsite>'.$commentVarsArr['website'].'</commentwebsite>'
				.'<commenttimestamp>'.convertTimeStamp($commentVarsArr['submitiontimestamp'],'reallylong').'</commenttimestamp>'
				.'<commentreply>'.$commentVarsArr['reply'].'</commentreply>'
				.'<post>'.$commentVarsArr['postid'].'</post>'
				.'<pageid>'.$pageID.'</pageid>';
	$response .= '</response>';
	
	// generate the response
	if(ob_get_length()) { ob_clean(); }
	header('Content-Type: text/xml');
	echo $response;
}//validateNsubmitXMLresponseComments($errorsVarsArr,$commentVarsArr,$insertID,$valueType)
function errorReportXMLresponse($errorCode,$dbError,$message,$fieldValue,$fieldID,$valueType)
{
	if($fieldValue==''){$fieldValue='null';}
	if($fieldID==''){$fieldID='null';}
	if($valueType==''){$valueType='null';}
	if($dbError==''){$dbError='null';}
	
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . '<response>';
	$response .= '<responsetype>'.'errorreporter'.'</responsetype>'
				.'<errorcode>'.$errorCode.'</errorcode>'
				.'<message>'.$message.'</message>'
				.'<databaseerror>'.$dbError.'</databaseerror>'
				.'<fieldid>'.$fieldID.'</fieldid>'
				.'<fieldvalue>'.$fieldValue.'</fieldvalue>'
				.'<result>1</result>'
				.'<valuetype>'.$valueType.'</valuetype>';
	$response .='</response>';
	
	// generate the response
	if(ob_get_length()) { ob_clean(); }
	header('Content-Type: text/xml');
	echo $response;
	exit;
}//errorReportXMLresponse($errorCode,$message,$fieldValue,$fieldID,$valueType)

function errorHandler($errorCode,$validationType)
{
	$message = variousMessages($errorCode);
	
	$errArray['entryType']='error';
	$errArray['valueType']='-';
	$errArray['validationType']=$validationType;
	$errArray['errorCode']=$errorCode;
	$errArray['message']=$message;
	$errArray['query']='-';
	$errArray['dbError']='-';
	editUsersActionLog('insert', $errArray);
	
	if($validationType=='ajax')
		{errorReportXMLresponse($errorCode,'',$message,'','','');}
	elseif($validationType=='php')
		{$_SESSION['ERR']['TYPE']='Security'; $_SESSION['ERR']['CODE']=$errorCode; $_SESSION['ERR']['MESSAGE']=$message; redirects(21,'');}
}//errorHandler($errorCode,$validationType)
function dbErrorHandler($errorCode,$dbError,$query,$validationType,$fieldValue,$fieldID,$valueType,$parseErrorResponse)
{
	$message = variousMessages($errorCode);
	
	$errArray['entryType']='error';
	$errArray['valueType']=$valueType;
	$errArray['validationType']=$validationType;
	$errArray['errorCode']=$errorCode;
	$errArray['message']=$message;
	$errArray['query']=$query;
	$errArray['dbError']=$dbError;
	
	if($validationType=='ajax')
	{
		if($parseErrorResponse=='true'){
			editUsersActionLog('insert', $errArray);
			errorReportXMLresponse($errorCode,$dbError,$message,$fieldValue,$fieldID,$valueType);
		}//true
		else{errorReportXMLresponse($errorCode,$dbError,$message,$fieldValue,$fieldID,$valueType); exit;}
	}//ajax
	elseif($validationType=='php')
	{
		if($parseErrorResponse=='true'){
			$_SESSION['ERR']['TYPE']='Database'; $_SESSION['ERR']['CODE']=$errorCode; $_SESSION['ERR']['DATABASEERROR']=$dbError; $_SESSION['ERR']['MESSAGE']=$message; redirects(21,'');
		}//true
		elseif($parseErrorResponse=='false')
		{
			echo "<div class='phperrorcontent'>";
			echo "<div class='phperrorbanners'>warning</div>
				<div id='phperrormessage'><span class='red'>Error </span>".$errorCode.": ".$message."<br /><br />".$dbError."</div>";
			echo "<div class='phperrornotes'>
            	    Sorry for the inconvenience. <br />The system administrators has been notified about this error.<br />
					Try refreshing the page or hit F5. <br />If that doesn't seem to work, visit us soon again!</div>";
			echo "</div>";
			editUsersActionLog('insert', $errArray);
			exit;
		}//false
	}//php
}//dbErrorHandler($errorCode,$dbError,$query,$validationType,$fieldValue,$fieldID,$valueType,$parseErrorResponse)
function getImageXMLresponse($errorCode,$imageVars)
{
	if($imageVars['name']==''){$imageVars['name']='null';}
	if($imageVars['description']==''){$imageVars['description']='null';}
	if($imageVars['tags']==''){$imageVars['tags']='null';}
		
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . '<response>';
	$response .= '<responsetype>'.'routine'.'</responsetype>'
				.'<result>'.$errorCode.'</result>'
				.'<imageid>'.$imageVars['id'].'</imageid>'
				.'<imagename>'.$imageVars['name'].'</imagename>'
				.'<imagedescription>'.$imageVars['description'].'</imagedescription>'
				.'<imagetags>'.$imageVars['tags'].'</imagetags>'
				.'<imagefileurl>'.$imageVars['fileurl'].'</imagefileurl>'
				.'<imagesubmitiontimestamp>'.$imageVars['submitiontimestamp'].'</imagesubmitiontimestamp>'
				.'<imagealbumid>'.$imageVars['albumid'].'</imagealbumid>'
				.'<imagecategoryid>'.$imageVars['albumtagid'].'</imagecategoryid>'
				.'<imagealbumname>'.$imageVars['albumname'].'</imagealbumname>'
				.'<imagecategoryname>'.$imageVars['categoryname'].'</imagecategoryname>'
				.'<imageposition>'.$imageVars['imageposition'].'</imageposition>'
				.'<albumimagescount>'.$imageVars['albumimagescount'].'</albumimagescount>';
	$response .= '</response>';
	
	// generate the response
	if(ob_get_length()) { ob_clean(); }
	header('Content-Type: text/xml');
	echo $response;
}//getImageXMLresponse($errorCode,$imageVars)
function validateNsubmitLoginXMLresponse($errorCode)
{
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . '<response>';
	$response .= '<responsetype>'.'routine'.'</responsetype>'.'<result>'.$errorCode.'</result>';
	$response .= '</response>';
	
	// generate the response
	if(ob_get_length()) { ob_clean(); }
	header('Content-Type: text/xml');
	echo $response;
	exit;
}//validateNsubmitLoginXMLresponse($errorCode,$fieldID,$fieldValue,$insertID,$valueType,$updateDate)
function validateNsubmitXMLresponseContactJD($errorsVarsArr,$senderVarsArr,$insertID,$valueType,$pageID)
{
	if($insertID==''){$insertID = 'null';}
	if((!isset($senderVarsArr))||($senderVarsArr==''))
	{
		$senderVarsArr['name'] = 'null';
		$senderVarsArr['email'] = 'null';
		$senderVarsArr['regarding'] = 'null';
		$senderVarsArr['message'] = 'null';
		$senderVarsArr['cc'] = 'null';
	}
	if((!isset($errorsVarsArr))||($errorsVarsArr==''))
	{
		$errorsVarsArr['errorCode'] = 0;
		$errorsVarsArr['errorFieldID'] = 'null';
		$errorsVarsArr['errorFieldValue'] = 'null';
	}
	$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . '<response>';
	$response .= '<responsetype>'.'routine'.'</responsetype>'
				.'<valuetype>'.$valueType.'</valuetype>'
				.'<result>'.$errorsVarsArr['errorCode'].'</result>'
				.'<fieldid>'.$errorsVarsArr['errorFieldID'].'</fieldid>'
				.'<fieldvalue>'.$errorsVarsArr['errorFieldValue'].'</fieldvalue>'
				.'<insertid>'.$insertID.'</insertid>'
				.'<sendername>'.$senderVarsArr['name'].'</sendername>'
				.'<senderemail>'.$senderVarsArr['email'].'</senderemail>'
				.'<senderregarding>'.$senderVarsArr['regarding'].'</senderregarding>'
				.'<sendermessage>'.$senderVarsArr['message'].'</sendermessage>'
				.'<sendercc>'.$senderVarsArr['cc'].'</sendercc>'
				.'<pageid>'.$pageID.'</pageid>';
	$response .= '</response>';
	
	// generate the response
	if(ob_get_length()) { ob_clean(); }
	header('Content-Type: text/xml');
	echo $response;
}//validateNsubmitXMLresponseContactJD($errorsVarsArr,$senderVarsArr,$insertID,$valueType,$pageID)
function variousMessages($code)
{	
	if(!preg_match("/^[0-9]([0-9]*)/",$code)){ $code = NULL; 	$error = "";}
	else { 	$error = ""; }//do nothing. ALL OK
	
	$message = "";
	switch($code)
	{
		case 101:
			$message = $error."Please fill out all the requested fields.";
			break;
		case 102:
			$message = $error."The fields are too long for our database.";
			break;
		case 103:
			$message = $error."Unaccepted field value.";
			break;
		case 104:
			$message = $error."";
			break;
		case 105:
			$message = $error."Unaccepted file format.";
			break;
		case 106:
			$message = $error."Allowed image formats are not defined";
			break;
		case 107:
			$message = $error."Image exceeds the defined maximum filesize";
			break;
		case 108:
			$message = $error."Image not uploaded";
			break;
		case 191:
			$message = $error."The uploaded file exceeds the upload_max_filesize directive in php.ini.";
			break;
		case 192:
			$message = $error."The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
			break;
		case 193:
			$message = $error."The uploaded file was only partially uploaded.";
			break;
		case 194:
			$message = $error."No file was uploaded.";
			break;
		case 196:
			$message = $error."Missing a temporary folder.";
			break;
		case 197:
			$message = $error."Failed to write file to disk.";
			break;
		case 198:
			$message = $error."File upload stopped by extension.";
			break;
		case 201:
			$message = "album created successfully.";
			break;
		case 202:
			$message = "Images uploaded successfully.";
			break;
		case 303:
			$message = $error."Unable to change album visibility.";
			break;
		case 304:
			$message = $error."Unable to toggle settings checkbox.";
			break;
		case 305:
			$message = $error."Unable to edit profile information";
			break;
		case 601:
			$message = $error."Unable to open fileserver directory.";
			break;
		case 602:
			$message = $error."Unable to delete image from fileserver.";
			break;
		case 603:
			$message = $error."Unable to delete all images from this album.";
			break;
		case 604:
			$message = $error."Unable to delete all images from this category.";
			break;
		case 701:
			$message = $error."Form was not submitted normally. Post value was not used.";
			break;
		case 702:
			$message = $error."Form was not submitted normally. CSRF error.";
			break;
		case 703:
			$message = $error."Values have been injected.";
			break;
		case 801:
			$message = "Database error.";
			break;
		case 802:
			$message = $error."Error executing database query.";
			break;
		case 803:
			$message = "Unaccepted action.";
			break;
		case 804:
			$message = $error."Error connecting to the database.";
			break;
		case 805:
			$message = $error."Error selecting the database.";
			break;
		default:
			break;
	}//switch
	
	return $message;
}//errorMessages()

//removeQuotes($arVals)
function removeQuotes($arVals)
{
	reset($arVals);
	while (list($key, $val) = each ($arVals))
	{
		$arVals[$key] = substr($val,1,-1);
	}//while
	return $arVals;
}//removeQuotes()

//$ar_values --> array with values
//$from_str --> change this string
//$to_str -->with this
//example: convert_ar_vals($ar_values, "NULL", "*unspecified*")
function convertArVals($arVals, $fromStr, $toStr)
{
	reset ($arVals);
	while(list($key, $val) = each ($arVals))
	{
		if($val == strtoupper($fromStr) || $val == strtolower($fromStr)) 
		{
			$val = $toStr; 
			$arVals[$key] = $val;
		}//if
	}//while
	return $arVals;
}//convert_ar_vals($ar_values, $from_str, $to_str)

function convertTimeStamp($dateStr,$dateType)
{
	if($dateType=='full')
	{
		$monthNames = array( '1'=>'January','01'=>'January','2'=>'February','02'=>'February',
			'3'=>'March','03'=>'March','4'=>'April','04'=>'April','5'=>'May','05'=>'May',
			'6'=>'June','06'=>'June','7'=>'July','07'=>'July','8'=>'August','08'=>'August',
			'9'=>'September','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
	}elseif($dateType=='short')
	{
		$monthNames = array( '1'=>'Jan','01'=>'Jan','2'=>'Feb','02'=>'Feb',
			'3'=>'Mar','03'=>'Mar','4'=>'Apr','04'=>'Apr','5'=>'May','05'=>'May',
			'6'=>'June','06'=>'June','7'=>'July','07'=>'July','8'=>'Aug','08'=>'Aug',
			'9'=>'Sept','09'=>'Sept','10'=>'Oct','11'=>'Nov','12'=>'Dec');
	}elseif($dateType=='reallyshort')
	{
		$monthNames = array( '1'=>'01','01'=>'01','2'=>'02','02'=>'02',
			'3'=>'03','03'=>'03','4'=>'04','04'=>'04','5'=>'05','05'=>'05',
			'6'=>'06','06'=>'06','7'=>'07','07'=>'07','8'=>'08','08'=>'08',
			'9'=>'09','09'=>'09','10'=>'10','11'=>'11','12'=>'12');		
	}elseif($dateType=='shortdaynmonth')
	{
		$monthNames = array( '1'=>'Jan','01'=>'Jan','2'=>'Feb','02'=>'Feb',
			'3'=>'Mar','03'=>'Mar','4'=>'Apr','04'=>'Apr','5'=>'May','05'=>'May',
			'6'=>'June','06'=>'June','7'=>'July','07'=>'July','8'=>'Aug','08'=>'Aug',
			'9'=>'Sept','09'=>'Sept','10'=>'Oct','11'=>'Nov','12'=>'Dec');
	}elseif($dateType=='reallylong')
	{
		$monthNames = array( '1'=>'January','01'=>'January','2'=>'February','02'=>'February',
			'3'=>'March','03'=>'March','4'=>'April','04'=>'April','5'=>'May','05'=>'May',
			'6'=>'June','06'=>'June','7'=>'July','07'=>'July','8'=>'August','08'=>'August',
			'9'=>'September','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
	}
	
	$dateStr = str_replace(' ','-',$dateStr);
	$dateStr = str_replace(':','-',$dateStr);
	$explodeDateArr = explode('-',$dateStr);
	
	reset($monthNames);
	while (list($key, $val) = each ($monthNames)) { if($key==$explodeDateArr[1]){ $explodeDateArr[1] = $val; } }//
	$dateStr = $explodeDateArr[1].' '.$explodeDateArr[2].' '.$explodeDateArr[0].', '.$explodeDateArr[3].':'.$explodeDateArr[4];
	if($dateType=='reallyshort')
		{$dateStr = $explodeDateArr[1].'.'.$explodeDateArr[2].'.'.$explodeDateArr[0];}
	if($dateType=='shortdaynmonth')
		{$dateStr = $explodeDateArr[2].' '.$explodeDateArr[1].' '.substr($explodeDateArr[0],2,2);}
	if($dateType=='reallylong')
		{$dateStr = $explodeDateArr[1].' '.$explodeDateArr[2].', '.$explodeDateArr[0].' at '.$explodeDateArr[3].':'.$explodeDateArr[4];}
	return $dateStr;
}//convertTimeStamp($dateStr,$dateType)

function strReplaceCount($search,$replace,$subject,$times)
{
	$subjectOriginal=$subject;
	$len=strlen($search);    
	$pos=0;
	
	for($i=1; $i<=$times; $i++)
	{
		$pos=strpos($subject,$search,$pos);
        if($pos!==false)
		{
			$subject = substr($subjectOriginal,0,$pos);
			$subject .= $replace;
			$subject .= substr($subjectOriginal,$pos+$len);
			$subjectOriginal = $subject;
        }//if
		else{break;}
    }//for
    return($subject);
}//strReplaceCount

function randomDummyPassword($length) {
    $charsRepository = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ()!@#$%^&*";
    $dummyPassword = "";
    while(strlen($dummyPassword)<$length) {
        $dummyPassword .= substr($charsRepository,(rand()%(strlen($charsRepository))),1);
    }
    return($dummyPassword);
}

function whereUgo($cteva)
{
	if(!preg_match("/^[0-9]([0-9]*)/",$cteva)){ $cteva = NULL; }
	else {}//do nothing. ALL OK
	
	switch ($cteva){
		case 0:
			//if the user is not logged in, redirect him to 'index.php'
			if(
				(!isset($_SESSION['ADMIN_LOGIN'])) || ($_SESSION['ADMIN_LOGIN']!=TRUE) ||
				(!isset($_SESSION['ADMIN_USERNAME'])) || ($_SESSION['ADMIN_USERNAME']=='') ||
				(!isset($_SESSION['ADMIN_PASSWORD'])) || ($_SESSION['ADMIN_PASSWORD']=='')
			){
				redirects(0);
			}//if
		break;
		case 1:
			//if the user is not logged in, redirect him to 'index.php'
			$loginResult=validateLoginCredentials();
			if($loginResult==1){redirects(0);}
		break;
		case 2:
			//if the user is logged in, redirect him to 'index.php'
			if(
				(isset($_SESSION['ADMIN_LOGIN'])) && ($_SESSION['ADMIN_LOGIN']==TRUE) &&
				(isset($_SESSION['ADMIN_USERNAME'])) && ($_SESSION['ADMIN_USERNAME']!='') &&
				(isset($_SESSION['ADMIN_PASSWORD'])) && ($_SESSION['ADMIN_PASSWORD']!='')
			){
				redirects(0);
			}//if
		break;
		default:
			//do nothing
		break;
	}//switch
}//whereUgo()

function adminLoggedIn()
{
	if(
		(isset($_SESSION['ADMIN_LOGIN'])) && ($_SESSION['ADMIN_LOGIN']==TRUE) &&
		(isset($_SESSION['ADMIN_USERNAME'])) && ($_SESSION['ADMIN_USERNAME']!='') &&
		(isset($_SESSION['ADMIN_PASSWORD'])) && ($_SESSION['ADMIN_PASSWORD']!='')
	){return 1;}
	else{return 0;}
}//adminLoggedIn()

function checkCookiesAvailability($errVars)
{
	error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE);
	setcookie ('test', 'test', time() + 60000);
	if (!empty($_COOKIE['test'])) 
	{
		if(isset($errVars['javascriptEnabled'])){ 
			if($errVars['javascriptEnabled']!='1'){redirects(0,'');}
		}
		else{}
	}//"Cookies are enabled on your browser"
	else if(!isset($_COOKIE['test']))
	{ 
		if(!isset($errVars['javascriptEnabled']))
		{
			redirects(20,'?e='.hash('sha256', "cookies"));
		}
		else{}
	}
}//checkCookies()
?>