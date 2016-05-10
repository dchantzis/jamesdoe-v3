// JavaScript Document

/* GLOBAL JS ARRAYS */
var imageIDArr = new Array;
var imageMorphArr = new Array;
var albumID;
var openMorphImageID;
var imagesToUploadNum=0;
var maxImagesToUploadNum=5;
var imageToUploadClass;
var organizealbumimagescheckbox = 'unchecked';
var sortableObj=null;
var albumElements = new Array;
var imageMorphElementsArr = new Array;
var imageDescriptionMaxLength = 1500;
var albumDescriptionMaxLength = 1500;

window.addEvent('domready', 
	function(){
	var ulArr = $('album').getElementsByTagName('ul');
	var counter=0;
	var tempid, imageIDArrPointer;
	
	for (var i=0; i<ulArr.length; i++){ tempid = ulArr[i].id.split('_'); if(tempid[0]=='imagesection'){ imageIDArr[counter] = parseInt(tempid[1]); counter++;} if(tempid[0]=='albumbanner'){albumID = parseInt(tempid[1]);}}	
	for (var i=0; i<imageIDArr.length; i++){ imageIDArrPointer = i; initImageMorphs(imageIDArr[imageIDArrPointer]);}//for

	$('editalbumname_'+albumID).addEvent('click', function(e){e.stop(); editAlbumName(albumID);});
	$('editalbumdescription_'+albumID).addEvent('click', function(e){e.stop(); editAlbumDescription(albumID);});
	$('albumnamesubmitbutt_'+albumID).addEvent('click', function(e){e.stop(); validateNsubmit('album_name_'+albumID);});
	$('albumdescriptionsubmitbutt_'+albumID).addEvent('click', function(e){e.stop(); validateNsubmit('album_description_'+albumID);});
	$('albumnamecancelbutt_'+albumID).addEvent('click', function(e){e.stop(); hideEditAlbumNameField(albumID,'');});
	$('albumdescriptioncancelbutt_'+albumID).addEvent('click', function(e){e.stop(); hideEditAlbumDescriptionField(albumID,'null');});
	$('album_description_'+albumID).addEvent('click', function(e){e.stop(); if($('album_description_'+albumID).value=='(type a description)'){$('album_description_'+albumID).value='';}});
	$('album_description_'+albumID).addEvent('keyup',function(e){e.stop(); countChars('album_description_'+albumID,'acounter_'+albumID,albumDescriptionMaxLength);});
	if($('imagefile_0')!=null)
		{$('imagefile_0').addEvent('change', function(e){e.stop(); addImageLi();});
		$('submituploadfrmbutt').addEvent('click',function(e){ e.stop(); $('uploadimagesfrm').submit(); $('submituploadfrm').innerHTML = 'Uploading... <img class="loaderimg" src="./jdlayout/images/loader.gif" />'; $('submituploadfrmbutt').style.display='none';});}

	$('organizealbumimages').addEvent('click', function(e){e.stop(); organiazeAlbumImages();});
	initTips();
});

