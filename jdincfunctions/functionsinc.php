<?php
session_start();

//load functions
require("jdconfiginc.php");
require("editdbfunctionsinc.php");
require("selectdbadminc.php");
require("loginlogoutinc.php");
require("commonfunctionsinc.php");
require("uploaddeleteinc.php");
require("layoutinc.php");



reset($_GET); //resets the pointer to the $_GET table
if(isset($_GET['type']))
{ 
	if(!preg_match("/^[0-9]([0-9]*)/",$_GET['type'])){$_GET['type'] = NULL; }
	else{$get_type = $_GET['type']; }
	unset($_GET['type']);
}else { $get_type = NULL; }

switch($get_type)
{
	case 3:
		editCategories('update','ajax');
		break;
	case 4:
		editCategories('insert','ajax');
		break;
	case 5:
		editAlbums('update','ajax');
		break;
	case 6:
		editAlbums('insert','ajax');
		break;
	case 7:
		editImages('update','ajax');
		break;
	case 8:
		uploadImages();
		break;
	case 9:
		deleteImages();
		break;
	case 10:
		deleteAlbums();
		break;
	case 11:
		deleteCategories();
		break;
	case 12:
		searchNsuggest();
		break;
	case 13:
		editPosts('update','ajax');
		break;
	case 14:
		editPosts('insert','ajax');
		break;
	case 15:
		deletePosts();
		break;
	case 16:
		getImage('previous');
		break;
	case 17:
		getImage('next');
		break;
	case 19:
		findImage(imageID);
		break;
	case 20;	
		validateImageID('albumsinc.js');
		break;
	case 21:
		validateImageID('postsinc.js');
		break;
	case 22:
		getPostElements();
		break;
	case 23:
		//previous 10 posts button
		$_SESSION['postslimit']=$_SESSION['postslimit']+10;
		redirects(12,'');
		break;
	case 24:
		//next 10 posts button
		$_SESSION['postslimit']=$_SESSION['postslimit']-10;
		redirects(12,'');
		break;
	case 25:
		adminLogin();
		break;
	case 26:
		adminLogout();
		break;
	case 27:
		$postID=$_GET['postid']; unset($_GET['postid']);
		if(!preg_match("/^[0-9]([0-9]*)/",$postID)){redirects(0,'');}
		$_SESSION['displayPosts']=TRUE;
		redirects(14,'?postid='.$postID.'#displaycomment_'.$postID);
		break;
	case 28:
		$postID=$_POST['postid']; unset($_POST['postid']);
		if(!preg_match("/^[0-9]([0-9]*)/",$postID)){redirects(0,'');}
		editComments('insert','php',$postID);
		break;
	case 29:
		$postID=$_GET['postid']; unset($_GET['postid']);
		if(!preg_match("/^[0-9]([0-9]*)/",$postID)){redirects(0,'');}
		else{redirects(15,'?postid='.$postID);}
		break;
	case 30:
		deleteComments();
		break;
	case 31:
		editAlbumVisibility();
		break;
	case 32:
		editSettings();
		break;
	case 33:
		editProfiles();
		break;
	case 34:
		validateImageID('tags.php');
		break;
	case 35:
		getTaggedImage('previous');
		break;
	case 36:
		getTaggedImage('next');
		break;
	case 37:
		//previous 10 comments button
		$_SESSION['ualcommentslimit']=$_SESSION['ualcommentslimit']+20;
		redirects(22,'');
		break;
	case 38:
		//next 10 comments button
		$_SESSION['ualcommentslimit']=$_SESSION['ualcommentslimit']-20;
		redirects(22,'');
		break;
	case 39:
		//previous 10 routine entries button
		$_SESSION['ualroutinelimit']=$_SESSION['ualroutinelimit']+30;
		redirects(23,'');
		break;
	case 40:
		//next 10 routine entries button
		$_SESSION['ualroutinelimit']=$_SESSION['ualroutinelimit']-30;
		redirects(23,'');
		break;
	case 41:
		//previous 10 error entries button
		$_SESSION['ualerrorlimit']=$_SESSION['ualerrorlimit']+20;
		redirects(24,'');
		break;
	case 42:
		//next 10 error entries button
		$_SESSION['ualerrorlimit']=$_SESSION['ualerrorlimit']-20;
		redirects(24,'');
		break;
	case 43:
		deleteUsersActionLogEntries('routine');
		break;
	case 44:
		deleteUsersActionLogEntries('error');
		break;
	case 45:
		contactJD('ajax');
		break;
	default:
		//IMPORTANT TO HAVE NO ACTION
		return -1;
		break;
}//switch

//function with different headers for redirection purposes
function redirects($r_id,$flags)
{
	if(!preg_match("/^[0-9]([0-9]*)/",$r_id)){ $r_id = NULL; }
	else {}//do nothing. ALL OK
	
	switch($r_id)
	{
		case 0:
			header("Location: ./index.php".$flags);
			exit;
			break;
		case 10:
			header("Location: ../mmeditimages.php".$flags);
			exit;
			break;
		case 11:
			header("Location: ../mmeditalbums.php".$flags);
			exit;
			break;
		case 12:
			header("Location: ../mmeditposts.php".$flags);
			exit;
			break;
		case 13:
			header("Location: ../images.php".$flags);
			exit;
			break;
		case 14:
			header("Location: ../posts.php".$flags);
			exit;
			break;
		case 15:
			header("Location: ../mmeditcomments.php".$flags);
			exit;
			break;
		case 16:
			header("Location: ./posts.php".$flags);
			exit;
			break;
		case 17:
			header("Location: ../taggedimages.php".$flags);
			exit;
			break;
		case 18:
			header("Location: ./tags.php".$flags);
			exit;
			break;
		case 19:
			header("Location: ../mmeditwelcomepage.php".$flags);
			exit;
			break;
		case 20:
			header("Location: ./browsersettings.php".$flags);
			exit;
			break;
		case 21:
			header("Location: ./fuerrors.php".$flags);
			exit;
			break;
		case 22:
			header("Location: ../mmualeditcomments.php".$flags);
			exit;
			break;
		case 23:
			header("Location: ../mmeditualroutine.php".$flags);
			exit;
			break;
		case 24:
			header("Location: ../mmeditualerrors.php".$flags);
			exit;
			break;
		default:
			//do nothing
			break;
	}//switch
}//Redirects()
?>