// JavaScript Document
//searchnsuggestinc.js
/*
contains the following functions:
*/

var searchNsuggestServerAddress = null;
var searchNsuggestCache = new Array();

function searchNsuggest(elementType,fieldValue,csrf,pageID,imageID,albumID,categoryID)
{
	if(elementType)
	{
		fieldValue = encodeURIComponent(fieldValue);
		imageID = encodeURIComponent(imageID);
		albumID = encodeURIComponent(albumID);
		categoryID = encodeURIComponent(categoryID);
		csrf = encodeURIComponent(csrf);
		pageID = encodeURIComponent(pageID);
		switch(elementType)
		{
			case "imagetag":
				searchNsuggestCache.push("imagetag="+fieldValue+"&imageid="+imageID+"&albumid="+albumID+"&categoryid="+categoryID+"&csrf="+csrf+"&pageid="+pageID);
				break;
			default:
				break;
		}//switch
	}//if
}//searchNsuggest(elementType,fieldValue,csrf,pageID,imageID,albumID,categoryID)
function searchNsuggestImageTags(imageID,fieldValue)
{
	searchNsuggestServerAddress = "./jdincfunctions/functionsinc.php?type=12";
	var albumID = parseInt($('aid').innerHTML);
	var categoryID = parseInt($('ncat_'+albumID).innerHTML);
	var csrf = $('csrf').innerHTML;
	csrf +='searchnsuggestimagetags';
	var pageID = document.body.className;

	if(xmlHttp){ 
		searchNsuggest('imagetag',fieldValue,csrf,pageID,imageID,albumID,categoryID);
		sendXMLRequestSearchNSuggest();
	}//if(xmlHttp)
}//searchNsuggestImageTags()
function sendXMLRequestSearchNSuggest()
{
	try
	{
		// continue only if the XMLHttpRequest object isn't busy
		// and the cache is not empty
		if ((xmlHttp.readyState == 4 || xmlHttp.readyState == 0) && searchNsuggestServerAddress.length > 0)
		{
			// get a new set of parameters from the cache
			var cacheEntry = searchNsuggestCache.shift();
			// make a server request to validate the extracted data
			xmlHttp.open("POST", searchNsuggestServerAddress, true);
			xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xmlHttp.onreadystatechange = handleRequestStateChangeSearchNSuggest;
			xmlHttp.send(cacheEntry);		
		}//if
	}//try
	catch (e)
	{
		// display an error when failing to connect to the server
		displayError(e.toString(), "search and suggest.");
	}//catch
}//sendXMLRequestSearchNSuggest()
function readResponseSearchNSuggest()
{
	// retrieve the server's response
	var response = xmlHttp.responseText;
	var suggestionsArr = new Array();
	// server error?
	if (response.indexOf("ERRNO") >= 0 || response.indexOf("error:") >= 0 || response.length == 0){throw(response.length == 0 ? "Server error." : response);}

	// get response in XML format (assume the response is valid XML)
	responseXml = xmlHttp.responseXML;
	// get the document element
	xmlDoc = responseXml.documentElement;
	
	imageID = xmlDoc.getElementsByTagName("fieldid")[0].firstChild.data;
	responsetype = xmlDoc.getElementsByTagName("responsetype")[0].firstChild.data;
	if(responsetype!='routine'){errorReporter(); return 0;}//error
	else{}//OK
	
	if(xmlDoc.getElementsByTagName("fieldid")[0].firstChild.data != ' ')
	{
		suggestionsArr = xmlToArray(xmlDoc.getElementsByTagName("suggestion"));
	}
	displayImageTagSuggestions(imageID,suggestionsArr);
}//readResponseSearchNSuggest()