// JavaScript Document

/* GLOBAL JS ARRAYS */
var catIDArr = new Array;
var catSlideArr = new Array;
var albumIDArr = new Array;
var categoriesComboBox = new Array;
var catSectionElementsArr = new Array;
var albumSectionElementsArr = new Array;

var albumDescriptionMaxLength = 1500;

window.addEvent('domready', function(){
	var spanArr = $('categories').getElementsByTagName('span');
	var newCatID = new Array;
	var counter=0;
	var tempid, catIDArrPointer, albumIDArrPointer;

	for (var i=0; i<spanArr.length; i++){tempid=spanArr[i].id.split('_'); if(tempid[0]=='catsp'){catIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for (var i=0; i<catIDArr.length; i++){ catIDArrPointer = i; initCatSliders(catIDArr[catIDArrPointer]);}//for

	$('addcategory').addEvent('click', function(e) {
		e.stop();
		catIDArr[catIDArr.length]=parseInt('999'+catIDArr.length);
		//catIDArr[catIDArr.length]='99x'+catIDArr.length;
		catIDArrPointer = (catIDArr.length-1);
		
		var listitem = new Element('li', {
			'class': 'cats',
			'id': 'cat_'+catIDArr[catIDArrPointer]+'',
			'html': '\n\n<ul class="catbanners"><li class="catnames"><span class="catsps" id="catsp_'+catIDArr[catIDArrPointer]+'">(Category Name)</span><span class="catfrms" id="catnamefrm_'+catIDArr[catIDArrPointer]+'"><input class="text" type="text" id="cat_name_'+catIDArr[catIDArrPointer]+'" value="" maxlength="18" /><input class="button" type="button" id="catnamesubmitbutt_'+catIDArr[catIDArrPointer]+'" value="save" /><input class="button" type="button" id="catnamecancelbutt_'+catIDArr[catIDArrPointer]+'" value="cancel" /><div id="cat_name_'+catIDArr[catIDArrPointer]+'failed" class="hidden"></div></span><span class="hidden" id="catnameloader_'+catIDArr[catIDArrPointer]+'"></span><span class="hidden" id="ncat_'+catIDArr[catIDArrPointer]+'">'+catIDArr[catIDArrPointer]+'</span></li><li class="catinfos"><span id="albumsnum_'+catIDArr[catIDArrPointer]+'">0 albums</span>:: <span id="albumsimagesnum_'+catIDArr[catIDArrPointer]+'">0 images</span><span class="togglers" id="togglecat_'+catIDArr[catIDArrPointer]+'">show</span></li></ul><div class="separator"></div><div class="catsections" id="catsection_'+catIDArr[catIDArrPointer]+'"><span class="hidden" id="categorytypedesc_'+catIDArr[catIDArrPointer]+'"></span><ul class="catoptions" id="catoption_'+catIDArr[catIDArrPointer]+'"><li id="addalbum_'+catIDArr[catIDArrPointer]+'">Add Album</li> | <li id="deletecat_'+catIDArr[catIDArrPointer]+'">Delete</li></ul><ul class="catdeletes" id="catdelete_'+catIDArr[catIDArrPointer]+'"><li class="deletecatmsg">delete this category and all its albums?</li><li class="completedeletecategories"><span id="completedeletecategory_'+catIDArr[catIDArrPointer]+'">yes</span></li><li class="dontdeletecategories"><span id="dontdeletecategory_'+catIDArr[catIDArrPointer]+'">no</span></li></ul><ul class="caterrmsguls" id="caterrmsgul_'+catIDArr[catIDArrPointer]+'"><li class="caterrmsgs" id="caterrmsg_'+catIDArr[catIDArrPointer]+'">Can\'t create new album. Please name your new category first.</li><li class="caterrmsgokbutts"><span id="caterrmsgokbutt_'+catIDArr[catIDArrPointer]+'">ok</span></li></ul><div class="catalbumcontainers" id="catalbumcontainer_'+catIDArr[catIDArrPointer]+'" ><div>\n\n'});
			
		var catanchoritem = new Element('span',{ 'id': 'newcategoryanchor', 'html': ''});
		//Add the new category
		$('categoryanchor').appendChild(catanchoritem);
		$('categoryanchor').appendChild(listitem);
		$('categoryanchor').id = '';
		$('newcategoryanchor').id = 'categoryanchor';
			
		initCatSliders(catIDArr[catIDArrPointer]);
		//slide out all the open categories
		for(var j=0; j<catIDArr.length; j++){ if(catIDArr[j]==null){continue;} $('togglecat_'+catIDArr[j]).innerHTML='show'; catSlideArr[catIDArr[j]].slideOut();}
		//slide in the new category
		$('togglecat_'+catIDArr[catIDArrPointer]).innerHTML='show';
		catSlideArr[catIDArr[catIDArrPointer]].toggle();
	});
});
function initAlbums()
{
	var counter, tempid, albumIDArrPointer;
	counter=0;
	var ulArr = $('categories').getElementsByTagName('div');
	for (var i=0; i<ulArr.length; i++){tempid = ulArr[i].id.split('_'); if(tempid[0]=='album'){albumIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for (var i=0; i<albumIDArr.length; i++){ albumIDPointer = i; if(albumIDArr[albumIDPointer]!=null){initAlbumElements(albumIDArr[albumIDPointer]);}}
}//initAlbums()
/*
CATEGORIES
*/
function editCategoryName(categoryID)
{
	$('catsp_'+categoryID).style.display='none';
	$('cat_name_'+categoryID).value = (html_entity_decode($('catsp_'+categoryID).innerHTML,'ENT_QUOTES')).toLowerCase();		
	$('catnamefrm_'+categoryID).style.display='block';
	$('cat_name_'+categoryID).select();
	return false;
}
function hideEditCategoryNameField(categoryID,fieldValue)
{
	if(fieldValue!=''){$('catsp_'+categoryID).innerHTML = fieldValue.toUpperCase();}
	$('catnamefrm_'+categoryID).style.display='none';
	$('cat_name_'+categoryID+'failed').className='hidden';
	$('catsp_'+categoryID).style.display='block';
}
function catSectionToggler(categoryID, e)
{	
	e.stop();
	for(var j=0; j<catIDArr.length; j++){ if(catIDArr[j]==null){continue;} if(catIDArr[j]!=categoryID) { $('togglecat_'+catIDArr[j]).innerHTML='show'; catSlideArr[catIDArr[j]].slideOut();} }
	//e = new Event(e); 
	catSlideArr[categoryID].toggle();
	if($('togglecat_'+categoryID).innerHTML=='show'){$('togglecat_'+categoryID).innerHTML='hide';}
	else{$('togglecat_'+categoryID).innerHTML='show';}
}
function deleteCategory(categoryID)
{
	var tempArr = $('albumsnum_'+categoryID).innerHTML.split(' ');
	var albumNum = parseInt(tempArr[0]);
	if(albumNum != 0)
	{
		arr = new Array;
		$('albumbanner_'+categoryID).fade(0.3);
		$('catalbums_'+categoryID).fade(0.3);
		if(albumNum!=0)
		{ 
			var catAlbums = $('catalbums_'+categoryID).getElementsByTagName('ul'); 
			for(var i=0; i<catAlbums.length; i++)
			{ 
				arr = catAlbums[i].id.split('_');
				arr[1]=parseInt(arr[1]);
				$('albumoption_'+arr[1]).style.display='none';
				$('albumdelete_'+arr[1]).style.display='none';
				$('albuminfo_'+arr[1]).style.display='block';
				
				$('album_name_'+arr[1]+'failed').className='hidden';	
				$('albumnamefrm_'+arr[1]).style.display = 'none';
				$('editalbumname_'+arr[1]).style.display = 'block';
				$('album_description_'+arr[1]+'failed').className='hidden';	
				$('albumdescriptionfrm_'+arr[1]).style.display = 'none';
				$('editalbumdescription_'+arr[1]).style.display = 'block';
			}
		}
	}
	$('catoption_'+categoryID).style.display='none';
	$('catdelete_'+categoryID).style.display='block';
	catSlideArr[categoryID].hide(); catSlideArr[categoryID].show();
}//deleteCategory(categoryIndex)
function dontDeleteCategory(categoryID)
{
	var tempArr = $('albumsnum_'+categoryID).innerHTML.split(' ');
	if(parseInt(tempArr[0])!=0)
	{ 
		var catAlbums = $('catalbums_'+categoryID).getElementsByTagName('ul'); 
		$('albumbanner_'+categoryID).fade(1); 
		$('catalbums_'+categoryID).fade(1); 
		for(var i=0; i<catAlbums.length; i++) 
		{
			var arr = catAlbums[i].id.split('_');
			arr[1]=parseInt(arr[1]);
			$('albumoption_'+arr[1]).style.display='block';
			$('albumcover_'+arr[1]).fade(1);
		}
	}
	$('catoption_'+categoryID).style.display='block';
	$('catdelete_'+categoryID).style.display='none';
	catSlideArr[categoryID].hide(); catSlideArr[categoryID].show();
}//dontDeleteCategory(categoryIndex)
function completeDeleteCategory(categoryID)
{
	var dbCategoryID = categoryID;
	var domCategoryID = null;
	
	var spanArr = $('categories').getElementsByTagName('span');
	for (var i=0; i<spanArr.length; i++)
	{tempid = spanArr[i].id.split('_');if(tempid[0]=='ncat'){if(parseInt($('ncat_'+tempid[1]).innerHTML) == dbCategoryID){domCategoryID = parseInt(tempid[1]);}}}
	
	//unset the album values of the albums that belong to that category
	spanArr = $('catalbumcontainer_'+domCategoryID).getElementsByTagName('span');
	for (var i=0; i<spanArr.length; i++)
	{
		tempid = spanArr[i].id.split('_');
		if(tempid[0]=='albumcategoryid')
		{
			if(parseInt($('albumcategoryid_'+tempid[1]).innerHTML) == dbCategoryID)
			{
				albumID = parseInt(tempid[1]);
				for(var j=0; j<albumIDArr.length; j++){ if(albumIDArr[j]==albumID){albumIDArr[j]=null; categoriesComboBox[albumID]=null;}}
			}
		}
	}
	$('cat_'+domCategoryID).fade(0);
	$('cat_'+domCategoryID).style.display='none';
	//removeHTMLElement($('cat_'+domCategoryID));
	$('cat_'+domCategoryID).dispose();
	for(var i=0; i<catIDArr.length; i++){ if(catIDArr[i]==domCategoryID){catSlideArr[catIDArr[i]]=null; catIDArr[i]=null;} }
	updateCatComboBoxes();
}//completeDeleteCategory(categoryID)
	
function addNewAlbum(categoryID)
{
	//for albums that will be created for a newly created category
	if($('ncat_'+categoryID).innerHTML.substr(0,3)!='999'){var categoryNID = parseInt($('ncat_'+categoryID).innerHTML);}
	else {
		$('catoption_'+categoryID).style.display='none';
		$('catdelete_'+categoryID).style.display='none';
		$('caterrmsgul_'+categoryID).style.display='block';
		catSlideArr[categoryID].hide(); catSlideArr[categoryID].show();
		return 1;
	}

	albumIDArr[albumIDArr.length]=parseInt('888'+albumIDArr.length);
	albumIDArrPointer = (albumIDArr.length-1);
	
	var tempArr = $('albumsnum_'+categoryID).innerHTML.split(' ');
	var oldCatAlbumsNum = parseInt(tempArr[0]);
	var newAlbumCatNum = (oldCatAlbumsNum+1);
	
	//if there is no other album in this category
	if(oldCatAlbumsNum == 0)
	{
		var spanitem01 = new Element('span', {
			'class': 'albumbanners',
			'id': 'albumbanner_'+categoryID+'',
			'html': 'Albums'
		});
		var spanitem02 = new Element('span', {
			'class': 'catalbums',
			'id': 'catalbums_'+categoryID+'',
			'html': ''
		});
		var spanitem03 = new Element('span', {
			'id': 'albumanchor_'+categoryID+'',
			'html': ''
		});
		$('catalbumcontainer_'+categoryID).appendChild(spanitem01);
		$('catalbumcontainer_'+categoryID).appendChild(spanitem02);
		$('catalbums_'+categoryID).appendChild(spanitem03);
	}//
	createCatComboBox(albumIDArr[albumIDArrPointer],categoryID,'create');
	var ulitem = new Element('ul', {
		'class': 'albums',
		'id': 'album_'+albumIDArr[albumIDArrPointer]+'',
		'html': '\n\n<span class="hidden" id="nalbum_'+albumIDArr[albumIDArrPointer]+'">'+albumIDArr[albumIDArrPointer]+'</span><span class="albumcovers" id="albumcover_'+albumIDArr[albumIDArrPointer]+'"><img src="./jdimages/largethumbnails/defaultcover.png" alt="album cover" /></span><ul class="albumdatas" id="albumdata_'+albumIDArr[albumIDArrPointer]+'"><li class="albuminfos" id="albuminfo_'+albumIDArr[albumIDArrPointer]+'"><span class="editalbumnames" id="editalbumname_'+albumIDArr[albumIDArrPointer]+'">(Type Album Name)</span><span class="albumfrms" id="albumnamefrm_'+albumIDArr[albumIDArrPointer]+'"><input class="text" type="text" id="album_name_'+albumIDArr[albumIDArrPointer]+'" value="" maxlength="24" /><input class="button" type="button" id="albumnamesubmitbutt_'+albumIDArr[albumIDArrPointer]+'" value="save" /><input class="button" type="button" id="albumnamecancelbutt_'+albumIDArr[albumIDArrPointer]+'" value="cancel" /><span id="album_name_'+albumIDArr[albumIDArrPointer]+'failed" class="hidden"></span></span><span id="albumnameloader_'+albumIDArr[albumIDArrPointer]+'" class="hidden"></span><span class="editalbumdescriptions" id="editalbumdescription_'+albumIDArr[albumIDArrPointer]+'">(type a description)</span><span class="albumfrms" id="albumdescriptionfrm_'+albumIDArr[albumIDArrPointer]+'"><textarea class="text" id="album_description_'+albumIDArr[albumIDArrPointer]+'" cols="21" rows="5" wrap="hard"></textarea><span class="editdescriptionbuttons"><input class="button" type="button" id="albumdescriptionsubmitbutt_'+albumIDArr[albumIDArrPointer]+'" value="save" /><input class="button" type="button" id="albumdescriptioncancelbutt_'+albumIDArr[albumIDArrPointer]+'" value="cancel" /></span><span class="charcounters"><span class="counters" id="acounter_'+albumIDArr[albumIDArrPointer]+'">'+albumDescriptionMaxLength+'</span> remaining characters</span><span id="album_description_'+albumIDArr[albumIDArrPointer]+'failed" class="hidden"></span></span><span id="albumdescriptionloader_'+albumIDArr[albumIDArrPointer]+'" class="hidden"></span><span class="editalbumtagids" id="editalbumtagid_'+albumIDArr[albumIDArrPointer]+'">Move to category: </span><span class="albumfrms" id="albumtagidfrm_'+albumIDArr[albumIDArrPointer]+'">'+'<span class="albumcatcomboboxes" id="albumcatcombobox_'+albumIDArr[albumIDArrPointer]+'">'+categoriesComboBox[albumIDArr[albumIDArrPointer]]+'</span>'+'<input class="button" type="button" id="albumtagidsubmitbutt_'+albumIDArr[albumIDArrPointer]+'" value="save" /><input class="button" type="button" id="albumtagidcancelbutt_'+albumIDArr[albumIDArrPointer]+'" value="cancel" /><span id="album_tagid_'+albumIDArr[albumIDArrPointer]+'failed" class="hidden"></span></span><span id="albumtagidloader_'+albumIDArr[albumIDArrPointer]+'" class="hidden"></span><span class="editimagescounts" id="editimagescount_'+albumIDArr[albumIDArrPointer]+'">0 images</span><span class="editablumupdateds" id="editalbumupdated_'+albumIDArr[albumIDArrPointer]+'">Updated: </span></li><li class="albumdeletes" id="albumdelete_'+albumIDArr[albumIDArrPointer]+'"><span class="deletealbummsg">delete this album and all its images?</span><div class="completedeletealbums"><span id="completedeletealbum_'+albumIDArr[albumIDArrPointer]+'">yes</span></div><div class="dontdeletealbums"><span id="dontdeletealbum_'+albumIDArr[albumIDArrPointer]+'">no</span></div></li></ul><span class="hidden" id="albumcategoryid_'+albumIDArr[albumIDArrPointer]+'">'+categoryNID+'</span><div class="albumoptions" id="albumoption_'+albumIDArr[albumIDArrPointer]+'"><div class="editalbumimages"><span id="editalbumimages_'+albumIDArr[albumIDArrPointer]+'">edit images</span></div><div class="deletealbums"><span id="deletealbum_'+albumIDArr[albumIDArrPointer]+'">delete</span></div></div>\n\n'
	});
		
	var albumanchoritem = new Element('span',{ 'id': 'newalbumanchor_'+categoryID, 'html': ''});
	//Add the new album
	$('albumanchor_'+categoryID).appendChild(albumanchoritem);
	$('albumanchor_'+categoryID).appendChild(ulitem);
	$('albumanchor_'+categoryID).id = '';
	$('newalbumanchor_'+categoryID).id = 'albumanchor_'+categoryID;

	//$('catalbums_'+categoryID).appendChild(ulitem);
	albumSectionElementsArr[albumIDArr[albumIDArrPointer]]='disabled';
	initAlbumElements(albumIDArr[albumIDArrPointer]);
	$('albumsnum_'+categoryID).innerHTML = newAlbumCatNum+' albums';
	$('album_'+albumIDArr[albumIDArrPointer]).style.visibility='hidden';
	catSlideArr[categoryID].show();
	if(sleep(0.1)==0){$('album_'+albumIDArr[albumIDArrPointer]).fade(1);}
}//addNewAlbum(categoryID)
function categoryMessageOKButton(categoryID)
{
	$('catoption_'+categoryID).style.display='block';
	$('catdelete_'+categoryID).style.display='none';
	$('caterrmsgul_'+categoryID).style.display='none';
	catSlideArr[categoryID].hide(); catSlideArr[categoryID].show();
}//categoryMessageOKButton(categoryID)

function initCatSliders(categoryID)
{
	catSlideArr[categoryID] = new Fx.Slide('catsection_'+categoryID);
	catSlideArr[categoryID].hide();
	catSectionElementsArr[categoryID]='disabled';
	$('togglecat_'+categoryID).addEvent('click',function(e){
		if(catSectionElementsArr[categoryID]=='disabled'){initCatSectionElement(categoryID); initAlbums();}
		catSectionToggler(categoryID, e);
	});
	$('catsp_'+categoryID).addEvent('click', function(e){e.stop(); editCategoryName(categoryID); });
	$('catnamesubmitbutt_'+categoryID).addEvent('click', function(e){e.stop(); validateNsubmit('cat_name_'+categoryID);});
	$('catnamecancelbutt_'+categoryID).addEvent('click', function(e){e.stop(); hideEditCategoryNameField(categoryID,'');});
	$('cat_name_'+categoryID).addEvent('click',function(e){e.stop(); if($('cat_name_'+categoryID).value=='(category name)'){$('cat_name_'+categoryID).value='';}});
}//initCatSliders(categoryID)

function initCatSectionElement(categoryID)
{
	catSectionElementsArr[categoryID]='enabled';
	if(($('categorytypedesc_'+categoryID).innerHTML!='postsalbum') && ($('categorytypedesc_'+categoryID).innerHTML!='welcomepageimagesalbum') )
		{$('addalbum_'+categoryID).addEvent('click', function(e){e.stop(); addNewAlbum(categoryID);});}
	$('deletecat_'+categoryID).addEvent('click', function(e){e.stop(); deleteCategory(categoryID);});
	$('dontdeletecategory_'+categoryID).addEvent('click', function(e){e.stop(); dontDeleteCategory(categoryID);});
	$('completedeletecategory_'+categoryID).addEvent('click', function(e){ e.stop(); var nCategoryID = parseInt($('ncat_'+categoryID).innerHTML); deleteCategories(nCategoryID);});
	$('caterrmsgokbutt_'+categoryID).addEvent('click', function(e){e.stop(); categoryMessageOKButton(categoryID);});
}//initCatSectionElement()

/*
ALBUMS
*/
	
function deleteAlbum(albumID)
{
	$('editalbumname_'+albumID).style.display = 'block';
	$('albumnamefrm_'+albumID).style.display='none';
	$('albumdescriptionfrm_'+albumID).style.display = 'none';
	$('editalbumdescription_'+albumID).style.display = 'block';
	$('album_name_'+albumID+'failed').className='hidden';
	$('albumoption_'+albumID).style.display='none';
	$('albumcover_'+albumID).fade(0.3);
	$('albuminfo_'+albumID).style.display='none';
	$('albumdelete_'+albumID).style.display='block';
}//deleteAlbum(albumID)
function dontDeleteAlbum(albumID)
{	
	$('albumdelete_'+albumID).style.display='none';
	$('albuminfo_'+albumID).style.display='block';
	$('albumoption_'+albumID).style.display='block';
	$('albumcover_'+albumID).fade(1);
}//dontDeleteAlbum(albumID)
function completeDeleteAlbum(albumID,categoryID)
{
	var dbCategoryID = categoryID;
	var domCategoryID = null;
	
	var spanArr = $('categories').getElementsByTagName('span');
	for (var i=0; i<spanArr.length; i++)
	{tempid = spanArr[i].id.split('_');if(tempid[0]=='ncat'){if(parseInt($('ncat_'+tempid[1]).innerHTML) == dbCategoryID){domCategoryID = parseInt(tempid[1]);}}}

	spanArr = $('catalbumcontainer_'+domCategoryID).getElementsByTagName('span');
	for (var i=0; i<spanArr.length; i++)
	{
		tempid = spanArr[i].id.split('_');
		if(tempid[0]=='nalbum')
		{
			if(parseInt($('nalbum_'+tempid[1]).innerHTML) == albumID)
			{
				albumID = parseInt(tempid[1]);
				domCategoryID +=''; if(domCategoryID.substr(0,3)!='999'){domCategoryID = parseInt($('albumcategoryid_'+albumID).innerHTML);}
			}
		}
	}

	var tempArr = $('albumsnum_'+domCategoryID).innerHTML.split(' ');
	var tempArr2 = $('albumsimagesnum_'+domCategoryID).innerHTML.split(' ');
	var tempArr3 = $('editimagescount_'+albumID).innerHTML.split(' ');
	var newCatAlbumNum = parseInt(tempArr[0]);
	var newCatAlbumImagesNum = parseInt(tempArr2[0]) - parseInt(tempArr3[0]);
	newCatAlbumNum = --newCatAlbumNum;
				
	$('album_'+albumID).fade(0);
	if(newCatAlbumNum == 0)
	{ 
		removeHTMLElement($('catalbumcontainer_'+domCategoryID));
		catSlideArr[domCategoryID].slideOut();
		$('togglecat_'+domCategoryID).innerHTML='show';
		$('albumsnum_'+domCategoryID).innerHTML = newCatAlbumNum+' albums';
		if(newCatAlbumImagesNum == 1){$('albumsimagesnum_'+domCategoryID).innerHTML = newCatAlbumImagesNum+' image';}
		else{$('albumsimagesnum_'+domCategoryID).innerHTML = newCatAlbumImagesNum+' images';}
		for(var i=0; i<albumIDArr.length; i++){ if(albumIDArr[i]==albumID){albumIDArr[i]=null;} }
	}
	else
	{
		catSlideArr[domCategoryID].hide();
		if(newCatAlbumNum == 1){$('albumsnum_'+domCategoryID).innerHTML = newCatAlbumNum+' album';}
		else{$('albumsnum_'+domCategoryID).innerHTML = newCatAlbumNum+' albums';}
		if(newCatAlbumImagesNum == 1){$('albumsimagesnum_'+domCategoryID).innerHTML = newCatAlbumImagesNum+' image';}
		else{$('albumsimagesnum_'+domCategoryID).innerHTML = newCatAlbumImagesNum+' images';}
		$('album_'+albumID).style.display='none';
		if(sleep(0.5)==0){ removeHTMLElement($('album_'+albumID)); }
		for(var i=0; i<albumIDArr.length; i++){ if(albumIDArr[i]==albumID){albumIDArr[i]=null; categoriesComboBox[albumID]=null;} }
		catSlideArr[domCategoryID].show();	
	}
}//completeDeleteAlbum(albumID)
function editAlbumName(albumID)
{
	var categoryID = parseInt($('albumcategoryid_'+albumID).innerHTML);
	closeAlbumElements(albumID)
	$('editalbumname_'+albumID).style.display = 'none';
	$('albumnamefrm_'+albumID).style.display = 'block';
	$('album_name_'+albumID).value = (html_entity_decode($('editalbumname_'+albumID).innerHTML,'ENT_QUOTES')).toLowerCase();
	$('album_name_'+albumID).select();
}//editAlbumName(albumID)
function hideEditAlbumNameField(albumID,fieldValue)
{
	var categoryID = parseInt($('albumcategoryid_'+albumID).innerHTML);
	if(fieldValue==''){ fieldValue = $('editalbumname_'+albumID).innerHTML; }
	$('editalbumname_'+albumID).innerHTML = fieldValue;
	$('album_name_'+albumID+'failed').className='hidden';	
	$('albumnamefrm_'+albumID).style.display = 'none';
	$('editalbumname_'+albumID).style.display = 'block';
}//hideEditAlbumNameField(albumID)
function editAlbumDescription(albumID)
{
	var categoryID = parseInt($('albumcategoryid_'+albumID).innerHTML);
	closeAlbumElements(albumID)
	$('editalbumdescription_'+albumID).style.display = 'none';
	$('albumdescriptionfrm_'+albumID).style.display = 'block';
	$('album_description_'+albumID).value = (html_entity_decode($('editalbumdescription_'+albumID).innerHTML,'ENT_QUOTES'));
	$('album_description_'+albumID).select();
}//editAlbumDescription(albumID)
function hideEditAlbumDescriptionField(albumID,fieldValue)
{
	var categoryID = parseInt($('albumcategoryid_'+albumID).innerHTML);
	if(fieldValue==''){ fieldValue = '(type a description)'; }
	if(fieldValue=='null'){ fieldValue = $('editalbumdescription_'+albumID).innerHTML; }
	$('editalbumdescription_'+albumID).innerHTML = fieldValue;
	$('album_description_'+albumID+'failed').className='hidden';	
	$('albumdescriptionfrm_'+albumID).style.display = 'none';
	$('editalbumdescription_'+albumID).style.display = 'block';
}//hideEditAlbumDescriptionField(albumID,'')
function albumDescriptionSubmitButt(albumID)
{
	var nalbumID = parseInt($('nalbum_'+albumID).innerHTML.substr(0,3));
	if(nalbumID==888){}//
	else{validateNsubmit('album_description_'+albumID);}//
}//albumDescriptionSubmitButt(albumID)
function editAlbumImages(albumID)
{
	var categoryID = parseInt($('albumcategoryid_'+albumID).innerHTML);
	var nalbumID = parseInt($('nalbum_'+albumID).innerHTML.substr(0,3));
	if(nalbumID==888)
	{$('album_description_'+albumID+'failed').innerHTML = 'Please name your new album first.'; $('album_description_'+albumID+'failed').className='error';}//
	//var nalbumID = parseInt($('nalbum_'+albumID).innerHTML.substr(0,3));
	else{
		var albumID = parseInt($('nalbum_'+albumID).innerHTML);
		window.location = './mmeditalbums.php?albumid='+albumID+'&categoryid='+categoryID+'';
	}
}//editAlbumImages(albumID)
function editAlbumCategory(albumID)
{
	var categoryID = parseInt($('albumcategoryid_'+albumID).innerHTML);
	closeAlbumElements(albumID)
	$('editalbumtagid_'+albumID).style.display = 'none';
	$('albumtagidfrm_'+albumID).style.display = 'block';	
}//editAlbumCategory(albumID)
function hideEditAlbumCategoryField(albumID,fieldValue)
{
	var categoryID = parseInt($('albumcategoryid_'+albumID).innerHTML);
	if(fieldValue!='')
	{
		$('albumcategoryid_'+albumID).innerHTML=categoryID;
		$('album_tagid_'+albumID+'failed').className='hidden';	
		$('albumtagidfrm_'+albumID).style.display = 'none';
		$('editalbumtagid_'+albumID).style.display = 'block';
		$('album_'+albumID).fade(0);
		window.location = './mmeditimages.php';
	}
	else if(fieldValue=='')
	{
		$('album_tagid_'+albumID+'failed').className='hidden';	
		$('albumtagidfrm_'+albumID).style.display = 'none';
		$('editalbumtagid_'+albumID).style.display = 'block';
	}
}//hideEditAlbumCategoryField(albumID,fieldValue)
function albumCategorySubmitButt(albumID)
{
	var nalbumID = parseInt($('nalbum_'+albumID).innerHTML.substr(0,3));
	if(nalbumID==888){}//
	else{validateNsubmit('album_tagid_'+albumID);}//
}//albumCategorySubmitButt(albumID)
function closeAlbumElements(albumID)
{
	for (i=0; i<albumIDArr.length; i++)
	{
		if((albumIDArr[i]==null)||(albumIDArr[i]==albumID)){continue;}
		$('editalbumname_'+albumIDArr[i]).style.display = 'block';
		$('albumnamefrm_'+albumIDArr[i]).style.display = 'none';
		$('album_name_'+albumIDArr[i]+'failed').className='hidden';
		$('editalbumdescription_'+albumIDArr[i]).style.display = 'block';
		$('albumdescriptionfrm_'+albumIDArr[i]).style.display = 'none';
		$('album_description_'+albumIDArr[i]+'failed').className='hidden';
		$('editalbumtagid_'+albumIDArr[i]).style.display = 'block';
		$('albumtagidfrm_'+albumIDArr[i]).style.display = 'none';
		$('album_tagid_'+albumIDArr[i]+'failed').className='hidden';
	}//for
}//closeAlbumElements(albumID)
function createCatComboBox(albumID, categoryID, type)
{
	categoriesComboBox[albumID] = '<select class="text" name="album_tagid_'+albumID+'" id="album_tagid_'+albumID+'" >';
	categoriesComboBox[albumID] += '<option value="">[category name]</option>';
	for (var i=0; i<catIDArr.length; i++)
	{
		if(catIDArr[i]==null){continue;}
		var ncatID = $('ncat_'+catIDArr[i]).innerHTML;
		if(ncatID.substr(0,3)=='999'){continue;}
	
		ncatID = parseInt(ncatID);
		if(type=='create'){if(parseInt($('ncat_'+categoryID).innerHTML)==ncatID){continue;}}
		if(categoryID != ncatID)
		{
			categoryName = $('catsp_'+catIDArr[i]).innerHTML; 
			categoriesComboBox[albumID] +='<option value="'+ncatID+'">'+categoryName.toLowerCase()+'</option>';
		}
	}
	categoriesComboBox[albumID] += '</select>';
}//createCatComboBox(albumID, categoryID, type)
function updateCatComboBoxes()
{
	for(var i=0; i<albumIDArr.length; i++)
	{
		if((albumIDArr[i]==null)){continue;}
		else if((albumIDArr[i]!=null))
		{
			var categoryID = parseInt($('albumcategoryid_'+albumIDArr[i]).innerHTML);
			createCatComboBox(albumIDArr[i], categoryID,'update');
			$('album_tagid_'+albumIDArr[i]).dispose();
			$('albumcatcombobox_'+albumIDArr[i]).innerHTML=categoriesComboBox[albumIDArr[i]];
		}
	}//for
}//updateCatComboBoxes()
function initAlbumElements(albumID)
{
	categoryID=parseInt($('albumcategoryid_'+albumID).innerHTML);
	if(catSectionElementsArr[categoryID]=='enabled')
	{
		if(albumSectionElementsArr[albumID]!='enabled')
		{
			albumSectionElementsArr[albumID]='enabled';
			$('editalbumname_'+albumID).addEvent('click', function(e){e.stop(); editAlbumName(albumID);});
			$('editalbumdescription_'+albumID).addEvent('click', function(e){e.stop(); editAlbumDescription(albumID);});
			$('editalbumimages_'+albumID).addEvent('click', function(e){e.stop(); editAlbumImages(albumID);});
			$('editalbumtagid_'+albumID).addEvent('click', function(e){e.stop(); editAlbumCategory(albumID);});
			$('deletealbum_'+albumID).addEvent('click', function(e){e.stop(); deleteAlbum(albumID);});
			$('dontdeletealbum_'+albumID).addEvent('click', function(e){e.stop(); dontDeleteAlbum(albumID);});
			$('completedeletealbum_'+albumID).addEvent('click', function(e){e.stop(); deleteAlbums(albumID);});
			$('albumnamesubmitbutt_'+albumID).addEvent('click', function(e){e.stop(); validateNsubmit('album_name_'+albumID);});
			$('albumdescriptionsubmitbutt_'+albumID).addEvent('click', function(e){e.stop(); albumDescriptionSubmitButt(albumID);});
			$('albumnamecancelbutt_'+albumID).addEvent('click', function(e){e.stop(); hideEditAlbumNameField(albumID,'');});
			$('albumdescriptioncancelbutt_'+albumID).addEvent('click', function(e){e.stop(); hideEditAlbumDescriptionField(albumID,'null');});
			$('albumtagidsubmitbutt_'+albumID).addEvent('click', function(e){e.stop(); albumCategorySubmitButt(albumID);});
			$('albumtagidcancelbutt_'+albumID).addEvent('click', function(e){e.stop(); hideEditAlbumCategoryField(albumID,'');})
			$('album_name_'+albumID).addEvent('click',function(e){e.stop(); if($('album_name_'+albumID).value=='(type album name)'){$('album_name_'+albumID).value='';}});
			$('album_description_'+albumID).addEvent('click',function(e){e.stop(); if($('album_description_'+albumID).value=='(type a description)'){$('album_description_'+albumID).value='';}});
			$('album_description_'+albumID).addEvent('keyup',function(e){e.stop(); countChars('album_description_'+albumID,'acounter_'+albumID,albumDescriptionMaxLength);});
		}//if
	}//if
}//initAlbumElements(albumIDArr, albumIDArrPointer)