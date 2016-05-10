<?php
###################################################
############layoutinc.php##########################
###################################################

function sidebarNavigation()
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$catVars = array();
	$albumVars = array();
	$profileVars = array();
	$mainNavi = NULL;
	$profileID = 0;
	
	$query00 = "SELECT * FROM tags WHERE type='album' ORDER BY name ASC; ";
	$query01 = "SELECT albums.id, albums.name, albums.description, albums.visibility, albums.tagid, COUNT(images.id) AS albumimagescount "
				." FROM albums,images WHERE albums.id=images.albumid"
				." GROUP BY(albums.id) ORDER BY albums.creationtimestamp DESC; ";
	$query02 = "SELECT * FROM profiles WHERE id='".$profileID."'; ";
	
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'php','','','sidebarNavigation','true');
	$num00 = @mysql_num_rows($result00);
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','sidebarNavigation','true');
	$num01 = @mysql_num_rows($result01);
	$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','sidebarNavigation','true');
	$num02 = @mysql_num_rows($result02);
	
	if($num00!=0){
		for($i; $i<$num00; $i++){
			$tempCatID = @mysql_result($result00,$i,'id');
			$catVars[$tempCatID]['name'] = strtolower(mysql_result($result00,$i,'name'));
			$catVars[$tempCatID]['description'] = @mysql_result($result00,$i,'description');
			$catVars[$tempCatID]['albumscount'] = 0;
			//if(($catVars[$tempCatID]['description']=='postsalbum')&&(DISPLAY_POSTS_IMAGES=='false')){unset($catVars[$tempCatID]);}		
		}//for
	}else{}//do nothing
	
	if($num01!=0){
		for($j; $j<$num01; $j++){
			$tempAlbumID = @mysql_result($result01,$j,'id');
			$albumVars[$tempAlbumID]['name'] = @mysql_result($result01,$j,'name');
			$albumVars[$tempAlbumID]['description'] = @mysql_result($result01,$j,'description');
			$albumVars[$tempAlbumID]['categoryid'] = @mysql_result($result01,$j,'tagid');
			$albumVars[$tempAlbumID]['visibility'] = @mysql_result($result01,$j,'visibility');
			if($albumVars[$tempAlbumID]['visibility']=='true')
				{$catVars[$albumVars[$tempAlbumID]['categoryid']]['albumscount']++;}
			else {unset($albumVars[$tempAlbumID]);}
		}
	}else{}//do nothing
	
	if($num02!=0){
		$profileVars['username']=@mysql_result($result02,0,'username'); $_SESSION['profileUsername'] = $profileVars['username'];
		$profileVars['email']=@mysql_result($result02,0,'email'); $_SESSION['profileEmail'] = $profileVars['email'];
		$profileVars['blog']=@mysql_result($result02,0,'blog');
	}else{}//do nothing
	
	$mainNavi="<ul class='navi' id='mainnavi'>";
		$mainNavi.="<li><a href='./posts.php?mostrecent' title='Latest News'>news</a></li>";
		$counter=1;
		if(isset($catVars))
		{
			reset($catVars);
			while (list($catKey, $catVal)=each($catVars))
			{
				//if($catVars[$catKey]['description']=='postsalbum'){continue;}
				if($catVars[$catKey]['albumscount']>1)
				{
					$mainNavi.="<li class='subnavitogglers'>";
					$mainNavi.="<span id='subnavitoggler_".$counter."' title='Category: ".strtoupper($catVars[$catKey]['name'])."'>".$catVars[$catKey]['name']."</span>";
					$mainNavi.="<ul class='subnavi' id='subnavisection_".$counter."'>";
						reset($albumVars);
						while (list($albumKey, $albumVal)=each($albumVars)){
						if($catKey == $albumVars[$albumKey]['categoryid']){
							$mainNavi.="<li><a href='./albums.php?categoryid=".$catKey."&albumid=".$albumKey."' title='Album: ".strtoupper($albumVars[$albumKey]['name'])."'>".$albumVars[$albumKey]['name']."</a></li>";}}
					$mainNavi.="</ul>";
					$mainNavi.="</li>";
					$counter++;
				}
				elseif($catVars[$catKey]['albumscount']==1)
				{
					reset($albumVars);
					while (list($albumKey, $albumVal)=each($albumVars)){if($catKey == $albumVars[$albumKey]['categoryid']){$tempAlbumID=$albumKey;}}
					$mainNavi.="<li><a href='./albums.php?categoryid=".$catKey."&albumid=".$tempAlbumID."' title='Category: ".strtoupper($catVars[$catKey]['name'])."'>".$catVars[$catKey]['name']."</a></li>";
				}
				elseif($catVars[$catKey]['albumscount']==0)
				{}
				//to display categories that don't have any albums, use this:
				//{$mainNavi.="<li><a href='./images.php' title='Category ".$catVars[$catKey]['name'].".'>".$catVars[$catKey]['name']."</a></li>";}
			}
		}
		$mainNavi.="<li><a id='naviinfo' href='./info.php' title='Personal Info'>info</a></li>";
		/*$mainNavi.="<li class='subnavitogglers'>
				<span id='subnavitoggler_".$counter."' title='Personal info.'>info</span>
				<ul class='subnavi' id='subnavisection_".$counter."'>
					<li><a href='#' title='Biography.'>biography</a></li>
					<li><a href='#' title='Contact.'>contact</a></li>
				</ul></li>";*/
		if($profileVars['blog']!=''&&strtolower($profileVars['blog'])!='null'){
			$mainNavi.="<li><a id='naviblog' href='".$profileVars['blog']."' title='Blog'>blog</a></li>";
		}
	$mainNavi.="</ul>";
	$_SESSION['MAINNAVI']=$mainNavi;
	unset($dbobj);
}//sidebarNavigation()

