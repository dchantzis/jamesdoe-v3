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
	
	$csrf_password_generator = hash('sha256', "fuerrors").CSRF_PASS_GEN;
	
	//whereUgo(6);
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
	
        if(isset($_SESSION['DATABASEERROR']) && !isset($_SESSION['ERR'])) {
            $_SESSION['ERR'] = $_SESSION['DATABASEERROR'];
        }
        
	//if(!isset($_SESSION['ERR'])){redirects(0,'');}
	//else{$errVars = $_SESSION['ERR']; unset($_SESSION['ERR']);}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>.::jAMES dOE::.</title>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsersettings.php?e=".hash('sha256', "javascript")?>"></noscript>
<script type="text/javascript" src="./jdscripts/mootools/mootools.js"></script>
<script type="text/javascript" src="./jdscripts/commonfunctionsinc.js"></script>
<script type="text/javascript" src="./jdscripts/commonajaxfunctionsinc.js"></script>
<script type="text/javascript" src="./jdscripts/layoutinc.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class='fuerrors'>
	<div id='wrapper'>
    <?php echo sidebarSlim(); ?>
    <div id='topbar'></div>
    <div id='content'>
		<div id="pagetitle">
        	<ul class='secondarynavi'>
                <li class='highlight'>errors</li>
            </ul>
        </div>
    	<div id="spacer"></div>
		<div id="maincolumn">
            
			<div class='fuerrorsbanners'>
                <span class='red'>ERROR </span>
                <?php 
                if($errVars['TYPE']=='Database'){echo $errVars['CODE'].": Database error"; } 
                if($errVars['TYPE']=='Security'){echo $errVars['CODE'].": Security error"; } 
                ?>
			</div>
        	<div class='fuerrorssubtitle'><?=$errVars['MESSAGE']?></div>
            <div class='fuerrorssubtitle'><?=$errVars['DATABASEERROR']?></div>
            <div class="notes">
                Sorry for the inconvenience. <br />
                The system administrators has been notified about this error.<br />
				Try refreshing the page or hit F5. <br />
                If that doesn't seem to work, visit us soon again!
            </div>
		</div><!--mainColumn-->
		<div id='mainfooter'></div>
    </div><!--content-->
    </div><!--wrapper-->
    <span id='csrf' class='hidden'><?=$csrf_password_generator?></span>
</body>
</html>
