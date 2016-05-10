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

	unset($_SESSION['IMAGESORDER']);
	if(!isset($_SESSION['IMAGESORDER'])){loadImagesOrder();}
	if(!isset($_SESSION['ALBUMCOVERS'])){loadImagesOrder();}
	if(!isset($_SESSION['COVERS'])){loadImagesOrder();}
	$currentImageVars = array();
	$currentImageID = $_SESSION['ALBUMCOVERS'][$categoryID][$albumID];
	$csrf_password_generator = hash('sha256', "images").CSRF_PASS_GEN;
	
	//load the image that was selected in the albums.php page
	if(isset($_SESSION['IMGID']))
		{$currentImageID=$_SESSION['IMGID']; $currentImageVars=findImage($categoryID,$albumID,$currentImageID); $imagePosition='?'; $albumImagesCount='?';}
	elseif(!isset($_SESSION['IMGID']))
		{$currentImageVars = $_SESSION['COVERS'][$currentImageID]; $imagePosition='cover'; $albumImagesCount=$currentImageVars['albumimagescount'];}//load the album cover
	
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
<script type="text/javascript" src="./jdscripts/getimagesinc.js"></script>
<script type="text/javascript" src="./jdscripts/imagesinc.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class='images'>
	<div id="wrapper">
    <?=sidebar();?>
    <div id='topbar'></div>
    <div id="content">
		<div id="pagetitle">
        	<ul class='secondarynavi'>
                <li><a href='./categories.php?categoryid=<?=$categoryID?>' title='Category'><?=$albumVarsArr['tagname']?></a> &gt;</li>
                <li><a href='./albums.php?categoryid=<?=$categoryID?>&albumid=<?=$albumID?>' title='Album'><?=$albumVarsArr['name']?></a> &gt;</li>
                <li class='highlight' id='secondarynaviimagename'><?=$currentImageVars['name']?></li>
            </ul>
            <ul class='albumnavi'>
            	<li id='previousimage' title='Previous Image'>&lt; prev</li>
                <li id='count'>
                	<span id='imageposition'><?=$imagePosition?></span> of <span id='albumimagescount'><?=$albumImagesCount?></span>
                </li>
                <li id='nextimage' title='Next Image'>next &gt;</li>
            </ul>
        </div>
    	<div id="spacer"></div>
        <div id="alertcontent"><img src='./jdlayout/images/closebutt.png' id='alertclosebutt' title='close alert' alt="close alert"/><div class='alertbanners'>warning</div><div id='alertmessage'></div></div>
		<div id="maincolumn">
		<?php
        	if(!isset($_SESSION['IMAGESORDER'][$categoryID][$albumID]))
				{echo "<span class='noimagesnote'>"."no images in this album yet. Come back again!"."</span>";}
			else
			{
		?>
            <ul class='imageul'>
                <li class='imagenames'><span id='imagename'><?=$currentImageVars['name']?></span></li>
                <li>
                	<div id='enlargenote'>[ click on image to enlarge ]</div>
                	<div id='infonote'><a href='#imagedescription'>view image info</a></div>
                </li>
				<li class='imagefileurls' id='imagefileurlli'>
                    <div id='loadnextimage' class='hidden'>&nbsp;</div>
                    <img width='<?=IMAGES_FULL_RESOLUTION_PIXELS?>' src='./jdimages/fullresolution/<?=$currentImageVars['fileurl']?>' id='imagefileurl'/>
                </li>
                <li class='imagedescriptions'><span id='imagedescription'><?=nl2br($currentImageVars['description'])?></span></li>
                <li class='imagetags'><div id='imagetagsbanner'>Tags: </div>
                	<span id='imagetags'>
						<?php if($currentImageVars['tags']==''){echo '-';}else{echo getImageTagNames($currentImageVars['tags'],'php');} ?>
                	</span>
                </li>
                <li class='imagesubmitiontimestamps'>
					<span id='imagesubmitiontimestamp'>Added: <?=$currentImageVars['submitiontimestamp']?></span>
				</li>
			</ul>
		<?php } ?>
		</div><!--mainColumn-->
		<div id='mainfooter'></div>
    </div><!--content-->
    </div><!--wrapper-->
    <span id='category' class='hidden'><?=$categoryID?></span>
    <span id='album' class='hidden'><?=$albumID?></span>
    <span id='image' class='hidden'><?=$currentImageID?></span>
    <span id='csrf' class='hidden'><?=$csrf_password_generator?></span>
</body>
</html>
