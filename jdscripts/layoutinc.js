// JavaScript Document

//
var mainNaviIDArr = new Array;
var mainNaviSlideArr = new Array;

window.addEvent('domready', function(){
	var spanArr = $('sidebar').getElementsByTagName('span');
	var counter=0;
	var tempid, mainNaviIDArrPointer;
	
	for (var i=0; i<spanArr.length; i++){tempid=spanArr[i].id.split('_'); if(tempid[0]=='subnavitoggler'){mainNaviIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for (var i=0; i<mainNaviIDArr.length; i++){mainNaviIDArrPointer = i; initMainNaviSliders(mainNaviIDArr[mainNaviIDArrPointer]);}//for

	$('sitebanner').addEvent('click',function(e){e.stop(); window.location = './index.php'; });
});

function initMainNaviSliders(mainNaviID)
{
	mainNaviSlideArr[mainNaviID] = new Fx.Slide('subnavisection_'+mainNaviID);
	mainNaviSlideArr[mainNaviID].hide();
	$('subnavitoggler_'+mainNaviID).addEvent('click',function(e){e.stop(); mainNaviSectionToggler(mainNaviID, e);});
}//initMainNaviSliders(mainNaviID)

function mainNaviSectionToggler(mainNaviID, e)
{	
	e.stop();
	for(var j=0; j<mainNaviIDArr.length; j++)
		{if(mainNaviIDArr[j]==null){continue;} if(mainNaviIDArr[j]!=mainNaviID){ mainNaviSlideArr[mainNaviIDArr[j]].slideOut();} }
	mainNaviSlideArr[mainNaviID].toggle();
}//mainNaviSectionToggler(mainNaviID, e)