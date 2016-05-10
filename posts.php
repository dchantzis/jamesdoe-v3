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
	require("./jdincfunctions/findpostinc.php");
	
	$csrf_password_generator = hash('sha256', "posts").CSRF_PASS_GEN;
	
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
<script type="text/javascript" src="./jdscripts/postsinc.js"></script>
<script type="text/javascript" src="./jdscripts/validatensubmitinc.js"></script>
<script type="text/javascript" src="./jdscripts/deleteelementsinc.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class='posts'>
	<div id='wrapper'>
    <?=sidebar();?>
    <div id='topbar'></div>
    <div id='content'>
		<div id='pagetitle'>
        	<?php 
				if(isset($_GET['mostrecent'])){
					echo "<ul class='secondarynavi'>
                    	<li><a href='./postsarchive.php' title='Posts Archive'>news</a> &gt;</li>
                    	<li class='highlight'>latest news</li>
                		</ul>";
				}
				else
				{
				 	echo "<ul class='secondarynavi'>
                    	<li><a href='./postsarchive.php' title='Posts Archive'>news</a> &gt;</li>
                    	<li class='highlight'>".$postVarsArr[0]['headline']."</li>
                		</ul>";
				}
			?>
        </div>
    	<div id='spacer'></div>
        <div id="alertcontent"><img src='./jdlayout/images/closebutt.png' id='alertclosebutt' title='close alert' alt="close alert"/><div class='alertbanners'>warning</div><div id='alertmessage'></div></div>
		<div id='maincolumn'>
        	<div class='postbanners'>news</div>
            <ul class='postul'>
            	<?php for($i=0; $i<$num; $i++){ //the num value is set in the findpostinc.php file ?>
            	<li class='postheadlines'>
					<?php if($postVarsArr[$i]['type']=='imagesupdate'){echo "<span class='posttypes'>images update</span>";} ?>
					<span class='displayposts' id='displaypost_<?=$postVarsArr[$i]['id']?>'><?=$postVarsArr[$i]['headline']?></span><div class='biglines'></div>
                </li>
                <li class='postcreationtimestamps'><?=$postVarsArr[$i]['shortcreationtimestamp']?></li>
                <?php 
					if($postVarsArr[$i]['display_body']!=''&&strtolower($postVarsArr[$i]['display_body'])!='null')
					{echo '<li class="postbodies">'.$postVarsArr[$i]['display_body'].'</li>';
					echo '<li class="postsignature">'.'- '.POST_SIGNATURE.'</li>';}
				?>
                <li><?=$postVarsArr[$i]['imagesthumbnails']?></li>
                <?php if($settingsArr['postscommentsstatus']=='active'){ ?>
                <li class='displaycomments' id='displaycomment_<?=$postVarsArr[$i]['id']?>'><span class='red' id='commentcounter'><?=$postVarsArr[$i]['commentscounter']?></span> <span id='commentscounterphrase'><?=$postVarsArr[$i]['commentscounterphrase']?></span></li>
                <?php } ?>
				<?php if(isset($_SESSION['displayPosts']) && $_SESSION['displayPosts'])
						{echo "<li class='leavenewcomment'><a href='#reply'>leave new comment</a></li>";}?>
                <li class='commentsdisplay'>
                	<?php if($settingsArr['postscommentsstatus']=='active'){ ?>
                	<?php if(isset($_SESSION['displayPosts']) && $_SESSION['displayPosts']){displayPostComments($postVarsArr[$i]['id']);} ?>
                    <?php } ?>
                </li>
                <li class='postulclear'></li>
                <!--<span id='5'></span>-->
			  <?php } ?>
            </ul>
            
		</div><!--mainColumn-->
		<div id='mainfooter'>
        	<ul class='postlinks'>
            	<? if(!isset($_GET['mostrecent'])){ ?>
                    <li id='prev'>
                        <? if($prevPostID == NULL) { echo "<span class='strikethrough'>&lt;previous</span>"; }
                            else { echo "<a href='./posts.php?postid=".$prevPostID."' title='Previous Post'>&lt;previous</a>"; } ?>
                    </li>
                    <li id='next'>
                        <? if($nextPostID == NULL) { echo "<span class='strikethrough'>next&gt;</span>"; }
                            else { echo "<a href='./posts.php?postid=".$nextPostID."' title='Next Post'>next&gt;</a>"; } ?>
                    </li>
                    <li id='arch'><a href='./postsarchive.php' title='Posts Archive'>posts archive</a></li>
                <? }else{ ?>
                    <li id='prev'>
                        <? if($prevPostID == NULL) { echo "<span class='strikethrough'>&lt;previous</span>"; }
                            else { echo "<a href='./posts.php?postid=".$prevPostID."' title='Previous Post'>&lt;previous</a>"; } ?>
					<li id='archalone'><a href='./postsarchive.php' title='Posts Archive'>posts archive</a></li>
				<? } ?>
            </ul>
        </div>
    </div><!--content-->

    </div><!--wrapper-->
    <!--
    <span id='postimagescategoryid' class='hidden'><?php //$postImagesCategoryID ?></span>
    <span id='postimagesalbumid' class='hidden'><?php //$postImagesAlbumID?></span>
    -->
    <span id='csrf' class='hidden'><?=$csrf_password_generator?></span>
</body>
</html>