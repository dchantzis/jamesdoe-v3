// JavaScript Document

/* GLOBAL JS ARRAYS */
var profileID;
var imageIDArr = new Array;
var imageMorphArr = new Array;
var openMorphImageID;
var imageMorphElementsArr = new Array;
var imagesToUploadNum=0;
var maxImagesToUploadNum=5;
var maxWelcomePageImages=12;
var welcomePageTextMaxLength = 2000;

window.addEvent('domready', 
	function(){
		profileID = parseInt($('profile').innerHTML);
		var ulArr = $('welcomepageimagescontents').getElementsByTagName('ul');
		var counter=0;
		var tempid, imageIDArrPointer;
		
		for (var i=0; i<ulArr.length; i++){ tempid = ulArr[i].id.split('_'); if(tempid[0]=='imagesection'){ imageIDArr[counter] = parseInt(tempid[1]); counter++;}}	
		for (var i=0; i<imageIDArr.length; i++){ imageIDArrPointer = i; initImageMorphs(imageIDArr[imageIDArrPointer]);}//for

		$('editwelcomepagetext_'+profileID).addEvent('click', function(e){e.stop(); editWelcomePageText(profileID);});
		$('profilewelcomepagetextsubmitbutt_'+profileID).addEvent('click', function(e){e.stop(); validateNsubmit('profile_welcomepagetext_'+profileID);});
		$('profilewelcomepagetextcancelbutt_'+profileID).addEvent('click', function(e){e.stop(); hideEditWelcomePageTextField(profileID,'null');});
		$('profile_welcomepagetext_'+profileID).addEvent('click', function(e){e.stop(); if($('profile_welcomepagetext_'+profileID).value=='(type some welcoming text)'){$('profile_welcomepagetext_'+profileID).value='';}});
		$('profile_welcomepagetext_'+profileID).addEvent('keyup',function(e){e.stop(); countChars('profile_welcomepagetext_'+profileID,'pwptcounter_'+profileID,welcomePageTextMaxLength);})
		if($('imagefile_0')!=null){$('imagefile_0').addEvent('change', function(e){e.stop(); addImageLi();});
		$('submituploadfrmbutt').addEvent('click',function(e){ e.stop(); $('uploadimagesfrm').submit(); $('submituploadfrm').innerHTML = 'Uploading... <img class="loaderimg" src="./jdlayout/images/loader.gif" />'; $('submituploadfrmbutt').style.display='none';});}

});

function editWelcomePageText(profileID)
{
	$('editwelcomepagetext_'+profileID).style.display = 'none';
	$('profilewelcomepagetextfrm_'+profileID).style.display = 'inline';
	$('profile_welcomepagetext_'+profileID).value = (html_entity_decode($('editwelcomepagetext_'+profileID).innerHTML,'ENT_QUOTES'));
	$('profile_welcomepagetext_'+profileID).select();
}//editWelcomePageText(profileID)
function hideEditWelcomePageTextField(profileID,fieldValue)
{
	if(fieldValue==''){fieldValue = '(type some welcoming text)';}
	if(fieldValue=='null'){ fieldValue = $('editwelcomepagetext_'+profileID).innerHTML; }
	$('editwelcomepagetext_'+profileID).innerHTML = fieldValue;
	$('profile_welcomepagetext_'+profileID+'failed').className='hidden';	
	$('profilewelcomepagetextfrm_'+profileID).style.display = 'none';
	$('editwelcomepagetext_'+profileID).style.display = 'block';
}//hideEditWelcomePageTextField(profileID,fieldValue)
function addImageLi()
{
	var ulImagesToUploadArr = $('imagestoupload').getElementsByTagName('li');
	var alreadyUploadedImagesArr = $('welcomepageimagescontents').getElementsByTagName('img');
	if((alreadyUploadedImagesArr.length+ulImagesToUploadArr.length)>=maxWelcomePageImages)
	{
		$('imagefile_'+imagesToUploadNum+'').value='';
		alertmessages("Warning","You are not allowed to upload more than 12 welcome page images.");
		return 0;
	}
	if(ulImagesToUploadArr.length>=maxImagesToUploadNum)
	{
		$('imagefile_'+imagesToUploadNum+'').value='';
		alertmessages("Warning","You can upload up to 5 images at a time.");
		return 0;
	}
	else
	{
		//if there are no images then this is the first time this function is called
		if(ulImagesToUploadArr.length==0){imageToUploadClass='imagestouploadnoborder'; } 
		else{imageToUploadClass='imagestouploadbordertop';}
		var liImageItem = new Element('li', {'class': ''+imageToUploadClass+'','id': 'imagetoupload_'+imagesToUploadNum+''});
		var deleteImageItem = new Element('img',{'class': 'deleteimagestoupload ', 'id':'deleteimagetoupload_'+imagesToUploadNum+'','src':'./jdlayout/images/close.png','alt':'Delete','title':'Delete'});
		if($('imagefile_'+imagesToUploadNum+'').value==''){return 1;}
		var spanImageItem = new Element('span', {'html': ''+$('imagefile_'+imagesToUploadNum+'').value+''});
		$('imagestoupload').appendChild(liImageItem);
		liImageItem.appendChild(deleteImageItem);
		liImageItem.appendChild(spanImageItem);
		$('imagefile_'+imagesToUploadNum+'').style.display='none';
		$('imagestoupload').style.display='block';
		$('deleteimagetoupload_'+imagesToUploadNum).addEvent('click', function(e){e.stop(); deleteImageLi(this);});
		imagesToUploadNum++;
		var uploadImageInput = new Element('input',{'class': 'text', 'type': 'file', 'size': '41', 'title':'Browse image.', 'id': 'imagefile_'+imagesToUploadNum+'', 'name': 'imagefile_'+imagesToUploadNum+'' });
		$('inputplaceholder').appendChild(uploadImageInput);
		$('imagefile_'+imagesToUploadNum+'').addEvent('change', function(e){e.stop();addImageLi();});
	}//else
}//addImageLi()
function deleteImageLi(obj)
{
	var tempimageid = obj.id.split('_'); 
	$('imagetoupload_'+tempimageid[1]).dispose();
	var ulImagesToUploadArr = $('imagestoupload').getElementsByTagName('li');
	if(ulImagesToUploadArr.length==0){$('imagestoupload').style.display='none';}
	$('imagefile_'+tempimageid[1]).dispose();
}//deleteImageLi(obj)
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