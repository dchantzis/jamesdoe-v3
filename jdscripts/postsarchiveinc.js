// JavaScript Document

/* GLOBAL JS ARRAYS */
var yearIDArr = new Array;
var monthIDArr = new Array;

window.addEvent('domready', function(){
									 
	//GET ALL THE POST YEARS AND CREATE ACTION LISTENERS
	var yearliArr = $('postsarchiveyear').getElementsByTagName('li');
	var counter = 0;
	var tempid, yearIDArrPointer;
	for(var i=0; i<yearliArr.length; i++){tempid=yearliArr[i].id.split('_'); if(tempid[0]=='postyear'){yearIDArr[counter]=tempid[1]; counter++;}}
	for(var i=0; i<yearIDArr.length; i++){yearIDArrPointer=i; initPostYearElements(yearIDArr[yearIDArrPointer]);}
	
	//GET ALL THE POST MONTHS AND CREATE ACTION LISTENERS
	createPostMonthsActionListeners();
});

function createPostMonthsActionListeners()
{
	var monthliArr = $('postsarchivemonth').getElementsByTagName('li');
	var counter = 0;
	var tempid, monthIDArrPointer;
	monthIDArr.length=0;
	for(var i=0; i<monthliArr.length; i++){tempid=monthliArr[i].id.split('_'); if(tempid[0]=='postmonth'){ monthIDArr[counter]=tempid[1]; counter++;}}
	for(var i=0; i<monthIDArr.length; i++){monthIDArrPointer=i; initPostMonthElements(monthIDArr[monthIDArrPointer]);}
}//createPostMonthsActionListeners()

function loadPostMonths(yearID){var monthID = ''; $('syearid').innerHTML=yearID; getPostElements(yearID,monthID);}//loadPostMonths(yearID)
function loadPostHeadlines(yearID,monthID){var yearID = $('syearid').innerHTML; getPostElements(yearID,monthID);}//loadPostHeadlines(yearID,monthID)

function initPostYearElements(yearID)
{
	$('postyear_'+yearID).addEvent('click',function(e){e.stop(); loadPostMonths(yearID);});
}//initPostYearElements(yearID)
function initPostMonthElements(monthID)
{
	$('postmonth_'+monthID).addEvent('click',function(e){e.stop(); yearID = $('syearid').innerHTML; loadPostHeadlines(yearID,monthID);});
}//initPostMonthElements(monthID)