function siteUpdateTimestamp()
{
	require_once('jddbase.class.php');
	$dbobj = new JDDBase();
	
	$siteUpdateTimestamp = '0000-00-00 00:00:00';
	$albumsLastUpdatedTimeStamp = '0000-00-00 00:00:0';
	$postsLastUpdatedTimeStamp = '0000-00-00 0:0:00';
	
	$query01 = "SELECT lastupdatedtimestamp FROM albums ORDER BY lastupdatedtimestamp DESC; ";
	$query02 = "SELECT submitiontimestamp FROM posts ORDER BY submitiontimestamp DESC; ";
	
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','siteUpdateTimestamp','true');
	$num01 = @mysql_num_rows($result01);
	$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','siteUpdateTimestamp','true');
	$num02 = @mysql_num_rows($result02);
	
	if($num01!=0){$albumsLastUpdatedTimeStamp = mysql_result($result01,0,'lastupdatedtimestamp'); $siteUpdateTimestamp = $albumsLastUpdatedTimeStamp;}
	if($num02!=0){$postsLastUpdatedTimeStamp = mysql_result($result02,0,'submitiontimestamp'); $siteUpdateTimestamp = $postsLastUpdatedTimeStamp;}
	
	if($albumsLastUpdatedTimeStamp>$postsLastUpdatedTimeStamp){$siteUpdateTimestamp = $albumsLastUpdatedTimeStamp;}
	else{$siteUpdateTimestamp = $postsLastUpdatedTimeStamp;}
	
	return convertTimeStamp($siteUpdateTimestamp,'reallyshort');
	
	unset($dbobj);
}//siteUpdateTimestamp()

