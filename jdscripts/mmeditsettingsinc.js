// JavaScript Document

/* GLOBAL JS ARRAYS */
var invisibleAlbumIDArr = new Array;
var visibleAlbumIDArr = new Array;
var tempLiItem;
var profileID;
var togglecommentscheckbox;
var togglehomepagecheckbox;
var togglelinkscheckbox;
var toggleimagesupdatescheckbox;

window.addEvent('domready', 
	function(){
	
	var profileSpanArr = $('profileoptions').getElementsByTagName('span');
	var invisibleAlbumliArr = $('invisiblealbums').getElementsByTagName('li');
	var visibleAlbumliArr = $('visiblealbums').getElementsByTagName('li');
	var counter=0;
	var tempid, invisibleAlbumIDArrPointer, visibleAlbumIDArrPointer;
	
	for (var i=0; i<profileSpanArr.length; i++)
		{tempid = profileSpanArr[i].id.split('_'); if(tempid[0]=='editprofileusername'){profileID = parseInt(tempid[1]);}}
	for (var i=0; i<invisibleAlbumliArr.length; i++)
		{invisibleAlbumIDArr[counter]=invisibleAlbumliArr[i].id; initInvisibleAlbumObj(invisibleAlbumIDArr[counter]); counter++;}
	counter=0;
	for (var i=0; i<visibleAlbumliArr.length; i++)
		{visibleAlbumIDArr[counter] = visibleAlbumliArr[i].id; initVisibleAlbumObj(visibleAlbumIDArr[counter]); counter++;}

	$('editprofileusername_'+profileID).addEvent('click', function(e){e.stop(); editProfileUserName();})
	$('editprofileemail_'+profileID).addEvent('click', function(e){e.stop(); editProfileEmail();})
	$('editprofileblog_'+profileID).addEvent('click', function(e){e.stop(); editProfileBlog();})
	
	$('profileusernamesubmitbutt_'+profileID).addEvent('click', function(e){e.stop(); validateNsubmit('profile_username_'+profileID);});
	$('profileemailsubmitbutt_'+profileID).addEvent('click', function(e){e.stop(); validateNsubmit('profile_email_'+profileID);});
	$('profileblogsubmitbutt_'+profileID).addEvent('click', function(e){e.stop(); validateNsubmit('profile_blog_'+profileID);});
	
	$('profileusernamecancelbutt_'+profileID).addEvent('click', function(e){e.stop(); hideEditProfileUserNameField(profileID,'');});
	$('profileemailcancelbutt_'+profileID).addEvent('click', function(e){e.stop(); hideEditProfileEmailField(profileID,'');});
	$('profileblogcancelbutt_'+profileID).addEvent('click', function(e){e.stop(); hideEditProfileBlogField(profileID,'');});
	
	$('profile_username_'+profileID).addEvent('click', function(e){e.stop(); if($('profile_username_'+profileID).value=='(type your username)'){$('profile_username_'+profileID).value='';}});
	$('profile_email_'+profileID).addEvent('click', function(e){e.stop(); if($('profile_email_'+profileID).value=='(type your email address)'){$('profile_email_'+profileID).value='';}});
	$('profile_blog_'+profileID).addEvent('click', function(e){e.stop(); if($('profile_blog_'+profileID).value=='(type your blog address)'){$('profile_blog_'+profileID).value='http://';}});
	initCheckboxTogglers();
	initTips();
});

function editProfileUserName()
{
	$('editprofileusername_'+profileID).style.display = 'none';
	$('profileusernamefrm_'+profileID).style.display = 'block';
	$('profile_username_'+profileID).value = html_entity_decode($('editprofileusername_'+profileID).innerHTML,'ENT_QUOTES').toLowerCase();
	$('profile_username_'+profileID).select();
}
function editProfileEmail()
{
	$('editprofileemail_'+profileID).style.display = 'none';
	$('profileemailfrm_'+profileID).style.display = 'block';
	$('profile_email_'+profileID).value = (html_entity_decode($('editprofileemail_'+profileID).innerHTML,'ENT_QUOTES')).toLowerCase();
	$('profile_email_'+profileID).select();
}
function editProfileBlog()
{
	$('editprofileblog_'+profileID).style.display = 'none';
	$('profileblogfrm_'+profileID).style.display = 'block';
	$('profile_blog_'+profileID).value = (html_entity_decode($('editprofileblog_'+profileID).innerHTML,'ENT_QUOTES')).toLowerCase();
	$('profile_blog_'+profileID).select();
}