function addImageLi()
{
	var ulImagesToUploadArr = $('imagestoupload').getElementsByTagName('li');
	if(ulImagesToUploadArr.length>=maxImagesToUploadNum){
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
function editAlbumName(albumID)
{
	$('editalbumname_'+albumID).style.display = 'none';
	$('albumnamefrm_'+albumID).style.display = 'inline';
	$('album_name_'+albumID).value = (html_entity_decode($('editalbumname_'+albumID).innerHTML,'ENT_QUOTES')).toLowerCase();
	$('album_name_'+albumID).select();
}//editAlbumName(albumID)
function editAlbumDescription(albumID)
{
	$('editalbumdescription_'+albumID).style.display = 'none';
	$('albumdescriptionfrm_'+albumID).style.display = 'inline';
	$('album_description_'+albumID).value = (html_entity_decode($('editalbumdescription_'+albumID).innerHTML,'ENT_QUOTES'));
	$('album_description_'+albumID).select();
}//editAlbumDescription(albumID)
function hideEditAlbumNameField(albumID,fieldValue)
{
	if(fieldValue==''){ fieldValue = $('editalbumname_'+albumID).innerHTML; }
	$('albumname').innerHTML=fieldValue;
	$('editalbumname_'+albumID).innerHTML = fieldValue;
	$('album_name_'+albumID+'failed').className='hidden';
	$('albumnamefrm_'+albumID).style.display = 'none';
	$('editalbumname_'+albumID).style.display = 'block';
}//hideEditAlbumNameField(albumID,fieldValue)
function hideEditAlbumDescriptionField(albumID,fieldValue)
{
	if(fieldValue==''){ fieldValue = '(type a description)'; }
	if(fieldValue=='null'){ fieldValue = $('editalbumdescription_'+albumID).innerHTML; }
	$('editalbumdescription_'+albumID).innerHTML = fieldValue;
	$('album_description_'+albumID+'failed').className='hidden';	
	$('albumdescriptionfrm_'+albumID).style.display = 'none';
	$('editalbumdescription_'+albumID).style.display = 'block';
}//hideEditAlbumDescriptionField(albumID,fieldValue)
function closeAllImageSections(imageID)
{
	for(i=0; i<imageIDArr.length; i++)
	{
		if(imageID!=''){if((imageIDArr[i]==imageID)||(imageIDArr[i]==null)){continue;}}
		if(imageID==''){if(imageIDArr[i]==null){continue;}}
		$('imageinfo_'+imageIDArr[i]).style.display='none';
		$('editimagename_'+imageIDArr[i]).style.display='none';
		$('editimagedescription_'+imageIDArr[i]).style.display='none';
		$('editimagetags_'+imageIDArr[i]).style.display='none';
		$('editimagealbum_'+imageIDArr[i]).style.display='none';
		$('editimagecover_'+imageIDArr[i]).style.display='none';
		$('imagenamefrm_'+imageIDArr[i]).style.display='none';
		$('imagedescriptionfrm_'+imageIDArr[i]).style.display='none';
		$('imagetagsfrm_'+imageIDArr[i]).style.display='none';
		$('imagealbumfrm_'+imageIDArr[i]).style.display='none';
		$('imageoption_'+imageIDArr[i]).style.display='none';
		$('imagedelete_'+imageIDArr[i]).style.display = 'none';
		imageMorphArr[imageIDArr[i]].start({display: 'block',width: '100px',height: '100px',padding: '0px',cursor: 'pointer'});
		$('imagethumb_'+imageIDArr[i]).fade(1);
	}//for
}//closeAllImageSections(imageID)
function closeImageSection(imageID)
{
	$('editimagename_'+imageID).style.display='block';
	$('editimagedescription_'+imageID).style.display='block';
	$('editimagealbum_'+imageID).style.display='block';
	$('imageinfo_'+imageID).style.display='none';
	$('imagenamefrm_'+imageID).style.display='none';
	$('imagedescriptionfrm_'+imageID).style.display='none';
	$('imagetagsfrm_'+imageID).style.display='none';
	$('imagealbumfrm_'+imageID).style.display='none';
	$('imageoption_'+imageID).style.display='none';
	$('imagedelete_'+imageID).style.display='none';
	imageMorphArr[imageID].start({display: 'block',width: '100px',height: '100px',padding: '0px',cursor: 'pointer'});
	$('imagethumb_'+imageID).fade(1);
	openMorphImageID=null;
}//closeImageSection(imageID)
function imageSectionToggler(imageID)
{
	if(organizealbumimagescheckbox!='checked')
	{
		if(openMorphImageID!=imageID)
		{
			closeAllImageSections(imageID);
			$('imageinfo_'+imageID).style.display='block';
			$('editimagename_'+imageID).style.display='block';
			$('editimagedescription_'+imageID).style.display='block';
			$('editimagetags_'+imageID).style.display='block';
			$('editimagealbum_'+imageID).style.display='block';
			$('editimagecover_'+imageID).style.display='block';
			$('imageoption_'+imageID).style.display='block';
			$('imagenamefrm_'+imageID).style.display='none';
			$('imagedescriptionfrm_'+imageID).style.display='none';
			$('imagedelete_'+imageID).style.display = 'none';
			imageMorphArr[imageID].start({display: 'block',width: '520px',height: '300px',padding: '10px',cursor: 'default'});//width: '410px'
			openMorphImageID = imageID;
		}//if
	}//if
}//imageSectionToggler(imageID)
function editImageName(imageID)
{
	var imageNameOld = html_entity_decode($('editimagename_'+imageID).innerHTML,'ENT_QUOTES');
	$('editimagename_'+imageID).style.display='none';
	$('image_name_'+imageID).value = imageNameOld.toLowerCase();
	$('imagenamefrm_'+imageID).style.display='block';
	$('image_name_'+imageID).select();
	return false;
}//editImageName(imageID)
function hideEditImageNameField(imageID,fieldValue)
{
	if(fieldValue!=''){$('editimagename_'+imageID).innerHTML = fieldValue.toUpperCase();}
	$('image_name_'+imageID+'failed').className='hidden';	
	$('imagenamefrm_'+imageID).style.display = 'none';
	$('editimagename_'+imageID).style.display = 'block';	
}//hideEditImageNameField(imageID,fieldValue)
function editImageDescription(imageID)
{
	var imageDescriptionOld = html_entity_decode($('editimagedescription_'+imageID).innerHTML,'ENT_QUOTES');
	$('editimagedescription_'+imageID).style.display='none';
	$('image_description_'+imageID).value = imageDescriptionOld;
	$('imagedescriptionfrm_'+imageID).style.display='block';
	$('image_description_'+imageID).select();
	return false;
}//editImageDescription(imageID)
function hideEditImageDescriptionField(imageID,fieldValue)
{
	if(fieldValue!=''){$('editimagedescription_'+imageID).innerHTML = fieldValue;}
	$('image_description_'+imageID+'failed').className='hidden';	
	$('imagedescriptionfrm_'+imageID).style.display = 'none';
	$('editimagedescription_'+imageID).style.display = 'block';
}//hideEditImageDescriptionField(imageID,fieldValue)
function editImageTags(imageID)
{
	var imageTagsOld = html_entity_decode($('editimagetags_'+imageID).innerHTML,'ENT_QUOTES');
	$('editimagetags_'+imageID).style.display='none';
	$('image_tags_'+imageID).value = imageTagsOld.toLowerCase();
	$('imagetagsfrm_'+imageID).style.display='block';
	$('image_tags_'+imageID).select();
	$('imagetagsuggestions_'+imageID).innerHTML='';
	$('imagetagsuggestions_'+imageID).className='hidden';
	return false;
}//editImageTags(imageID)
function hideEditImageTagsField(imageID,fieldValue)
{
	if(fieldValue!=''){$('editimagetags_'+imageID).innerHTML = fieldValue;}
	$('image_tags_'+imageID+'failed').className='hidden';
	$('imagetagsfrm_'+imageID).style.display = 'none';
	$('editimagetags_'+imageID).style.display='block';
}//hideEditImageTagsField(imageID)
function editImageAlbum(imageID)
{
	var imageAlbumOld = $('editimagealbum_'+imageID).innerHTML;
	$('editimagealbum_'+imageID).style.display='none';
	$('image_album_'+imageID).value = imageAlbumOld.toLowerCase();
	$('imagealbumfrm_'+imageID).style.display='block';
	return false;
}//editImageTags(imageID)
function hideEditImageAlbumField(imageID,fieldValue)
{
	if(fieldValue!='')
	{
		$('imagesection_'+imageID).fade(0); 
		$('imagesection_'+imageID).dispose(); 
		for(var i=0; i<imageIDArr.length; i++){if(imageIDArr[i]==imageID){imageIDArr[i]=null;}}
	}
	else
	{
		$('image_album_'+imageID+'failed').className='hidden';
		$('imagealbumfrm_'+imageID).style.display = 'none';
		$('editimagealbum_'+imageID).style.display='block';
	}//
}//hideEditImageTagsField(imageID)
function deleteImage(imageID)
{
	$('editimagename_'+imageID).style.display='block';
	$('editimagedescription_'+imageID).style.display='block';
	$('imageoption_'+imageID).style.display = 'none';
	$('imagenamefrm_'+imageID).style.display = 'none';
	$('imagedescriptionfrm_'+imageID).style.display = 'none';
	$('imageinfo_'+imageID).style.display = 'none';
	$('imagedelete_'+imageID).style.display = 'block';
	imageMorphArr[imageID].start({display: 'block',width: '320px',height: '100px',padding: '0px',cursor: 'default'});
	$('imagethumb_'+imageID).fade(0.3);
}//deleteImage(imageID)
function completeDeleteImage(imageID)
{
	$('imagesection_'+imageID).fade(0);
	for(var i=0; i<imageIDArr.length; i++){if(imageIDArr[i]==imageID){imageIDArr[i]=null;}}
	imageMorphArr[imageID]=null;
	$('imagesection_'+imageID).dispose();
}//completeDeleteImage(imageID)
function dontDeleteImage(imageID)
{
	$('imageoption_'+imageID).style.display = 'block';
	$('imagedelete_'+imageID).style.display = 'none';
	$('imageinfo_'+imageID).style.display = 'block';
	imageMorphArr[imageID].start({display: 'block',width: '520px',height: '300px',padding: '10px',cursor: 'default'});
	$('imagethumb_'+imageID).fade(1);
	openMorphImageID = imageID;
}//dontDeleteImage(imageID)
function setAlbumCover(imageID)
{
	$albumID = parseInt($('aid').innerHTML);
	for(i=0; i<imageIDArr.length; i++)
	{
		if((imageIDArr[i]==imageID)||(imageIDArr[i]==null)){continue;}
		$('image_cover_'+imageIDArr[i]).src='./jdlayout/images/off.gif';
	}
	$('image_cover_'+imageID).src='./jdlayout/images/on.gif';
	$('album_coverid_'+$albumID).innerHTML = imageID;
	validateNsubmit('album_coverid_'+$albumID);
	$('cover').src=$('imagethumb_'+imageID).src;
}//setAlbumCover(imageID)
function displayImageTagSuggestions(imageID,suggestionsArr)
{
	var suggestions='';
	$('imagetagsuggestions_'+imageID).innerHTML='';
	if(suggestionsArr.length!=0)
	{
		for (var i=0; i<suggestionsArr.length; i++)
		{
			$('imagetagsuggestions_'+imageID).innerHTML += '<li onClick="addTagSuggestion('+imageID+',this)">'+suggestionsArr[i]+', '+'</li>';
		}
		$('imagetagsuggestions_'+imageID).className='imagetagsuggestions';
	}//if
	else{$('imagetagsuggestions_'+imageID).className='hidden';}
}//displayImageTagSuggestions(suggestionsArr)

function addTagSuggestion(imageID,tagElement)
{
	var tmpStr, tempArr;
	tmpStr='';
	if($('image_tags_'+imageID).value.indexOf(',')==-1)
	{
		$('image_tags_'+imageID).value = tagElement.innerHTML;
	}
	else
	{
		var tempArr = $('image_tags_'+imageID).value.split(',');
		for(var i=0; i<(tempArr.length-1); i++)
		{
			tempArr[i] = trim(tempArr[i],'');
			
			//check if the selected tag already exists in the text field
			$tempVar = tempArr[i]+", ";
			if($tempVar.toLowerCase()==tagElement.innerHTML.toLowerCase()){alertmessages('Warning','This tag already exists'); return 0;}
			
			if(i==0){tmpStr = tempArr[i];}
			else{tmpStr += ', '+tempArr[i];}
		}
		$('image_tags_'+imageID).value = tmpStr+', '+tagElement.innerHTML;
	}
}//addTagSuggestion(imageID,tagElement)

function initImageMorphs(imageID)
{
	imageMorphArr[imageID] = new Fx.Morph('imagesection_'+imageID);
	imageMorphElementsArr[imageID] = 'disabled';
	$('imagesection_'+imageID).addEvent('click',function(e){
		e.stop(); 
		if(imageMorphElementsArr[imageID]=='disabled'){initImageSectionElement(imageID);}
		imageSectionToggler(imageID);
	});
}//initImageMorphs(imageID)

function initImageSectionElement(imageID)
{
	imageMorphElementsArr[imageID]='enabled';
	$('editimagename_'+imageID).addEvent('click',function(e){e.stop(); editImageName(imageID);});
	$('imagenamecancelbutt_'+imageID).addEvent('click',function(e){e.stop(); hideEditImageNameField(imageID,'');});
	$('imagenamesubmitbutt_'+imageID).addEvent('click',function(e){e.stop(); validateNsubmit('image_name_'+imageID);});
	$('editimagedescription_'+imageID).addEvent('click',function(e){e.stop(); editImageDescription(imageID);});
	$('imagedescriptioncancelbutt_'+imageID).addEvent('click',function(e){e.stop(); hideEditImageDescriptionField(imageID,'');});
	$('imagedescriptionsubmitbutt_'+imageID).addEvent('click',function(e){e.stop(); validateNsubmit('image_description_'+imageID);});
	$('editimagetags_'+imageID).addEvent('click',function(e){e.stop(); editImageTags(imageID);});
	$('imagetagscancelbutt_'+imageID).addEvent('click',function(e){e.stop(); hideEditImageTagsField(imageID,'');});
	$('imagetagssubmitbutt_'+imageID).addEvent('click',function(e){e.stop(); validateNsubmit('image_tags_'+imageID);});
	$('editimagealbum_'+imageID).addEvent('click',function(e){e.stop(); editImageAlbum(imageID);});
	$('imagealbumcancelbutt_'+imageID).addEvent('click',function(e){e.stop(); hideEditImageAlbumField(imageID,'');});
	$('imagealbumsubmitbutt_'+imageID).addEvent('click',function(e){e.stop(); validateNsubmit('image_album_'+imageID);});
	$('editimagecover_'+imageID).addEvent('click',function(e){e.stop(); setAlbumCover(imageID);});
	$('image_tags_'+imageID).addEvent('keyup',function(e){
		e.stop(); 
		var temp = trim($('image_tags_'+imageID).value,'');
		if(temp!='')
		{
			if($('image_tags_'+imageID).value.indexOf(',')==-1) //there's no other tag in the text field
			{
				searchNsuggestImageTags(imageID,$('image_tags_'+imageID).value);
			}
			else //there already is another tag in the text field
			{
				var tempArr = $('image_tags_'+imageID).value.split(',');
				tempArr[tempArr.length-1] = trim(tempArr[tempArr.length-1],'');
				if(tempArr[tempArr.length-1]!=''){searchNsuggestImageTags(imageID,tempArr[tempArr.length-1]);}
				else{$('imagetagsuggestions_'+imageID).innerHTML=''; $('imagetagsuggestions_'+imageID).className='hidden';}
			}
		}
		else
		{
			$('imagetagsuggestions_'+imageID).innerHTML='';
			$('imagetagsuggestions_'+imageID).className='hidden';
		}
	});	
	$('image_name_'+imageID).addEvent('click',function(e){e.stop(); if($('image_name_'+imageID).value=='(type a name)'){$('image_name_'+imageID).value='';}});
	$('image_description_'+imageID).addEvent('click',function(e){e.stop(); if($('image_description_'+imageID).value=='(type a description)'){$('image_description_'+imageID).value='';}});
	$('image_description_'+imageID).addEvent('keyup',function(e){e.stop(); countChars('image_description_'+imageID,'icounter_'+imageID,imageDescriptionMaxLength);});
	$('image_tags_'+imageID).addEvent('click',function(e){e.stop(); if( $('image_tags_'+imageID).value=='(type some tags)'){$('image_tags_'+imageID).value='';}});
	$('deleteimage_'+imageID).addEvent('click',function(e){e.stop(); deleteImage(imageID);});
	$('completedeleteimage_'+imageID).addEvent('click',function(e){e.stop(); deleteImages(imageID);});
	$('dontdeleteimage_'+imageID).addEvent('click',function(e){e.stop(); dontDeleteImage(imageID);});
	$('closeimagesection_'+imageID).addEvent('click',function(e){e.stop(); closeImageSection(imageID);});
}//initImageSectionElement()
function organiazeAlbumImages()
{
	switch(organizealbumimagescheckbox)
	{
		case 'unchecked':
			closeAllImageSections(''); $('organizealbumimagescheckbox').src='./jdlayout/images/checked.gif'; organizealbumimagescheckbox='checked'; 
			initDragNDropCapability();
		break;
		case 'checked': 
			$('organizealbumimagescheckbox').src='./jdlayout/images/unchecked.gif'; organizealbumimagescheckbox='unchecked'; 
			sortableObj.detach();
		break;
		default: break;
	}
}//organiazeAlbumImages()
function initDragNDropCapability()
{
	sortableObj = new Sortables('albumcontents', { 
		clone:true, revert: true, initialize: function() {},  
		/* once an item is selected */  
		onStart: function(el) { el.setStyle('background','#FFFFFF');},
		/* when a drag is complete */  
		onComplete: function(el) {  
			el.setStyle('background','#FFFFFF');  
			//build a string of the order  
			var sort_order = '';  
			$$('#albumcontents ul.imagesections').each(function(ul) { sort_order = sort_order +  ul.get('rel')  + '.::.'; });
			$albumID = parseInt($('aid').innerHTML);
			$('album_imagesorder_'+albumID).innerHTML = sort_order;
			validateNsubmit('album_imagesorder_'+albumID);
		}//onComplete
	});
}//initDragNDropCapability()