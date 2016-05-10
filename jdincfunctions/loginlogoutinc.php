<?php
###################################################
############loginlogoutinc.php########################
###################################################

function adminLogin()
{
	require_once('validate.class.php');
	$validator = new Validate();
	
	$errorCode = NULL;
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok

	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'login', $_POST['pageid'])){errorHandler(702,'ajax'); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok	
	
	$arVals = array("mmloginusrnamefield"=>"","mmpasswrdfieldfragment1"=>"","mmpasswrdfieldfragment2"=>"","mmpasswrdfieldfragment3"=>"");		
	$arValsRequired = array("mmloginusrnamefield"=>"","mmpasswrdfieldfragment1"=>"","mmpasswrdfieldfragment2"=>"","mmpasswrdfieldfragment3"=>"");
	$arValsMaxSize = array("mmloginusrnamefield"=>"8","mmpasswrdfieldfragment1"=>"4","mmpasswrdfieldfragment2"=>"8","mmpasswrdfieldfragment3"=>"10");
	$arValsValidations = array();
	
	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{
		$val = addslashes(trim(strtolower($val)));
		$val = htmlentities($val, ENT_QUOTES, "UTF-8");
		$_POST[$key] = $val;
		
		if ($val == "NULL"){ $_SESSION['formvalues'][$key] = NULL;}
		else{$_SESSION['formvalues'][$key] = $val;}
		
		$errorCode = $validator->ValidateAJAX($val, $key, $arValsRequired, $arValsMaxSize, $arValsValidations);
		if($errorCode != 0){validateNsubmitLoginXMLresponse($errorCode);}//Validation error LOGIN.
	}//while
	
	if($errorCode==0)
	{
		$adminUsername = ''; // 'jamesdo1_'; //// Server db user prefix
		$adminUsername .= $_POST['mmloginusrnamefield']; 
		$adminPassword = $_POST['mmpasswrdfieldfragment1'].$_POST['mmpasswrdfieldfragment2'].$_POST['mmpasswrdfieldfragment3'];
		$adminPassword = hash('sha256',$adminPassword);
		unset($_POST['mmloginusrnamefield']);
		unset($_POST['mmpasswrdfieldfragment1']); unset($_POST['mmpasswrdfieldfragment2']); unset($_POST['mmpasswrdfieldfragment3']);
		
		generateUsers($adminUsername,$adminPassword);
		$loginResult=validateLoginCredentials();

		if($loginResult==1){validateNsubmitLoginXMLresponse(103);}
		elseif($loginResult==0)
		{
			$_SESSION['ADMIN_LOGIN'] = TRUE;
			$_SESSION['ADMIN_USERNAME'] = 'jdcooldaddy';
			$_SESSION['ADMIN_PASSWORD'] = hash('sha256','Bagger off, wanker');
			
			$entryVals['entryType']='routine'; $entryVals['valueType']='adminLogin'; $entryVals['message']='master user logged in.'; editUsersActionLog('insert', $entryVals);
			validateNsubmitLoginXMLresponse('0');
		}//elseif

	}//if
	
	unset($validator);
}//adminLogin()

function adminLogout()
{
	$entryVals['entryType']='routine'; $entryVals['valueType']='adminLogout'; $entryVals['message']='master user logged out.'; editUsersActionLog('insert', $entryVals);

	session_unset();
	// Clear the session cookie
	unset($_COOKIE[session_name()]);
	// Destroy session data
	session_destroy();
	
	redirects(0,'');
}//adminLogout()

function validateLoginCredentials()
{
	if(!isset($_SESSION['USERSARR'])){return 5;}
	
	//find the credential positions
	$explodedArr = explode('.',$_SERVER["REMOTE_ADDR"].'.'.date("Y.m.d"));
	$z=0; for($i=0;$i<count($explodedArr);$i++){$z+=(int)$explodedArr[$i];}
	$position=0; for($i=0;$i<strlen($z);$i++){$position+=(int)substr($z,$i,1);}

	$adminUsername = $_SESSION['USERSARR'][$position]['username'];
	$adminPassword = $_SESSION['USERSARR'][$position]['password'];

	//create the admin password
	$j=6; for($i=0;$i<7;$i++){$realAdminPassword.=substr($adminPassword,$j,2);$j+=9;}
	
	//attemp to connect to the database with the given username & password, and then select the database
	if( (@mysql_connect(JDDB_HOST,$adminUsername,$realAdminPassword)) || (@mysql_select_db(JDDB_DATABASE)) )
	{
		//attempt to run a SELECT query to the DB table usersactionlog. Only the administrator can run such a query
		@mysql_select_db(JDDB_DATABASE) or die("error selecting db"); 
		$query01 = "SELECT COUNT(*) FROM usersactionlog; ";
		$result01 = @mysql_query($query01) or validateNsubmitLoginXMLresponse(103); //can't execute this query, user is an imposter.
		$num01 = @mysql_num_rows($result01);//num
		
		return 0; //this dude is the real McCoy!
	}
	else{return 1;} //NOT OK

}//validateLoginCredentials($username,$password)