function hideEditProfileUserNameField(profileID,fieldValue)
{
	if(fieldValue==''){ fieldValue = $('editprofileusername_'+profileID).innerHTML; }
	$('profile_username_'+profileID).value = fieldValue;
	$('editprofileusername_'+profileID).innerHTML = fieldValue;
	$('profile_username_'+profileID+'failed').className='hidden';
	$('profileusernamefrm_'+profileID).style.display = 'none';
	$('editprofileusername_'+profileID).style.display = 'block';
}
function hideEditProfileEmailField(profileID,fieldValue)
{
	if(fieldValue==''){ fieldValue = $('editprofileemail_'+profileID).innerHTML; }
	$('profile_email_'+profileID).value=fieldValue;
	$('editprofileemail_'+profileID).innerHTML=fieldValue;
	
	var explodedArr = new Array();
	var emailAddress = "";
	explodedArr = fieldValue.split('@');
	explodedArr[0] = explodedArr[0].split('.');
	explodedArr[1] = explodedArr[1].split('.');
	counter=0;
	for(i=0; i<explodedArr[0].length; i++){
		counter++;
		if(explodedArr[0].length>1)
			{if(counter==explodedArr[0].length){emailAddress+=explodedArr[0][i];} else{emailAddress+=explodedArr[0][i]+"<span class='highlight'> dot </span>";}}
		else{emailAddress+=explodedArr[0][i];}
	}
	emailAddress+="<span class='highlight'> at </span>";
	counter=0;
	for(j=0; j<explodedArr[1].length; j++){
		counter++;
		if(explodedArr[1].length>1)
			{if(counter==explodedArr[1].length){emailAddress+=explodedArr[1][j];} else{emailAddress+=explodedArr[1][j]+"<span class='highlight'><a href='./mmlogin.php' class='login'> dot </a></span>";}}
		else{emailAddress+=explodedArr[1][j];}
	}
	$('sidebaremail').innerHTML = emailAddress;

	$('profile_email_'+profileID+'failed').className='hidden';
	$('profileemailfrm_'+profileID).style.display = 'none';
	$('editprofileemail_'+profileID).style.display = 'block';
}
function hideEditProfileBlogField(profileID,fieldValue)
{
	if(fieldValue.toLowerCase()=='null'){fieldValue='(type your blog address)'; if($('naviblog')!=null){$('naviblog').dispose();}}
	if(fieldValue==''){ fieldValue = $('editprofileblog_'+profileID).innerHTML; }
	$('profile_blog_'+profileID).value=fieldValue;
	$('editprofileblog_'+profileID).innerHTML = fieldValue;
	if(fieldValue!='(type your blog address)'){
		if($('naviblog')==null){
			var naviBlogLi = new Element('li',{'html': "<a id='naviblog' href='"+fieldValue+"' title='Blog'>blog</a>"}); $('mainnavi').appendChild(naviBlogLi);
		}else{$('naviblog').href = fieldValue;}
	}
	$('profile_blog_'+profileID+'failed').className='hidden';
	$('profileblogfrm_'+profileID).style.display = 'none';
	$('editprofileblog_'+profileID).style.display = 'block';
}


function initInvisibleAlbumObj(albumID)
	{$(albumID).addEvent('click', function(e){e.stop(); $('album_visibility').innerHTML=albumID+"::"+"makevisible"; validateNsubmit('album_visibility');})}
function initVisibleAlbumObj(albumID)
	{$(albumID).addEvent('click', function(e){e.stop(); $('album_visibility').innerHTML=albumID+"::"+"makeinvisible"; validateNsubmit('album_visibility');})}
