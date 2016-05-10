<?php
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	header ("Content-type: text/html; charset=utf-8");
	###################################################################################
	
	session_start(); //start session
	session_regenerate_id(true); //regenerate session id
	//regenerate session id if PHP version is lower thatn 5.1.0
	if(!version_compare(phpversion(),"5.1.0",">=")){ setcookie( session_name(), session_id(), ini_get("session.cookie_lifetime"), "/" );}
	
	require("./jdincfunctions/functionsinc.php"); checkCookiesAvailability('');
	require("./jdincfunctions/findprofileinc.php");
	
	$sitehomepage = 'welcomepage';
	$settingsArr = loadSettings();
	if($settingsArr['sitehomepage']=='welcomepage'){}//ok
	//elseif($settingsArr['sitehomepage']=='newssection'){redirects(16,'?mostrecent');}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>.::jAMES dOE::.</title>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsersettings.php?e=".hash('sha256', "javascript")?>"></noscript>
<script type="text/javascript" src="./jdscripts/mootools/mootools.js"></script>
<script type="text/javascript" src="./jdscripts/initalertinc.js"></script>
<script type="text/javascript" src="./jdscripts/layoutinc.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class='index'>
<div id="wrapper">
	<?=sidebar();?>
    <div id="content">
    	<div id="spacer"></div>
        <div id="alertcontent"><img src='./jdlayout/images/closebutt.png' id='alertclosebutt' title='close alert' alt="close alert"/><div class='alertbanners'>warning</div><div id='alertmessage'></div></div>
    	<div id="maincolumn">
        	<?php 
				$welcomeImageHTML = displayWelcomePageElements($profileID);
				if(strtolower($profileVarsArr['display_welcomepagetext'])=='null'){$profileVarsArr['display_welcomepagetext']=NULL; $welcomeTextHTML='';}
				else{$welcomeTextHTML='<div id="welcomepagetext">'.$profileVarsArr['display_welcomepagetext'].'</div>';}
				
				if($welcomeImageHTML=='empty'){
					if($profileVarsArr['display_welcomepagetext']==NULL){redirects(16,'?mostrecent');} else{echo $welcomeTextHTML;}
				}
				else{echo $welcomeImageHTML; echo $welcomeTextHTML;} 
			?>
        </div>
	</div><!--content-->
</div><!--wrapper-->
</body>
</html>
