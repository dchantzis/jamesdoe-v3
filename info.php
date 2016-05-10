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

	$csrf_password_generator = hash('sha256', "info").CSRF_PASS_GEN;
	
	//whereUgo(6);
	
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
<script type="text/javascript" src="./jdscripts/layoutinc.js"></script>
<script type="text/javascript" src="./jdscripts/info.js"></script>
<script type="text/javascript" src="./jdscripts/validatensubmitinc.js"></script>
<script type="text/javascript" src="./jdscripts/php.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class='info'>
	<div id="wrapper">
    <?=sidebar();?>
    <div id='topbar'></div>
    <div id="content">
		<div id="pagetitle">
        	<ul class='secondarynavi'>
                <li class='highlight' id='secondarynaviimagename'>info</li>
            </ul>
        </div>
    	<div id="spacer"></div>
        <div id="alertcontent"><img src='./jdlayout/images/closebutt.png' id='alertclosebutt' title='close alert' alt="close alert"/><div class='alertbanners'>warning</div><div id='alertmessage'></div></div>
		<div id="maincolumn">
            <div class='infobanners'>info</div>
        	<ul class='infoelements'>
            	<li class='infoprofilepicture'><img src='./jdimages/profileimage.png' /></li>
                <li class='infobio'><?=$profileVarsArr['display_information']?></li>
            </ul>
            
            <div class='clearboth'></div>
            <div class='contactmebanners'>Contact Me</div>           
            <div class='contactmeinfo'>
				You can contact me at
				<div class='emails'><a href='mailto:<?=$profileVarsArr['email']?>' title='Email me' class='emailme'><?=$emailAddress?></a></div>
                or you can use the form on the right.
				<br /><br />
				Whichever you use, I'll get the message...
          	</div>
            <div class='sendcontactfrm'>
                <ul id='contactfrm'>
                    <li id='senderfailed' class='hidden'></li>
                    <li><input class='text' type='text' name='sender_name' id='sender_name' maxlength='40' value='[type your name][required]' /></li>
                    <li><input class='text' type='text' name='sender_email' id='sender_email' maxlength='70' value='[type your email][required]' /></li>
                    <li><input class='text' type='text' name='sender_regarding' id='sender_regarding' maxlength='100' value='[regarding][not required]' /></li>
                    <li><textarea class='text' name='sender_message' id='sender_message' cols='' rows=''>[type your message][required]</textarea></li>
                    <li class='charcounters'><span class='counters' id='scounter'><?=CONTACT_MESSAGE_MAX_LENGTH?></span> remaining characters</li>
                    <li><input type='checkbox' name='sender_cc' id='sender_cc' value='on' />Send CC to self [not required]</li>
                    <li><input class='button' type='submit' name='sender_send' id='sender_send' value='send' /></li>
                </ul>
                <div id='sentemailanchor'></div>
                <span id='senderloader' class='hidden'></span>
            </div>
      </div>
        </div><!--mainColumn-->
		<div id='mainfooter'></div>
        <span id='csrf' class='hidden'><?=$csrf_password_generator?></span>
    </div><!--content-->
    </div><!--wrapper-->
</body>
</html>