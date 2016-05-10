// JavaScript Document
// deleteelementsinc.js
/*
contains functions:
///////////////////
cacheDeleteElements(elementType,csrf,pageID,imageID,albumID,categoryID)
deleteImages(imageID)
deleteAlbums(albumID)
deleteCategories(categoryID)
sendXMLRequestDeleteElements()
readResponseDeleteElements()
*/

var deleteElementsServerAddress = null;
var deleteElementsCache = new Array();

function cacheDeleteElements(elementType,csrf,pageID,imageID,albumID,categoryID,postID,commentID)
{
	if(elementType)
	{
		imageID = encodeURIComponent(imageID);
		albumID = encodeURIComponent(albumID);
		categoryID = encodeURIComponent(categoryID);
		postID = encodeURIComponent(postID);
		commentID = encodeURIComponent(commentID);	
		csrf = encodeURIComponent(csrf);
		pageID = encodeURIComponent(pageID);
		switch(elementType)
		{
			case "image":
				deleteElementsCache.push("imageid="+imageID+"&albumid="+albumID+"&categoryid="+categoryID+"&csrf="+csrf+"&pageid="+pageID);
				break;
			case "album":
				deleteElementsCache.push("albumid="+albumID+"&categoryid="+categoryID+"&csrf="+csrf+"&pageid="+pageID);
				break;
			case "category":
				deleteElementsCache.push("categoryid="+categoryID+"&csrf="+csrf+"&pageid="+pageID);
				break;
			case "post":
				deleteElementsCache.push("postid="+postID+"&csrf="+csrf+"&pageid="+pageID);
				break;
			case "comment":
				deleteElementsCache.push("commentid="+commentID+"&csrf="+csrf+"&pageid="+pageID);
				break;
			case "usersactionlog":
				deleteElementsCache.push("csrf="+csrf+"&pageid="+pageID);
				break;
			default:
				break;
		}//switch
	}//if
}//cacheDeleteElements(elementType,csrf,pageID,imageID,albumID,categoryID,postID,commentID)
function deleteImages(imageID)
{
	deleteElementsServerAddress = "./jdincfunctions/functionsinc.php?type=9";
	var albumID = parseInt($('aid').innerHTML);
	var categoryID = parseInt($('ncat_'+albumID).innerHTML);
	var postID = null;
	var commentID = null;
	var csrf = $('csrf').innerHTML;
	csrf +='deleteimage';
	var pageID = document.body.className;

	if(xmlHttp){
		cacheDeleteElements('image',csrf,pageID,imageID,albumID,categoryID,postID,commentID);
		sendXMLRequestDeleteElements();
	}//if(xmlHttp)
}//deleteImages(imageID)
function deleteAlbums(albumID)
{
	deleteElementsServerAddress = "./jdincfunctions/functionsinc.php?type=10";
	var imageID = null;
	var categoryID = parseInt($('albumcategoryid_'+albumID).innerHTML);
	var albumID = parseInt($('nalbum_'+albumID).innerHTML);
	var postID = null;
	var commentID = null;
	var csrf = $('csrf').innerHTML;
	csrf +='deletealbum';
	var pageID = document.body.className;

	if(xmlHttp){
		cacheDeleteElements('album',csrf,pageID,imageID,albumID,categoryID,postID,commentID);
		sendXMLRequestDeleteElements();
	}//if(xmlHttp)
}//deleteAlbums(albumID)
function deleteCategories(categoryID)
{
	deleteElementsServerAddress = "./jdincfunctions/functionsinc.php?type=11";
	var imageID = null;
	var albumID = null;
	var postID = null;
	var commentID = null;
	var csrf = $('csrf').innerHTML;
	csrf +='deletecategory';
	var pageID = document.body.className;

	if(xmlHttp){
		cacheDeleteElements('category',csrf,pageID,imageID,albumID,categoryID,postID,commentID);
		sendXMLRequestDeleteElements();
	}//if(xmlHttp)
}//deleteCategories(categoryID)
function deletePosts(postID)
{
	deleteElementsServerAddress = "./jdincfunctions/functionsinc.php?type=15";
	var imageID = null;
	var albumID = null;
	var categoryID = null;
	var commentID = null;
	var csrf = $('csrf').innerHTML;
	csrf +='deletepost';
	var pageID = document.body.className;

	if(xmlHttp){
		cacheDeleteElements('post',csrf,pageID,imageID,albumID,categoryID,postID,commentID);
		sendXMLRequestDeleteElements();
	}
}//deletePosts(postID)
function deleteComments(commentID)
{
	deleteElementsServerAddress = "./jdincfunctions/functionsinc.php?type=30";
	var imageID = null;
	var albumID = null;
	var categoryID = null;
	var postID = null;
	var csrf = $('csrf').innerHTML;
	csrf +='deletecomment';
	var pageID = document.body.className;

	if(xmlHttp){
		cacheDeleteElements('comment',csrf,pageID,imageID,albumID,categoryID,postID,commentID);
		sendXMLRequestDeleteElements();
	}
}
function deleteAllUsersActionLogEntries(usersActionLogType)
{
	if(usersActionLogType=='routine') {deleteElementsServerAddress = "./jdincfunctions/functionsinc.php?type=43";}
	else if(usersActionLogType=='error') {deleteElementsServerAddress = "./jdincfunctions/functionsinc.php?type=44";}
	
	var imageID = null;
	var albumID = null;
	var categoryID = null;
	var postID = null;
	var commentID = null;
	var csrf = $('csrf').innerHTML;
	csrf +='deleteentries';
	var pageID = document.body.className;
	
	if(xmlHttp){
		cacheDeleteElements('usersactionlog',csrf,pageID,imageID,albumID,categoryID,postID,commentID);
		sendXMLRequestDeleteElements();
	}
}//deleteAllUsersActionLogEntries(type)
function sendXMLRequestDeleteElements()
{
	try
	{
		// continue only if the XMLHttpRequest object isn't busy
		// and the cache is not empty
		if ((xmlHttp.readyState == 4 || xmlHttp.readyState == 0) && deleteElementsServerAddress.length > 0)
		{
			// get a new set of parameters from the cache
			var cacheEntry = deleteElementsCache.shift();
			// make a server request to validate the extracted data
			xmlHttp.open("POST", deleteElementsServerAddress, true);
			xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xmlHttp.onreadystatechange = handleRequestStateChangeDeleteElements;
			xmlHttp.send(cacheEntry);		
		}//if
	}//try
	catch (e)
	{
		// display an error when failing to connect to the server
		displayError(e.toString(), "delete elements.");
	}//catch
}//sendXMLRequestDeleteElements()
function readResponseDeleteElements()
{
	var valueSplitArr = new Array;
	// retrieve the server's response
	var response = xmlHttp.responseText;
	// server error?
	if (response.indexOf("ERRNO") >= 0 || response.indexOf("error:") >= 0 || response.length == 0){throw(response.length == 0 ? "Server error." : response);}
	// get response in XML format (assume the response is valid XML)
	responseXml = xmlHttp.responseXML;
	// get the document element
	xmlDoc = responseXml.documentElement;
	
	responsetype = xmlDoc.getElementsByTagName("responsetype")[0].firstChild.data;
	if(responsetype!='routine'){errorReporter(); return 0;}//error
	else{}//OK
	
	value = xmlDoc.getElementsByTagName("value")[0].firstChild.data;
	valueType = xmlDoc.getElementsByTagName("valuetype")[0].firstChild.data;
	pageID = xmlDoc.getElementsByTagName("page")[0].firstChild.data;
	
	switch(valueType)
	{
		case 'image':
			valueSplitArr = value.split('.::.');
			var imageID = parseInt(valueSplitArr[0]);
			var albumID = parseInt(valueSplitArr[1]);
			var categoryID = parseInt(valueSplitArr[2]);
			completeDeleteImage(imageID);
			break;
		case 'album':
			valueSplitArr = value.split('.::.');
			var albumID = parseInt(valueSplitArr[0]);
			var categoryID = parseInt(valueSplitArr[1]);
			completeDeleteAlbum(albumID,categoryID);
			break;
		case 'category':
			var categoryID = parseInt(value);
			completeDeleteCategory(categoryID);
			break;
		case 'post':
			var postID = value;
			completeDeletePost(postID);
			break;
		case 'comment':
			var commentID = parseInt(value);
			completeDeleteComment(commentID);
			break;
		case 'usersactionlog':
			completeDeleteEntries(value);
			break;
		default: break;
	}
}//readResponseDeleteElements()