function sidebar()
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$profileVars = array();
	$explodedArr = array();
	$emailAddress = "";
	$fullSideBar = "";
	
	$query = "SELECT * FROM profiles WHERE id='".$profileID."'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','sidebar','true');
	$num = @mysql_num_rows($result);
	
	if($num!=0){
		$profileVars['username']=@mysql_result($result,0,'username');
		$profileVars['email']=@mysql_result($result,0,'email');
		$explodedArr = explode('@',$profileVars['email'],2);
		$explodedArr[0] = explode('.',$explodedArr[0],10);
		$explodedArr[1] = explode('.',$explodedArr[1],10);
		$profileVars['blog']=@mysql_result($result,0,'blog');
	}else{}//do nothing
	
	$counter=0;
	reset($explodedArr[0]);
	while (list($key, $val) = each ($explodedArr[0])){
		$counter++; 
		if(count($explodedArr[0])>1){if($counter==count($explodedArr[0])){$emailAddress.=$val;} else{$emailAddress.=$val." <span class='highlight'>dot</span> ";}}
		else{$emailAddress.=$val;}
	}
	$emailAddress.=" <span class='highlight'>at</span> ";
	$counter=0;
	reset($explodedArr[1]);
	while (list($key, $val) = each ($explodedArr[1])){
		$counter++;
		if(count($explodedArr[1])>1){if($counter==count($explodedArr[1])){$emailAddress.=$val;}else{$emailAddress.=$val." <span class='highlight'><a href='./mmlogin.php' class='login'>dot</a></span> ";}}
		else{$emailAddress.=$val;}
	}
	
	if(!isset($_SESSION['SIDEBAR'])){
		$sideBar = "<div id='sidebar'>
		<div class='sitebanner' id='sitebanner' title='jAMES dOE'><img src='./jdlayout/images/jamesdoe.png' title='jAMES dOe' /></div>"
		."<ul class='legalshit' id='legalshit'>"
		."<li>UPDATED: <span class='highlight'>".siteUpdateTimestamp()."</span></li>"
		."<li>All images&copy; 1999 - 2009</li>"
		."<li class='emails' id='sidebaremail' title='Email Address'>"."<a href='mailto:".$profileVars['email']."' class='emailme'>".$emailAddress."</a>"."</li>"
		."</ul>";
		$_SESSION['SIDEBAR'] = $sideBar;
	}
	$fullSideBar .= $_SESSION['SIDEBAR'];
	$fullSideBar .= "<div class='separator'></div>";
	
	unset($_SESSION['MAINNAVI']);
	if(!isset($_SESSION['MAINNAVI'])){sidebarNavigation();}
	$fullSideBar .= $_SESSION['MAINNAVI'];
	
	$fullSideBar .= "<div id='adminsidebarplaceholder'></div>";
	if(!isset($_SESSION['ADMINSIDEBAR'])){$_SESSION['ADMINSIDEBAR'] = adminSideBar(); $fullSideBar .= $_SESSION['ADMINSIDEBAR'];}
	else{$_SESSION['ADMINSIDEBAR'] = adminSideBar(); $fullSideBar .= $_SESSION['ADMINSIDEBAR'];}
	

	$tagsUL = displayImageTags();
	if($tagsUL!='<ul></ul>'&&$tagsUL!=''){
		$fullSideBar .= "<div class='separator'></div>";
		$fullSideBar .= "<div id='navitagsbanner'>Image Tags</div>";
		$fullSideBar .= "<div id='navitags'>".$tagsUL."</div>";
	}
	$fullSideBar .= "<div id='sidefooter'></div>";
	$fullSideBar .= "</div>";
	
	return $fullSideBar;
}//sidebar()

function adminSideBar()
{
	$adminSideBar = "";
	
	if((isset($_SESSION['ADMIN_LOGIN'])) && ($_SESSION['ADMIN_LOGIN']==TRUE) &&
	(isset($_SESSION['ADMIN_USERNAME'])) && ($_SESSION['ADMIN_USERNAME']!='') &&
	(isset($_SESSION['ADMIN_PASSWORD'])) && ($_SESSION['ADMIN_PASSWORD']!=''))
	{
		$adminSideBar .= "<div class='logout'><a href='./jdincfunctions/functionsinc.php?type=26' id='logoutbutton'>LogOut</a></div>";
		
		$adminSideBar .=  "<div class='separator'></div>";
		$adminSideBar .=  "<div id='adminmodebanner'>master controls</div>";
		$adminSideBar .=  "<ul class='navi' id='adminnavi'>"
				//."<li title='Edit account setting.'><a href='#'>account</a></li>"
				."<li title='Edit posts'><a href='./mmeditposts.php'>posts</a></li>
				<li title='Edit images'><a href='./mmeditimages.php'>images</a></li>"
				//."<li title='Edit site layout.'><a href='#'>layout</a></li>"
				."<li title='Edit personal info'><a href='./mmeditinfo.php'>info</a></li>
				<li title='Edit site settings'><a href='./mmeditsettings.php'>settings</a></li>
				<li title='Edit site welcome page'><a href='./mmeditwelcomepage.php'>welcome page</a></li>
				<li class='subnavitogglers' title='View site action log'>"
					."<span id='subnavitoggler_0'>"."action log"."</span>"
					."<ul class='subnavi' id='subnavisection_0'>";
				
				$adminSideBar .= "<li><a href='./mmeditualcomments.php' title='View posts comments'>";
				$newComments = displayUsersActionLogEntriesComments('adminnavi');
				if($newComments==0){$adminSideBar .= "comments";}
				else{$adminSideBar .= "comments"."<span class='red'> (".$newComments." new)</span>";}
				$adminSideBar .= "</a></li>"
					."<li><a href='./mmeditualroutine.php' title='View routine system actions'>routine actions</a></li>"
					."<li><a href='./mmeditualerrors.php' title='View system errors'>errors</a></li>"
					."</ul>"
				."</li>"
				."</ul>";
	}//
	return $adminSideBar;
}//adminSideBar()

