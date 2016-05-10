// JavaScript Document

/* GLOBAL JS ARRAYS */
var postIDArr = new Array;
var postSlideArr = new Array;
var postSectionElementsArr = new Array;
var imagesToUploadNum = new Array; //0;
var maxImagesToUploadNum = 5;
var imageToUploadClass;

var imageIDArr = new Array;
var imageMorphArr = new Array;
var openMorphImageID;
var imageMorphElementsArr = new Array;

var postBodyMaxLength = 3500;

window.addEvent('domready', function(){
	var spanArr = $('posts').getElementsByTagName('span');
	var newPostID = new Array;
	var counter=0;
	var tempid, postIDArrPointer;
	var csrfPasswordGenerator = $('csrf').innerHTML;
	var currentConvertedTimeStamp = getCurrentConvertedTimeStamp('short');
	var newsPostsCounter = 0;
	
	for (var i=0; i<spanArr.length; i++){tempid=spanArr[i].id.split('_'); if(tempid[0]=='postsp'){postIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for (var i=0; i<postIDArr.length; i++){postIDArrPointer = i; initPostSliders(postIDArr[postIDArrPointer]);}//for

	$('addpost').addEvent('click', function(e){
		e.stop();
		for (var i=0; i<postIDArr.length; i++){ if(postIDArr[i]==null){continue;} if($('posttype_'+postIDArr[i]).innerHTML=='newspost'){newsPostsCounter++;}}
		addPostNewsPostsCounter = newsPostsCounter+1;
		//postIDArr[postIDArr.length]=parseInt('55x'+postIDArr.length);
		postIDArr[postIDArr.length]='55x'+postIDArr.length;
		postIDArrPointer = (postIDArr.length-1);
		
		var listitem = new Element('li', {
			'class': 'postelements',
			'id': 'postelement_'+postIDArr[postIDArrPointer]+'',
			'html': '\n\n<ul class="postelementsbanners"><li class="postheadlines"><span class="postsps" id="postsp_'+postIDArr[postIDArrPointer]+'" title="Post Headline">(type post headline)</span><span class="postfrms" id="postheadlinefrm_'+postIDArr[postIDArrPointer]+'"><input class="text" type="text" id="post_headline_'+postIDArr[postIDArrPointer]+'" value="" maxlength="38" title="Post Headlines." /><input class="button" type="button" name="postheadlinesubmitbutt_'+postIDArr[postIDArrPointer]+'" id="postheadlinesubmitbutt_'+postIDArr[postIDArrPointer]+'" value="save" /><input class="button" type="button" name="postheadlinescancelbutt_'+postIDArr[postIDArrPointer]+'" id="postheadlinescancelbutt_'+postIDArr[postIDArrPointer]+'" value="cancel" /><div id="post_headline_'+postIDArr[postIDArrPointer]+'failed" class="hidden"></div></span><span class="hidden" id="postheadlineloader_'+postIDArr[postIDArrPointer]+'"></span><span class="hidden" id="npost_'+postIDArr[postIDArrPointer]+'">'+postIDArr[postIDArrPointer]+'</span></li><li class="postinfos"><span id="postcreationtimestamp_'+postIDArr[postIDArrPointer]+'">'+currentConvertedTimeStamp+'</span><span class="togglers" id="togglepost_'+postIDArr[postIDArrPointer]+'">show</span></li></ul><div class="separator"></div><div class="postsections" id="postsection_'+postIDArr[postIDArrPointer]+'"><div class="postnotes"><span class="postcounts">'+'Post #'+addPostNewsPostsCounter+'</div><div class="clearboth"></div><div class="postcontainers" id="postcontainer_'+postIDArr[postIDArrPointer]+'"><div class="editpostbodycontainers"><span class="editpostbodies" id="editpostbody_'+postIDArr[postIDArrPointer]+'" title="Post Body.">(type your text here)</span><span class="postfrms" id="postbodyfrm_'+postIDArr[postIDArrPointer]+'"><textarea class="text" type="text" name="post_body_'+postIDArr[postIDArrPointer]+'" id="post_body_'+postIDArr[postIDArrPointer]+'" cols="41" rows="5" wrap="hard" title="Post Body."/></textarea><span class="editbodybuttons"><input class="button" type="button" id="postbodysubmitbutt_'+postIDArr[postIDArrPointer]+'" value="save" /><input class="button" type="button" id="postbodycancelbutt_'+postIDArr[postIDArrPointer]+'" value="cancel" /><span><span class="counters" id="pcounter_'+postIDArr[postIDArrPointer]+'">'+postBodyMaxLength+'</span> remaining characters</span></span><span id="post_body_'+postIDArr[postIDArrPointer]+'failed" class="hidden"></span></span><span id="postbodyloader_'+postIDArr[postIDArrPointer]+'" class="hidden"></span></div><div class="clearboth"></div><ul class="uploadimagesections" id="uploadimagesections_'+postIDArr[postIDArrPointer]+'"><li class="uploadimages" id="uploadimage_0_'+postIDArr[postIDArrPointer]+'"><form id="uploadimagesfrm_'+postIDArr[postIDArrPointer]+'" name="uploadimagesfrm_'+postIDArr[postIDArrPointer]+'" method="post" action="./jdincfunctions/functionsinc.php?type=8" enctype="multipart/form-data">Select images to upload: <span id="inputplaceholder_'+postIDArr[postIDArrPointer]+'"><input id="imagefile_'+postIDArr[postIDArrPointer]+'_0" name="imagefile_'+postIDArr[postIDArrPointer]+'_0" class="text" type="file" size="41" title="Browse image." /></span><input type="hidden" name="postid" class="hidden" id="postid_'+postIDArr[postIDArrPointer]+'" value="'+postIDArr[postIDArrPointer]+'" /><input type="hidden" name="csrf" class="hidden" value="'+csrfPasswordGenerator+'uploadimage'+'" /><input type="hidden" name="pageid" class="hidden" value="mmeditposts" /><span id="submituploadfrm_'+postIDArr[postIDArrPointer]+'"></span></form><ul class="imagestoupload" id="imagestoupload_'+postIDArr[postIDArrPointer]+'"></ul></li><li class="supportedimagefiletypes">Upload up to 5 images.<br />Supported image filetypes: jpg, gif, png</li></ul><span class="hidden" id="posttype_'+postIDArr[postIDArrPointer]+'">newspost</span><div class="postimagesbanners" id="postimagesbanners_'+postIDArr[postIDArrPointer]+'"><span class="banners">Post images:</span></div><div id="postimagecontents_'+postIDArr[postIDArrPointer]+'" class="postimagecontents"></div><div class="clearboth"></div><ul class="postdeletes" id="postdelete_'+postIDArr[postIDArrPointer]+'"><li class="deletepostmsg">delete this post and all its contents?</li><li class="completedeleteposts"><span id="completedeletepost_'+postIDArr[postIDArrPointer]+'">yes</span></li><li class="dontdeleteposts"><span id="dontdeletepost_'+postIDArr[postIDArrPointer]+'">no</span></li></ul><ul class="posterrmsguls" id="posterrmsgul_'+postIDArr[postIDArrPointer]+'"><li class="posterrmsgs" id="posterrmsg_'+postIDArr[postIDArrPointer]+'">Please enter a headline for this post first.</li><li class="posterrmsgokbutts"><span id="posterrmsgokbutt_'+postIDArr[postIDArrPointer]+'">ok</span></li></ul><ul class="postoptions" id="postoption_'+postIDArr[postIDArrPointer]+'"><li class="postcommentbutts" id="postcommentbutt_'+postIDArr[postIDArrPointer]+'">0 comments</li><li class="postdeletebutts" id="postdeletebutt_'+postIDArr[postIDArrPointer]+'">delete</li></ul><div class="clearboth"></div><div class="postslineseperator"></div></div></div>\n\n'
		});
			
		var postanchoritem = new Element('span',{ 'id': 'newpostanchor', 'html': ''});
		//Add the new category
		$('postanchor').appendChild(postanchoritem);
		$('postanchor').appendChild(listitem);
		$('postanchor').id = '';
		$('newpostanchor').id = 'postanchor';
			
		initPostSliders(postIDArr[postIDArrPointer]);
		//slide out all the open categories
		for(var j=0; j<postIDArr.length; j++){ if(postIDArr[j]==null){continue;} $('togglepost_'+postIDArr[j]).innerHTML='show'; postSlideArr[postIDArr[j]].slideOut();}
		//slide in the new category
		$('togglepost_'+postIDArr[postIDArrPointer]).innerHTML='show';
		postSlideArr[postIDArr[postIDArrPointer]].toggle();
	});
});

/* 
POSTS
*/
function editPostHeadline(postID)
{
	var postHeadlineOld = html_entity_decode($('postsp_'+postID).innerHTML,'ENT_QUOTES');
	$('postsp_'+postID).style.display='none';
	$('post_headline_'+postID).value = postHeadlineOld.toLowerCase();
	$('postheadlinefrm_'+postID).style.display='block';
	$('post_headline_'+postID).select();
	return false;
}
function hideEditPostHeadlineField(postID,fieldValue)
{
	if(fieldValue!='')
	{
		$('postsp_'+postID).innerHTML = fieldValue.toUpperCase();
		fadePostsElements(postID,1);
		$('posterrmsgul_'+postID).style.display='none';
	}
	$('postheadlinefrm_'+postID).style.display='none';
	$('post_headline_'+postID+'failed').className='hidden';
	$('postsp_'+postID).style.display='block';
}
function editPostBody(postID)
{
	var postBodyOld = html_entity_decode($('editpostbody_'+postID).innerHTML,'ENT_QUOTES');	
	$('editpostbody_'+postID).style.display='none';
	$('post_body_'+postID).value = postBodyOld;
	$('postbodyfrm_'+postID).style.display='block';
	$('post_body_'+postID).select();
	return false;
}//editPostBody(postID)
function hideEditPostBodyField(postID,fieldValue)
{
	if(fieldValue!=''){$('editpostbody_'+postID).innerHTML = fieldValue;}
	$('postbodyfrm_'+postID).style.display='none';
	$('post_body_'+postID+'failed').className='hidden';
	$('editpostbody_'+postID).style.display='block';
}//hideEditPostHeadlineField(postID,fieldValue)
function postSectionToggler(postID, e)
{	
	e.stop();
	for(var j=0; j<postIDArr.length; j++){ if(postIDArr[j]==null){continue;} if(postIDArr[j]!=postID){ $('togglepost_'+postIDArr[j]).innerHTML='show'; postSlideArr[postIDArr[j]].slideOut();} }
	//e = new Event(e); 
	postSlideArr[postID].toggle();
	if($('togglepost_'+postID).innerHTML=='show'){$('togglepost_'+postID).innerHTML='hide';}
	else{$('togglepost_'+postID).innerHTML='show';}
}
function addImageLi(postID)
{
	var ulImagesToUploadArr = $('imagestoupload_'+postID).getElementsByTagName('li');
	var alreadyUploadedPostsImagesArr = $('postimagecontents_'+postID).getElementsByTagName('img');
	if((alreadyUploadedPostsImagesArr.length+ulImagesToUploadArr.length)>=maxImagesToUploadNum)
	{
		$('imagefile_'+postID+'_'+imagesToUploadNum[postID]+'').value='';
		alertmessages("Warning","You are not allowed to upload more than 5 images per post."); return 0;
	}
	if(ulImagesToUploadArr.length>=maxImagesToUploadNum){}
	else
	{
		//if there are no images then this is the first time this function is called
		if(ulImagesToUploadArr.length==0){imageToUploadClass='imagestouploadnoborder'; } 
		else{imageToUploadClass='imagestouploadbordertop';}
		var liImageItem = new Element('li', {'class': ''+imageToUploadClass+'','id': 'imagetoupload_'+postID+'_'+imagesToUploadNum[postID]+''});
		var deleteImageItem = new Element('img',{'class': 'deleteimagestoupload ', 'id':'deleteimagetoupload_'+postID+'_'+imagesToUploadNum[postID]+'','src':'./jdlayout/images/close.png','alt':'Delete','title':'Delete'});
		if($('imagefile_'+postID+'_'+imagesToUploadNum[postID]+'').value==''){return 1;}
		var spanImageItem = new Element('span', {'html': ''+$('imagefile_'+postID+'_'+imagesToUploadNum[postID]+'').value+''});
		$('imagestoupload_'+postID).appendChild(liImageItem);
		liImageItem.appendChild(deleteImageItem);
		liImageItem.appendChild(spanImageItem);
		$('imagefile_'+postID+'_'+imagesToUploadNum[postID]+'').style.display='none';
		$('imagestoupload_'+postID).style.display='block';
		$('deleteimagetoupload_'+postID+'_'+imagesToUploadNum[postID]).addEvent('click', function(e){e.stop(); deleteImageLi(this);});
		imagesToUploadNum[postID]++;
		var uploadImageInput = new Element('input',{'class': 'text', 'type': 'file', 'size': '41', 'title':'Browse image.', 'id': 'imagefile_'+postID+'_'+imagesToUploadNum[postID]+'', 'name': 'imagefile_'+postID+'_'+imagesToUploadNum[postID]+'' });
		$('inputplaceholder_'+postID).appendChild(uploadImageInput);
		$('imagefile_'+postID+'_'+imagesToUploadNum[postID]+'').addEvent('change', function(e){e.stop();addImageLi(postID);});
	}//else
}//addImageLi()
function deleteImageLi(obj)
{
	var tempimageid = obj.id.split('_');
	postID = tempimageid[1];
	$('imagetoupload_'+postID+'_'+tempimageid[2]).dispose();
	var ulImagesToUploadArr = $('imagestoupload_'+postID).getElementsByTagName('li');
	if(ulImagesToUploadArr.length==0){$('imagestoupload_'+postID).style.display='none';}
	$('imagefile_'+postID+'_'+tempimageid[2]).dispose();
}//deleteImageLi(obj)
function deletePost(postID)
{
	$('postdelete_'+postID).style.display = 'block';
	$('postoption_'+postID).style.display = 'none';
	fadePostsElements(postID,0.3);
}//deletePost(postID)
function dontDeletePost(postID)
{
	$('postoption_'+postID).style.display = 'block';
	$('postdelete_'+postID).style.display = 'none';
	fadePostsElements(postID,1);
}//dontDeleteCategory(postID)
function completeDeletePost(postID)
{
	var dbPostID = postID;
	var domPostID = null;
	var spanArr = $('posts').getElementsByTagName('span');
	for (var i=0; i<spanArr.length; i++)
		{tempid = spanArr[i].id.split('_'); if(tempid[0]=='npost'){if($('npost_'+tempid[1]).innerHTML == dbPostID){domPostID = tempid[1];}}}		
	//unset the images that exist in this post

	$('postelement_'+domPostID).fade(0);
	$('postelement_'+domPostID).style.display='none';
	$('postelement_'+domPostID).dispose();
	for(var i=0; i<postIDArr.length; i++){ if(postIDArr[i]==domPostID){postSlideArr[postIDArr[i]]=null; postIDArr[i]=null;}}

}//completeDeletePost(postID)
function postMessageOKButton(postID)
{
	fadePostsElements(postID,1);
	$('posterrmsgul_'+postID).style.display='none';
}//postMessageOKButton(postID)
function fadePostsElements(postID,value)
{
	$('editpostbody_'+postID).fade(value);
	$('postbodyfrm_'+postID).fade(value);
	if($('posttype_'+postID).innerHTML!='imagesupdate'){$('uploadimagesections_'+postID).fade(value);}
	$('postimagesbanners_'+postID).fade(value);
	$('postimagecontents_'+postID).fade(value);	
}//fadePostsElements(postID,value)
function redirectPost(postID)
{
	window.location = './jdincfunctions/functionsinc.php?type=29&postid='+postID+'';
}//redirectPost(postID)
function initPostSliders(postID)
{
	postSlideArr[postID] = new Fx.Slide('postsection_'+postID);
	postSlideArr[postID].hide();
	postSectionElementsArr[postID] = 'disabled';
	$('togglepost_'+postID).addEvent('click',function(e){
		if(postSectionElementsArr[postID]=='disabled')
			{initPostSectionElements(postID); if($('posttype_'+postID).innerHTML!='imagesupdate'){initImageElements(postID);}}
		postSectionToggler(postID, e);
	});
	$('postsp_'+postID).addEvent('click', function(e){e.stop(); editPostHeadline(postID); });
	$('post_headline_'+postID).addEvent('click',function(e){
		e.stop(); if( $('post_headline_'+postID).value=='(type post headline)'){$('post_headline_'+postID).value='';}});
	$('postheadlinesubmitbutt_'+postID).addEvent('click', function(e){e.stop(); validateNsubmit('post_headline_'+postID);});
	$('postheadlinescancelbutt_'+postID).addEvent('click', function(e){e.stop(); hideEditPostHeadlineField(postID,'');});
}//initPostSliders(postID)
function initPostSectionElements(postID)
{
	postSectionElementsArr[postID] = 'enabled';
	$('editpostbody_'+postID).addEvent('click',function(e)
	{
		e.stop(); 
		if($('npost_'+postID).innerHTML.substr(0,3)=='55x'){fadePostsElements(postID,0.3); $('posterrmsgul_'+postID).style.display='block';}
		else{editPostBody(postID);}
	});
	$('postbodysubmitbutt_'+postID).addEvent('click',function(e){e.stop(); validateNsubmit('post_body_'+postID);});
	$('postbodycancelbutt_'+postID).addEvent('click',function(e){e.stop(); hideEditPostBodyField(postID,'');});
	$('posterrmsgokbutt_'+postID).addEvent('click', function(e){e.stop(); postMessageOKButton(postID);});
	$('post_headline_'+postID).addEvent('click',function(e){e.stop(); if( $('post_headline_'+postID).value=='(type post headline)'){$('post_headline_'+postID).value='';}});
	$('post_body_'+postID).addEvent('click',function(e){e.stop(); if( $('post_body_'+postID).value=='(type your text here)'){$('post_body_'+postID).value='';}});
	$('post_body_'+postID).addEvent('keyup',function(e){e.stop(); countChars('post_body_'+postID,'pcounter_'+postID,postBodyMaxLength);});
	
	if($('posttype_'+postID).innerHTML=='newspost')
	{
		imagesToUploadNum[postID] = 0;
		$('imagefile_'+postID+'_0').addEvent('change', function(e){e.stop(); addImageLi(postID);});
		
		if($('npost_'+postID).innerHTML.substr(0,3)!='55x')
		{	$('submituploadfrmbutt_'+postID).addEvent('click',function(e)
			{e.stop(); $('uploadimagesfrm_'+postID).submit(); 
			$('submituploadfrm_'+postID).innerHTML = 'Uploading... <img class="loaderimg" src="./jdlayout/images/loader.gif" />'; 
			$('submituploadfrmbutt_'+postID).style.display='none';});
		}
	}//if
	$('postdeletebutt_'+postID).addEvent('click',function(e){e.stop(); deletePost(postID);});
	$('postcommentbutt_'+postID).addEvent('click',function(e){e.stop(); redirectPost(postID);});
	$('completedeletepost_'+postID).addEvent('click',function(e){e.stop(); var nPostID = $('npost_'+postID).innerHTML; deletePosts(nPostID);});
	$('dontdeletepost_'+postID).addEvent('click',function(e){e.stop(); dontDeletePost(postID);});
	if($('posttype_'+postID).innerHTML=='imagesupdate'){$('postimagecontents_'+postID).fade(1);}
}//initPostSectionElements(postID)

/*
Images
*/
function initImageElements(postID)
{
	var counter, tempid, imageIDArrPointer;
	var counter=0;
	var ulArr = $('postimagecontents_'+postID).getElementsByTagName('ul');
	if(ulArr.length!=0)
	{
		for (var i=0; i<ulArr.length; i++){ tempid = ulArr[i].id.split('_'); if(tempid[0]=='imagesection'){imageIDArr[counter] = parseInt(tempid[1]); counter++;}}
		for (var i=0; i<imageIDArr.length; i++){imageIDArrPointer = i; initImageMorphs(imageIDArr[imageIDArrPointer]);}//for
	}
}//initImageElements()

function initImageMorphs(imageID)
{
	for(i=0; i<imageIDArr.length; i++){if(imageIDArr[i]==null){return 0;}}
	imageMorphArr[imageID] = new Fx.Morph('imagesection_'+imageID);
	imageMorphElementsArr[imageID] = 'disabled';
	$('imagesection_'+imageID).addEvent('click',function(e){e.stop(); 
	if(imageMorphElementsArr[imageID]=='disabled'){initImageSectionElement(imageID);}
		imageSectionToggler(imageID);
	});
}//initImageMorphs(imageID)
function closeAllImageSections(imageID)
{
	for(i=0; i<imageIDArr.length; i++)
	{
		if(imageID!=''){if((imageIDArr[i]==imageID)||(imageIDArr[i]==null)){continue;}}
		if(imageID==''){if(imageIDArr[i]==null){continue;}}
		$('imagedelete_'+imageIDArr[i]).style.display = 'none';
		imageMorphArr[imageIDArr[i]].start({display: 'block',width: '100px',height: '100px',padding: '0px',cursor: 'pointer'});
		$('imagethumb_'+imageIDArr[i]).fade(1);
	}//for
}//closeAllImageSections(imageID)
function closeImageSection(imageID)
{
	$('imagedelete_'+imageID).style.display='none';
	imageMorphArr[imageID].start({display: 'block',width: '100px',height: '100px',padding: '0px',cursor: 'pointer'});
	$('imagethumb_'+imageID).fade(1);
	openMorphImageID=null;
}//closeImageSection(imageID)
function completeDeleteImage(imageID)
{
	$('imagesection_'+imageID).fade(0);
	for(var i=0; i<imageIDArr.length; i++){if(imageIDArr[i]==imageID){imageIDArr[i]=null;}}
	imageMorphArr[imageID]=null;
	$('imagesection_'+imageID).dispose();
}//completeDeleteImage(imageID)
function imageSectionToggler(imageID)
{
	if(openMorphImageID!=imageID)
	{
		closeAllImageSections(imageID);
		imageMorphArr[imageID].start({display: 'block',width: '210px',height: '100px',cursor: 'default'});
		$('imagethumb_'+imageID).fade(0.3);
		openMorphImageID = imageID;
		$('imagedelete_'+imageID).style.display = 'block';
	}//if
}//imageSectionToggler(imageID)
function initImageSectionElement(imageID)
{
	imageMorphElementsArr[imageID]='enabled';
	$('completedeleteimage_'+imageID).addEvent('click',function(e){e.stop(); deleteImages(imageID);});
	$('dontdeleteimage_'+imageID).addEvent('click',function(e){e.stop(); closeImageSection(imageID);});
}//initImageSectionElement()