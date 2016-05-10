// JavaScript Document

/* GLOBAL JS ARRAYS */
var imageIDArr = new Array;
var inputButtIDArr = new Array;
var divIDArr = new Array;
var currentUrlArr = window.location.href.split('?');
var postCommentMaxLength = 3000;
window.addEvent('domready', function(){ 
	
	var imgArr = $('maincolumn').getElementsByTagName('img');
	var counter=0;
	var tempid, imageIDArrPointer;

	var divArr = $('comments').getElementsByTagName('div');
	var divIDArrPointer;

	for(var i=0; i<imgArr.length; i++){tempid=imgArr[i].id.split('_'); if(tempid[0]=='postimagethumb'){imageIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for(var i=0; i<imageIDArr.length; i++){imageIDArrPointer = i; initImgElements(imageIDArr[imageIDArrPointer]);}//for
	
	counter=0;
	for(var i=0; i<divArr.length; i++){tempid=divArr[i].id.split('_'); if(tempid[0]=='deletecommentbutton'){divIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for(var i=0; i<divIDArr.length; i++){divIDArrPointer = i; initDeleteElements(divIDArr[divIDArrPointer]);}//for	
	
	initCreateCommentElements();
});

function redirectImg(imageID)
{
	var redirectTo = './jdincfunctions/functionsinc.php?imageid='+imageID+'&type=21';
	window.location = redirectTo;
}//redirectImg(imageID)
function initImgElements(imageID)
{
	$('postimagethumb_'+imageID).addEvent('click',function(e){e.stop(); redirectImg(imageID);});
}//initImgElements(imageID)
function initDeleteElements(commentID)
{
	$('deletecommentbutton_'+commentID).addEvent('click',function(e){e.stop(); deleteComment(commentID);});
	$('completedeletecomment_'+commentID).addEvent('click',function(e){e.stop(); deleteComments(commentID);});
	$('dontdeletecomments_'+commentID).addEvent('click',function(e){e.stop(); dontDeleteComment(commentID);});
}//initDeleteElements(postID)
function deleteComment(commentID)
{
	$('commentdelete_'+commentID).style.display = 'block';
	$('deletecommentbutton_'+commentID).style.display = 'none';
	$('commentelement_'+commentID).fade(0.3);
}//deletePost(postID)
function dontDeleteComment(commentID)
{
	$('deletecommentbutton_'+commentID).style.display = 'block';
	$('commentdelete_'+commentID).style.display = 'none';
	$('commentelement_'+commentID).fade(1);
}//dontDeleteCategory(postID)
function completeDeleteComment(commentID)
{
	$('comment_'+commentID).fade(0);
	$('comment_'+commentID).style.display='none';
	$('comment_'+commentID).dispose();
}//completeDeletePost(postID)
//create submit comments form button
function initButtElements(postID)
{
	$('addreply_'+postID).addEvent('click', function(e){e.stop(); validateNsubmitMultipleValues(postID);})
}//
function initCreateCommentElements()
{
	var inputArr = $('maincolumn').getElementsByTagName('input');
	var inputButtIDArrPointer;
	
	//create submit comments form button
	counter=0;
	for(var i=0; i<inputArr.length; i++){tempid=inputArr[i].id.split('_'); if(tempid[0]=='addreply'){inputButtIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for(var i=0; i<inputButtIDArr.length; i++){inputButtIDArrPointer = i; initButtElements(inputButtIDArr[inputButtIDArrPointer]);}//for
	
	if($('name'))
	{
		$('name').addEvent('click',function(e)
			{e.stop(); if($('name').value=='your name (required)'){$('name').value='';} });
		$('email').addEvent('click',function(e)
			{e.stop(); if($('email').value=='your email (will not be published) (required)'){$('email').value='';} });
		$('website').addEvent('click',function(e)
			{e.stop(); if($('website').value=='your website (not required)'){$('website').value='http://www.';} });
		$('reply').addEvent('click',function(e)
			{e.stop(); if($('reply').value=='your reply'){$('reply').value='';} });
		
		$('name').addEvent('focus',function(e)
			{e.stop(); if($('name').value=='your name (required)'){$('name').value='';} });
		$('email').addEvent('focus',function(e)
			{e.stop(); if($('email').value=='your email (will not be published) (required)'){$('email').value='';} });
		$('website').addEvent('focus',function(e)
			{e.stop(); if($('website').value=='your website (not required)'){$('website').value='http://www.';} });
		$('reply').addEvent('focus',function(e)
			{e.stop(); if($('reply').value=='your reply'){$('reply').value='';} });
		
		$('reply').addEvent('keyup',function(e){e.stop(); countChars('reply','ccounter',postCommentMaxLength);});

	}
}//initCreateCommentElements()

function createNewComment(listitem)
{
	var commentanchoritem = new Element('span',{ 'id': 'newcommentanchor', 'html': ''});
	//Add the new comment
	$('commentanchor').appendChild(listitem);
	$('commentanchor').appendChild(commentanchoritem);
	$('commentanchor').id = '';
	$('newcommentanchor').id = 'commentanchor';
		
	$('newcommentfrms').style.display='block';
	
	if($('logoutbutton')!=null)
		{$('name').value = 'james';
		$('email').value = 'james.doe@gmail.com';
		$('website').value = 'http://www.jamesdoe.com';}
	else
		{$('name').value = 'your name (required)';
		$('email').value = 'your email (will not be published) (required)';
		$('website').value = 'your website (not required)';}
	
	$('reply').value = 'your reply';
	$('ccounter').innerHTML = postCommentMaxLength;
}//createNewComment()