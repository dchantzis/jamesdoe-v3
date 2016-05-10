// JavaScript Document

/* GLOBAL JS ARRAYS */
var imageIDArr = new Array;

window.addEvent('domready', function(){ 
	var imgArr = $('albumimagesthumbnails').getElementsByTagName('img');
	var counter=0;
	var tempid, imageIDArrPointer;
	
	for (var i=0; i<imgArr.length; i++){tempid=imgArr[i].id.split('_'); if(tempid[0]=='imagethumb'){imageIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for (var i=0; i<imageIDArr.length; i++){imageIDArrPointer = i; initImgElements(imageIDArr[imageIDArrPointer]);}//for
});

function redirectImg(imageID)
{
	var categoryID = $('category').innerHTML;
	var albumID = $('album').innerHTML;
	var redirectTo = './jdincfunctions/functionsinc.php?categoryid='+categoryID+'&albumid='+albumID+'&imageid='+imageID+'&type=20';
	window.location = redirectTo;
}//redirectImg(imageID)

function initImgElements(imageID)
{
	$('imagethumb_'+imageID).addEvent('click',function(e){e.stop(); redirectImg(imageID);});
}//initImgElements(imageID)