function generateUsers($adminUserName,$adminPassword)
{
	$dummyUsersArr = array();
	$usersArr = array();
	
	$dummyUsersArr[0]['username'] = 'pparkerz'; $dummyUsersArr[0]['password'] = '';
	$dummyUsersArr[1]['username'] = 'eddbrock()'; $dummyUsersArr[1]['password'] = '';
	$dummyUsersArr[2]['username'] = 'joeldoe'; $dummyUsersArr[2]['password'] = '';
	$dummyUsersArr[3]['username'] = 'avdalton423'; $dummyUsersArr[3]['password'] = '';
	$dummyUsersArr[4]['username'] = 'jameshowlett'; $dummyUsersArr[4]['password'] = '';
	$dummyUsersArr[5]['username'] = 'umdoken'; $dummyUsersArr[5]['password'] = '';
	$dummyUsersArr[6]['username'] = 'cklarkson'; $dummyUsersArr[6]['password'] = '';
	$dummyUsersArr[7]['username'] = 'murphybrown'; $dummyUsersArr[7]['password'] = '';
	$dummyUsersArr[8]['username'] = '&avl&don'; $dummyUsersArr[8]['password'] = '';
	$dummyUsersArr[9]['username'] = 'kong4kang'; $dummyUsersArr[9]['password'] = '';
	$dummyUsersArr[10]['username'] = 'millarmka'; $dummyUsersArr[10]['password'] = '';
	$dummyUsersArr[11]['username'] = 'varleylynna'; $dummyUsersArr[11]['password'] = '';
	$dummyUsersArr[12]['username'] = 'kishumotomasahi8'; $dummyUsersArr[12]['password'] = '';
	$dummyUsersArr[13]['username'] = 'scottpilgrim3'; $dummyUsersArr[13]['password'] = '';
	$dummyUsersArr[14]['username'] = 'hipperthendan'; $dummyUsersArr[14]['password'] = '';
	$dummyUsersArr[15]['username'] = 'samurahiroaki'; $dummyUsersArr[15]['password'] = '';
	$dummyUsersArr[16]['username'] = 'thomsponcraig'; $dummyUsersArr[16]['password'] = '';
	$dummyUsersArr[17]['username'] = 'jamesdoe'; $dummyUsersArr[17]['password'] = '';
	$dummyUsersArr[18]['username'] = 'jjjameson'; $dummyUsersArr[18]['password'] = '';
	$dummyUsersArr[19]['username'] = 'justafraction'; $dummyUsersArr[19]['password'] = '';
	$dummyUsersArr[20]['username'] = 'matthowlfract'; $dummyUsersArr[20]['password'] = '';
	$dummyUsersArr[21]['username'] = 'dannyrand'; $dummyUsersArr[21]['password'] = '';
	$dummyUsersArr[22]['username'] = 'murdockfoggy'; $dummyUsersArr[22]['password'] = '';
	$dummyUsersArr[23]['username'] = 'nelson&murdock'; $dummyUsersArr[23]['password'] = '';
	$dummyUsersArr[24]['username'] = 'chochofrank'; $dummyUsersArr[24]['password'] = '';
	$dummyUsersArr[25]['username'] = 'therealdude'; $dummyUsersArr[25]['password'] = '';
	$dummyUsersArr[26]['username'] = 'boschfawstin'; $dummyUsersArr[26]['password'] = '';
	$dummyUsersArr[27]['username'] = 'kunkelkelall'; $dummyUsersArr[27]['password'] = '';
	$dummyUsersArr[28]['username'] = 'kylebakerme'; $dummyUsersArr[28]['password'] = '';
	$dummyUsersArr[29]['username'] = 'bytemyass'; $dummyUsersArr[29]['password'] = '';
	$dummyUsersArr[30]['username'] = 'willypossada'; $dummyUsersArr[30]['password'] = '';
	$dummyUsersArr[31]['username'] = 'cookedarwinme'; $dummyUsersArr[31]['password'] = '';
	$dummyUsersArr[32]['username'] = 'milosmartin'; $dummyUsersArr[32]['password'] = '';
	$dummyUsersArr[33]['username'] = 'warrenmiles'; $dummyUsersArr[33]['password'] = '';
	$dummyUsersArr[34]['username'] = 'gabrielbaba'; $dummyUsersArr[34]['password'] = '';
	$dummyUsersArr[35]['username'] = 'herobeark&kid'; $dummyUsersArr[35]['password'] = '';
	$dummyUsersArr[36]['username'] = 'kurtzbrent'; $dummyUsersArr[36]['password'] = '';
	$dummyUsersArr[37]['username'] = 'larsongiveshope'; $dummyUsersArr[37]['password'] = '';
	$dummyUsersArr[38]['username'] = 'casanovababy'; $dummyUsersArr[38]['password'] = '';
	$dummyUsersArr[39]['username'] = 'kirbyditko'; $dummyUsersArr[39]['password'] = '';
	$dummyUsersArr[40]['username'] = 'jacksteve'; $dummyUsersArr[40]['password'] = '';
	$dummyUsersArr[41]['username'] = 'casanovababy'; $dummyUsersArr[41]['password'] = '';
	$dummyUsersArr[42]['username'] = 'brianwood'; $dummyUsersArr[42]['password'] = '';
	$dummyUsersArr[43]['username'] = 'popehope'; $dummyUsersArr[43]['password'] = '';
	$dummyUsersArr[44]['username'] = 'pulphope55'; $dummyUsersArr[44]['password'] = '';
	$dummyUsersArr[45]['username'] = 'ernestsale(43)'; $dummyUsersArr[45]['password'] = '';
	$dummyUsersArr[46]['username'] = 'saletimmy(blues)'; $dummyUsersArr[46]['password'] = '';
	$dummyUsersArr[47]['username'] = 'jephoneloeb'; $dummyUsersArr[47]['password'] = '';
	$dummyUsersArr[48]['username'] = 'ozbornozz'; $dummyUsersArr[48]['password'] = '';
	$dummyUsersArr[49]['username'] = 'nategray'; $dummyUsersArr[49]['password'] = '';
	$dummyUsersArr[50]['username'] = 'summersscotttime'; $dummyUsersArr[59]['password'] = '';
	$dummyUsersArr[51]['username'] = 'smithjeff'; $dummyUsersArr[51]['password'] = '';
	$dummyUsersArr[52]['username'] = 'frankquitely'; $dummyUsersArr[52]['password'] = '';
	$dummyUsersArr[53]['username'] = 'mccloudscotty'; $dummyUsersArr[53]['password'] = '';
	$dummyUsersArr[54]['username'] = 'thebrubaker'; $dummyUsersArr[54]['password'] = '';
	$dummyUsersArr[55]['username'] = 'totalbachalo'; $dummyUsersArr[55]['password'] = '';
	$dummyUsersArr[56]['username'] = 'mikescarrey'; $dummyUsersArr[56]['password'] = '';
	$dummyUsersArr[57]['username'] = 'ziltoidtownsend'; $dummyUsersArr[57]['password'] = '';
	$dummyUsersArr[58]['username'] = 'killerdude'; $dummyUsersArr[58]['password'] = '';
	$dummyUsersArr[59]['username'] = 'gerardnoway'; $dummyUsersArr[59]['password'] = '';
	$dummyUsersArr[60]['username'] = 'doezdoezup7'; $dummyUsersArr[60]['password'] = '';
	$dummyUsersArr[61]['username'] = 'corywalkerhard'; $dummyUsersArr[61]['password'] = '';
	$dummyUsersArr[62]['username'] = 'seanryan'; $dummyUsersArr[62]['password'] = '';
	$dummyUsersArr[63]['username'] = 'parkerrick'; $dummyUsersArr[63]['password'] = '';
	for($i=0;$i<count($dummyUsersArr);$i++){$dummyUsersArr[$i]['password'] = hash('sha256',randomDummyPassword(24));}
	
	//calculate the position of the real administrator credentials in the array
	$explodedArr = explode('.',$_SERVER["REMOTE_ADDR"].'.'.date("Y.m.d"));
	$z=0; for($i=0;$i<count($explodedArr);$i++){$z+=(int)$explodedArr[$i];}
	$position=0; for($i=0;$i<strlen($z);$i++){$position+=(int)substr($z,$i,1);}
	
	$j=0;
	while(count($usersArr)<65)
	{
		$randomInteger=rand(0,63);
		//save the real administrator credentials in the array
		if($j==$position){$usersArr[$j]['username']=$adminUserName; $usersArr[$j]['password']=$adminPassword; $j++;}
		else{$usersArr[$j]=$dummyUsersArr[$randomInteger];
			unset($dummyUsersArr[$randomInteger]); $j++;}
	}//while
	
	$_SESSION['USERSARR']=$usersArr;
}//generateUsers()
?>