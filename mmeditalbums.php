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
	
	$csrf_password_generator = hash('sha256', "mmeditalbums").CSRF_PASS_GEN;
	
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
<script type="text/javascript" src="./jdscripts/searchnsuggestinc.js"></script>
<script type="text/javascript" src="./jdscripts/mmeditalbumsinc.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class="mmeditalbums">
	<div id="wrapper">
    <?=sidebar();?>
    <div id="content">
    	<div id='topbar'></div>
		<div id="pagetitle">
        	<ul class='secondarynavi'>
            	<li><a href='./mmeditimages.php'>images</a> &gt;</li>
                <li><a href='./mmeditimages.php'><?=$albumVarsArr['tagname']?></a> &gt;</li>
                <li class='highlight' id='albumname'><?=$albumVarsArr['name']?></li>
            </ul>
            <div id='helpbutt'><a href="#" id="helpbutt">help</a><div id='helpline'></div></div>
        </div>
    	<div id="spacer"></div>
        <div id="helpcontent">
        	<img src='jdlayout/images/closebutt.png' id='helpclosebutt' title='close help' alt="close help"/>
        	<div class='helpbanners'>help</div>
			<?php helpContentsAdm('mmeditalbums');?>
        </div>
        <div id="alertcontent"><img src='./jdlayout/images/closebutt.png' id='alertclosebutt' title='close alert' alt="close alert"/><div class='alertbanners'>warning</div><div id='alertmessage'></div></div>
        <div id="maincolumn">
          <div id='album'>
           	<ul class='albumbanners' id='albumbanner_<?=$albumID?>'>
				<li class='albumcovers'>
					<img id='cover' src='<?=$albumVarsArr['albumcoverfullpath']?>' alt='Album Cover' />
					<span class='hidden' id='ncat_<?=$albumID?>'><?=$categoryID?></span>
                    <span class='hidden' id='album_coverid_<?=$albumID?>'><?=$albumVarsArr['coverid']?></span>
				</li>
                <li class='albumnames'>
					<span class='editalbumnames' id='editalbumname_<?=$albumID?>' title='Album Name'><?=$albumVarsArr['name']?></span>
					<span class='albumfrms' id='albumnamefrm_<?=$albumID?>'>
						<input class='text' type='text' name='album_name_<?=$albumID?>' id='album_name_<?=$albumID?>' value='' maxlength='25' wrap='soft' title='Album Name'/>
						<input class='button' type='button' id='albumnamesubmitbutt_<?=$albumID?>' value='save' />
						<input class='button' type='button' id='albumnamecancelbutt_<?=$albumID?>' value='cancel' />
                        <span id='album_name_<?=$albumID?>failed' class='hidden'></span>
					</span>
					<span id='albumnameloader_<?=$albumID?>' class='hidden'></span>
				</li>
				<li class='albumdescriptions'>
					<span class='editalbumdescriptions' id='editalbumdescription_<?=$albumID?>' title='Album Description'><?=$albumVarsArr['description']?></span>
					<span class='albumfrms' id='albumdescriptionfrm_<?=$albumID?>'>
						<textarea class='text' name='album_description_<?=$albumID?>' id='album_description_<?=$albumID?>' cols='21' rows='4' wrap='soft' title='Album Description'></textarea>
                   		<span class='charcounters'><span class='counters' id='acounter_<?=$albumID?>'><?=ALBUM_DESCRIPTION_MAX_LENGTH?></span> remaining characters</span>
						<input class='button' type='button' id='albumdescriptionsubmitbutt_<?=$albumID?>' value='save' />
						<input class='button' type='button' id='albumdescriptioncancelbutt_<?=$albumID?>' value='cancel' />
						<span id='album_description_<?=$albumID?>failed' class='hidden'></span>
					</span>
					<span id='albumdescriptionloader_<?=$albumID?>' class='hidden'></span>
				</li>
			</ul><!--albumbanner-->
            <div class='albumvisibility'>
				<?php 
					if($albumVarsArr['visibility']=='true'){echo 'Album is visible to visitors';}
					else{echo 'Album is <span class="highlight">not</span> visible to visitors';}
				?>
            </div>
			<div class='editablumupdateds' id='editalbumupdated_<?=$albumID?>'>Updated: <?=$albumVarsArr['lastupdatedtimestamp']?></div>
			
            <?php if(($albumVarsArr['tagsdescription']!='postsalbum')&&($albumVarsArr['tagsdescription']!='welcomepageimagesalbum')){?>
			<ul class='uploadimagesections'>
				<li class='uploadimages' id='uploadimage_<?=$albumID?>'>
					<form id='uploadimagesfrm' name='uploadimagesfrm' method='post' action='./jdincfunctions/functionsinc.php?type=8' enctype='multipart/form-data'>
					Select images to upload: <span id='inputplaceholder'><input id='imagefile_0' name='imagefile_0' class='text' type='file' size='41' title='Browse image' /></span>
                    <input type='hidden' name='albumid' class='hidden' value='<?=$albumID?>' />
                    <input type='hidden' name='categoryid' class='hidden' value='<?=$categoryID?>'/>
                    <input type='hidden' name='csrf' class='hidden' value='<?=$csrf_password_generator.'uploadimage'?>' />
                    <input type='hidden' name='pageid' class='hidden' value='mmeditalbums' />
					<span id='submituploadfrm'></span>
                    <input id='submituploadfrmbutt' type='submit' class='button' value='upload' />              
                    </form>
					<ul class='imagestoupload' id='imagestoupload'></ul>
				</li>
				<li class='supportedimagefiletypes'>Select up to 5 images.<br />Supported image filetypes: jpg, gif, png</li>
			</ul>
            <?php } ?>
            <?php 
				if($albumVarsArr['tagsdescription']=='postsalbum')
					{echo "<div class='albumnotes'>This category is automatically generated to store the images that are uploaded from the <span class='bold'>'Posts'</span> section. </div>";}
            	elseif($albumVarsArr['tagsdescription']=='welcomepageimagesalbum')
					{echo "<div class='albumnotes'>This category is automatically generated to store the images that are uploaded from the <span class='bold'>'Welcome Page'</span> section. </div>";}
			?>
			<?php uploadedImagesResults();?>
			<?php loadAlbumImagesAdm($albumID,$categoryID); ?>
          
          </div><!--album-->
        </div><!--mainColumn-->
		<div id='mainfooter'></div>
    </div><!--content-->
    </div><!--wrapper-->
    <span id='aid' class='hidden'><?=$albumID?></span>
	<span id='csrf' class='hidden'><?=$csrf_password_generator?></span>
</body>
</html>