function sidebarSlim()
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$profileVars = array();
	$explodedArr = array();
	$emailAddress = "";
	$fullSideBar = "";

	$query = "SELECT * FROM profiles WHERE id='".$profileID."'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','sidebar','true');
	$num = @mysql_num_rows($result);
	
	if($num!=0){
		$profileVars['username']=@mysql_result($result,0,'username');
		$profileVars['email']=@mysql_result($result,0,'email');
		$explodedArr = explode('@',$profileVars['email'],2);
		$explodedArr[0] = explode('.',$explodedArr[0],10);
		$explodedArr[1] = explode('.',$explodedArr[1],10);
		$profileVars['blog']=@mysql_result($result,0,'blog');
	}else{}//do nothing
	
	$counter=0;
	reset($explodedArr[0]);
	while (list($key, $val) = each ($explodedArr[0])){
		$counter++; 
		if(count($explodedArr[0])>1){if($counter==count($explodedArr[0])){$emailAddress.=$val;} else{$emailAddress.=$val."<span class='highlight'>dot</span>";}}
		else{$emailAddress.=$val;}
	}
	$emailAddress.="<span class='highlight'>at</span>";
	$counter=0;
	reset($explodedArr[1]);
	while (list($key, $val) = each ($explodedArr[1])){
		$counter++;
		if(count($explodedArr[1])>1){if($counter==count($explodedArr[1])){$emailAddress.=$val;}else{$emailAddress.=$val."<span class='highlight'><a href='./mmlogin.php' class='login'>dot</a></span>";}}
		else{$emailAddress.=$val;}
	}
	
	$sideBar = "<div id='sidebar'>
		<div class='sitebanner' id='sitebanner' title='jAMES dOE'><img src='./jdlayout/images/jamesdoe.png' title='jAMES dOe' /></div>"
		."<ul class='legalshit' id='legalshit'>"
		."<li>UPDATED: <span class='highlight'>".siteUpdateTimestamp()."</span></li>"
		."<li>All images&copy; 1999 - 2009</li>"
		."<li class='emails' id='sidebaremail' title='Email Address'>".$emailAddress."</a></li>"
		."</ul>"
		."</div>";
	$fullSideBar .= $sideBar;
	$fullSideBar .= "<div class='separator'></div>";

	return $fullSideBar;
}//sidebarSlim()

function helpContentsAdm($pageID)
{
	$helpContents = array();
	
	$helpContents['mmeditimages'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";
	
	$helpContents['mmeditalbums'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";
	
	$helpContents['mmeditposts'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";
	
	$helpContents['mmeditcomments'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";
	
	$helpContents['mmeditsettings'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";

	$helpContents['mmeditinfo'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";

	$helpContents['mmeditwelcomepage'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";
	
	$helpContents['mmualeditcomments'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";
	
	$helpContents['mmeditualroutine'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";
	
	$helpContents['mmeditualerrors'] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam dictum, eros ut ornare aliquet, massa nibh commodo sem, id semper leo diam eget risus. Nullam lobortis. Fusce tincidunt fringilla erat. Duis vel risus et nunc commodo dapibus. Proin vitae ante. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam congue consectetuer nulla. Donec id orci. Maecenas augue sapien, egestas ac, pretium ac, tempus eget, justo. Nulla dolor lectus, viverra ac, posuere adipiscing, condimentum ac, ipsum. Duis nisl. Aliquam nec augue aliquet ligula condimentum pellentesque. Pellentesque risus turpis, egestas quis, fringilla sed, scelerisque id, lacus. Nunc nisi dui, bibendum vel, laoreet volutpat, consequat id, felis. Nunc nec odio. Duis eu risus ac magna ornare viverra.";

	echo '<div>'.$helpContents[$pageID].'</div>';
}//helpContentsAdm($pageID)
?>