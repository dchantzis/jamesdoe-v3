// JavaScript Document

/* GLOBAL JS ARRAYS */
var headlineIDArr = new Array;
var imageIDArr = new Array;
var inputButtIDArr = new Array;
var liIDArr = new Array;
var divIDArr = new Array;
var spanArr = new Array;
var currentUrlArr = window.location.href.split('?');
var postCommentMaxLength = 3000;
window.addEvent('domready', function(){ 
	
	var imgArr = $('maincolumn').getElementsByTagName('img');
	var counter=0;
	var tempid, imageIDArrPointer;

	var liArr = $('maincolumn').getElementsByTagName('li');
	var liIDArrPointer;
	
	var spanArr = $('maincolumn').getElementsByTagName('span');
	var headlineIDArrPointer;
	
	//create headline button
	for(var i=0; i<spanArr.length; i++){tempid=spanArr[i].id.split('_'); if(tempid[0]=='displaypost'){headlineIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for(var i=0; i<headlineIDArr.length; i++){headlineIDArrPointer = i; initSpanElements(headlineIDArr[headlineIDArrPointer]);}//for
	
	//create image thumbs button
	counter=0;
	for(var i=0; i<imgArr.length; i++){tempid=imgArr[i].id.split('_'); if(tempid[0]=='postimagethumb'){imageIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for(var i=0; i<imageIDArr.length; i++){imageIDArrPointer = i; initImgElements(imageIDArr[imageIDArrPointer]);}//for

	//create display comments button
	counter=0;
	for(var i=0; i<liArr.length; i++){tempid=liArr[i].id.split('_'); if(tempid[0]=='displaycomment'){liIDArr[counter] = parseInt(tempid[1]); counter++;}}
	for(var i=0; i<liIDArr.length; i++){liIDArrPointer = i; initLiElements(liIDArr[liIDArrPointer]);}//for
	
	if($('logoutbutton')!=null)
	{
		if($('comments')!=null)
		{
		var divArr = $('comments').getElementsByTagName('div');
		var divIDArrPointer;
		counter=0;
		for(var i=0; i<divArr.length; i++){tempid=divArr[i].id.split('_'); if(tempid[0]=='deletecommentbutton'){divIDArr[counter] = parseInt(tempid[1]); counter++;}}
		for(var i=0; i<divIDArr.length; i++){divIDArrPointer = i; initDeleteElements(divIDArr[divIDArrPointer]);}//for	
		}
	}
	
	initCreateCommentElements();
});

function redirectImg(imageID)
{
	var redirectTo = './jdincfunctions/functionsinc.php?imageid='+imageID+'&type=21';
	window.location = redirectTo;
}//redirectImg(imageID)
function redirectLi(postID)
{
	var redirectTo = './jdincfunctions/functionsinc.php?postid='+postID+'&type=27';
	window.location = redirectTo;
}
function redirectSpan(postID)
{
	var redirectTo = './posts.php?postid='+postID;
	window.location = redirectTo;
}
function initSpanElements(postID)
{
	$('displaypost_'+postID).addEvent('click',function(e){e.stop(); redirectSpan(postID);});
}
function initImgElements(imageID)
{
	$('postimagethumb_'+imageID).addEvent('click',function(e){e.stop(); redirectImg(imageID);});
}//initImgElements(imageID)
//create submit comments form button
function initButtElements(postID)
{
	$('addreply_'+postID).addEvent('click', function(e){e.stop(); validateNsubmitMultipleValues(postID);})
}//
//create display comments button
function initLiElements(postID)
{
	if(currentUrlArr[1]=='mostrecent')
		{$('displaycomment_'+postID).addEvent('click', function(e){e.stop(); redirectLi(postID);})}
	else
		{$('displaycomment_'+postID).addEvent('click', function(e){e.stop(); redirectLi(postID);})}//could create an ajax function that displays the posts and call it here.
}//
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
	if($('commentcounter')!=null)
		{$('commentcounter').innerHTML=parseInt($('commentcounter').innerHTML)-1;
		if(parseInt($('commentcounter').innerHTML)==1){$('commentscounterphrase').innerHTML='comment'}
		else{$('commentscounterphrase').innerHTML='comments'}}
				
	$('comment_'+commentID).fade(0);
	$('comment_'+commentID).style.display='none';
	$('comment_'+commentID).dispose();
}//completeDeletePost(postID)
//create submit comments form button
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



