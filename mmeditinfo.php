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
	
	$csrf_password_generator = hash('sha256', "mmeditinfo").CSRF_PASS_GEN;
	
	whereUgo(0);
	
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>.::jAMES dOE::.</title>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsersettings.php?e=".hash('sha256', "javascript")?>"></noscript>
<script type="text/javascript" src="./jdscripts/mootools/mootools.js"></script>
<script type="text/javascript" src="./jdscripts/initalertinc.js"></script>
<script type="text/javascript" src="./jdscripts/commonfunctionsinc.js"></script>
<script type="text/javascript" src="./jdscripts/commonajaxfunctionsinc.js"></script>
<script type="text/javascript" src="./jdscripts/php.js"></script>
<script type="text/javascript" src="./jdscripts/layoutinc.js"></script>
<script type="text/javascript" src="./jdscripts/inithelpinc.js"></script>
<script type="text/javascript" src="./jdscripts/validatensubmitinc.js"></script>
<script type="text/javascript" src="./jdscripts/deleteelementsinc.js"></script>
<script type="text/javascript" src="./jdscripts/mmeditinfoinc.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class="mmeditinfo">
	<div id="wrapper">
    <?=sidebar();?>
    <div id='topbar'></div>
    <div id="content">
    	<div id="pagetitle">
        	<ul class='secondarynavi'>
                <li class='highlight'>info</li>
            </ul>
            <div id='helpbutt'><a href="#" id="helpbutt">help</a><div id='helpline'></div></div>
        </div>
    	<div id="spacer"></div>
        <div id="helpcontent">
        	<img src='./jdlayout/images/closebutt.png' id='helpclosebutt' title='close help' alt="close help"/>
        	<div class='helpbanners'>help</div>
			<?=helpContentsAdm('mmeditinfo');?>
		</div>
        <div id="alertcontent"><img src='./jdlayout/images/closebutt.png' id='alertclosebutt' title='close alert' alt="close alert"/><div class='alertbanners'>warning</div><div id='alertmessage'></div></div>
  		<div id="maincolumn">
        	<div class='editinfobanners'><span id='banner'>Profile Info:</span></div>
            
            <ul class='infoelements'>
            	<li class='infoelement'>
                	<span class='editprofileinformations' id='editprofileinformation_<?=$profileID?>' title='Welcome Page Text'><?=$profileVarsArr['information']?></span>
                	
                    <span class='profileinformationfrms' id='profileinformationfrm_<?=$profileID?>'>
                    	<textarea class='text' name='profile_information_<?=$profileID?>' id='profile_information_<?=$profileID?>' cols='21' rows='10' wrap='soft' title='Welcome Page Text'></textarea>
                        <input class='button' type='button' id='profileinformationsubmitbutt_<?=$profileID?>' value='save' />
						<input class='button' type='button' id='profileinformationcancelbutt_<?=$profileID?>' value='cancel' />
                    	<span class='charcounters'><span class='counters' id='picounter_<?=$profileID?>'><?=PROFILE_INFO_MAX_LENGTH?></span> remaining characters</span>
                        <span id='profile_information_<?=$profileID?>failed' class='hidden'></span>
                    </span>
                    <span id='profileinformationloader_<?=$profileID?>' class='hidden'></span>
                </li>
			</ul>
    	</div><!--mainColumn-->
		<div id='mainfooter'></div>
    </div><!--content-->
    </div><!--wrapper-->
    <span id='profile' class='hidden'><?=$profileID?></span>
    <span id='csrf' class='hidden'><?=$csrf_password_generator?></span>
</body>
</html>
