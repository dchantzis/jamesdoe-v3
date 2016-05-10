// JavaScript Document

/* GLOBAL JS ARRAYS */
var albumIDArr = new Array;

window.addEvent('domready', function(){ 
	var liArr = $('albums').getElementsByTagName('li');
	var counter=0;
	var tempid, albumIDArrPointer;
	
	for (var i=0; i<liArr.length; i++){tempid=liArr[i].id.split('_'); if(tempid[0]=='albumname'){albumIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for (var i=0; i<albumIDArr.length; i++){albumIDArrPointer = i; initAlbumElements(albumIDArr[albumIDArrPointer]);}//for
});

function redirectAlbum(albumID)
{
	var categoryID = $('category').innerHTML;
	var redirectTo = './albums.php?categoryid='+categoryID+'&albumid='+albumID;
	window.location = redirectTo;
}//redirectAlbum(albumID)

function initAlbumElements(albumID)
{
	$('albumname_'+albumID).addEvent('click',function(e){e.stop(); redirectAlbum(albumID);});
}//initAlbumElements(albumID)