function toggleAlbumVisibility(albumID,visibilityStatus)
{
	tempLiItem = new Element('li',{ 'id': albumID, 'html': $(albumID).innerHTML});
	$(albumID).dispose();
	if(visibilityStatus=='makevisible')
		{$('visiblealbums').appendChild(tempLiItem); initVisibleAlbumObj(albumID);}
	else if(visibilityStatus=='makeinvisible')
		{$('invisiblealbums').appendChild(tempLiItem); initInvisibleAlbumObj(albumID);}
}

function initCheckboxTogglers()
{
	togglecommentscheckbox = $('togglecommentscheckbox').title;
	togglehomepagecheckbox = $('togglehomepagecheckbox').title;
	togglelinkscheckbox = $('togglelinkscheckbox').title;
	toggleimagesupdatescheckbox = $('toggleimagesupdatescheckbox').title;
	
	$('togglecomments').addEvent('click', function(e){e.stop();
	if(togglecommentscheckbox == 'checked'){$('togglecommentscheckbox').title = 'unchecked'; validateNsubmit('comments_checkbox_'+profileID);}
	else if(togglecommentscheckbox == 'unchecked'){$('togglecommentscheckbox').title = 'checked'; validateNsubmit('comments_checkbox_'+profileID);}})
	
	$('togglehomepage').addEvent('click', function(e){e.stop();
	if(togglehomepagecheckbox == 'checked'){$('togglehomepagecheckbox').title = 'unchecked'; validateNsubmit('homepage_checkbox_'+profileID);}
	else if(togglehomepagecheckbox == 'unchecked'){$('togglehomepagecheckbox').title = 'checked'; validateNsubmit('homepage_checkbox_'+profileID);}})
	
	$('toggleimagesupdates').addEvent('click', function(e){e.stop();
	if(toggleimagesupdatescheckbox == 'checked'){$('toggleimagesupdatescheckbox').title = 'unchecked'; validateNsubmit('imagesupdates_checkbox_'+profileID);}
	else if(toggleimagesupdatescheckbox == 'unchecked'){$('toggleimagesupdatescheckbox').title = 'checked'; validateNsubmit('imagesupdates_checkbox_'+profileID);}})
	
	$('togglelinks').addEvent('click', function(e){e.stop();
	if(togglelinkscheckbox == 'checked'){$('togglelinkscheckbox').title = 'unchecked'; validateNsubmit('links_checkbox_'+profileID);}
	else if(togglelinkscheckbox == 'unchecked'){$('togglelinkscheckbox').title = 'checked'; validateNsubmit('links_checkbox_'+profileID);}})
}//

function toggleSettingsCheckbox(fieldID,fieldValue)
{
	switch(fieldID)
	{
		case 'togglecommentscheckbox':
			if(fieldValue=='checked'){$('togglecommentscheckbox').src='./jdlayout/images/checked.gif'; togglecommentscheckbox='checked';}
			else if(fieldValue=='unchecked'){$('togglecommentscheckbox').src='./jdlayout/images/unchecked.gif'; togglecommentscheckbox='unchecked';}
			break;
		case 'togglehomepagecheckbox':
			if(fieldValue=='checked'){$('togglehomepagecheckbox').src='./jdlayout/images/checked.gif'; togglehomepagecheckbox='checked';}
			else if(fieldValue=='unchecked'){$('togglehomepagecheckbox').src='./jdlayout/images/unchecked.gif'; togglehomepagecheckbox='unchecked';}
			break;
		case 'toggleimagesupdatescheckbox':
			if(fieldValue=='checked'){$('toggleimagesupdatescheckbox').src='./jdlayout/images/checked.gif'; toggleimagesupdatescheckbox='checked';}
			else if(fieldValue=='unchecked'){$('toggleimagesupdatescheckbox').src='./jdlayout/images/unchecked.gif'; toggleimagesupdatescheckbox='unchecked';}
			break;
		case 'togglelinkscheckbox':
			if(fieldValue=='checked'){$('togglelinkscheckbox').src='./jdlayout/images/checked.gif'; togglelinkscheckbox='checked';}
			else if(fieldValue=='unchecked'){$('togglelinkscheckbox').src='./jdlayout/images/unchecked.gif'; togglelinkscheckbox='unchecked';}	
			break;
		default: break;
	}
}

