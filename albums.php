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
	require('./jdincfunctions/findalbuminc.php');
	
	if($albumVarsArr['visibility']=='false'){redirects(0,'');}
	
	$csrf_password_generator = hash('sha256', "albums").CSRF_PASS_GEN;
	
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
<script type="text/javascript" src="./jdscripts/albumsinc.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class='albums'>
	<div id='wrapper'>
    <?=sidebar();?>
    <div id='topbar'></div>
    <div id='content'>
		<div id='pagetitle'>
        	<ul class='secondarynavi'>
                <li><a href='./categories.php?categoryid=<?=$categoryID?>'><?=$albumVarsArr['tagname']?></a> &gt;</li>
                <li class='highlight'><?=$albumVarsArr['name']?></li>
            </ul>
        </div>
    	<div id="spacer"></div>
        <div id="alertcontent"><img src='./jdlayout/images/closebutt.png' id='alertclosebutt' title='close alert' alt="close alert"/><div class='alertbanners'>warning</div><div id='alertmessage'></div></div>
		<div id="maincolumn">
            <ul class='albumul'>
            	<li class='albumnames'><?=$albumVarsArr['name']?></li>
                <?php if($albumVarsArr['display_description']=='' || strtolower($albumVarsArr['display_description'])!="null") {?> 
				<li class='albumcovers'><img src='<?=$albumVarsArr['albumcoverfullpath']?>' alt='Album Cover' title='Album Cover'/></li>
                <li class='albumdescriptions'>
                    <?=$albumVarsArr['display_description']?>
                </li>
                <?php }?>
                <li class='albumupdatedtimestamps'>Updated: <?=$albumVarsArr['lastupdatedtimestamp']?></li>
			</ul>
            <div class='albumimagesbanners'>Album Images<div class='biglines'></div></div>
            <?php loadImageThumbnails($categoryID, $albumID, $albumVarsArr['imagesorder']); ?>
		</div><!--mainColumn-->
		<div id='mainfooter'></div>
    </div><!--content-->
    </div><!--wrapper-->
    <span id='category' class='hidden'><?=$categoryID?></span>
    <span id='album' class='hidden'><?=$albumID?></span>
    <span id='csrf' class='hidden'><?=$csrf_password_generator?></span>
</body>
</html>
