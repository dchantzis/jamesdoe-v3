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
	$_SESSION['mmeditcommentsVisited'] = 'true';
	require("./jdincfunctions/findpostinc.php");
	unset($_SESSION['mmeditcommentsVisited']);
	
	$csrf_password_generator = hash('sha256', "mmeditcomments").CSRF_PASS_GEN;
	
	whereUgo(0);
	
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
	
	//initialize seome session variables to prevent PHP throwing Notices
	//if(!isset($_SESSION['formvalues'])) { $_SESSION['formvalues']['name'] = ''; $_SESSION['formvalues']['description'] = ''; }//if
	//if(!isset($_SESSION['errorformvalues'])) { $_SESSION['errorformvalues']['name'] = 'hidden'; $_SESSION['errorformvalues']['description'] = 'hidden'; }//if
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
<script type="text/javascript" src="./jdscripts/mmeditcommentsinc.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class="mmeditcomments">
	<div id="wrapper">
    <?=sidebar();?>
    <div id='topbar'></div>
    <div id="content">
    	<div id="pagetitle">
        	<ul class='secondarynavi'>
                <li><a href='./mmeditposts.php'>posts</a> &gt;</li>
                <li><a href='./mmeditposts.php'><?=$postVarsArr[0]['headline']?></a> &gt;</li>
                <li class='highlight' id='secondarynaviimagename'>comments</li>
            </ul>
            <div id='helpbutt'><a href="#" id="helpbutt">help</a><div id='helpline'></div></div>
        </div>
    	<div id="spacer"></div>
        <div id="helpcontent">
        	<img src='jdlayout/images/closebutt.png' id='helpclosebutt' title='close help' alt="close help"/>
        	<div class='helpbanners'>help</div>
			<?php helpContentsAdm('mmeditcomments');?>
        </div>
        <div id="alertcontent"><img src='./jdlayout/images/closebutt.png' id='alertclosebutt' title='close alert' alt="close alert"/><div class='alertbanners'>warning</div><div id='alertmessage'></div></div>
        <div id="maincolumn">
			<ul class='postul'>
            <li class='postheadlines'>
				<?=$postVarsArr[0]['headline']?><div class='biglines'></div>
            </li>
            <li><?php if($postVarsArr[0]['type']=='imagesupdate'){echo "<span class='posttypes'>images update</span>";} ?></li>
            <li class='postcreationtimestamps'><?=$postVarsArr[0]['reallylongcreationtimestamp']?></li>
               <?php 
				if($postVarsArr[0]['body']!=''&&strtolower($postVarsArr[0]['body'])!='null')
				{echo '<li class="postbodies">'.$postVarsArr[0]['display_body'].'</li>';
				echo '<li class="postsignature">'.'- '.POST_SIGNATURE.'</li>';}
				?>
               <li><?=$postVarsArr[0]['imagesthumbnails']?></li>
               <li class='postulclear'></li>
			</ul>
        	
            <div class='responses' id='responses'>
            	<div class='responsesbanners'>comments</div>
				<?php displayPostComments($postVarsArr[0]['id']);?>
    		</div>
        </div><!--mainColumn-->
		<div id='mainfooter'></div>
    </div><!--content-->
    </div><!--wrapper-->
    <span id='csrf' class='hidden'><?=$csrf_password_generator?></span>
</body>
</html>