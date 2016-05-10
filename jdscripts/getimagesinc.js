// JavaScript Document

var getImageCache = new Array();
var getImageServerAddress;

function getImage(imageID,type)
{
	// holds the remote server address (for validations)
	getImageServerAddress = "./jdincfunctions/functionsinc.php?type=";
	var categoryID = null;
	var albumID = null;
	var tagID = null;
	var csrf = null;
	
	categoryID = $('category').innerHTML;
	albumID = $('album').innerHTML;
	csrf = $('csrf').innerHTML;
	
	$('loadnextimage').className='';
	
	switch(document.body.className)
	{
		case "images":
			csrf += 'getimage';
			if(type=='previous'){getImageServerAddress+="16";}
			else if(type=='next'){getImageServerAddress+="17";}
		break;
		case "taggedimages":
			csrf += 'gettaggedimage';
			tagID = $('tag').innerHTML;
			if(type=='previous'){getImageServerAddress+="35";}
			else if(type=='next'){getImageServerAddress+="36";}
		break;
		default:
		break;
	}
	// only continue if xmlHttp isn't void
	if (xmlHttp)
	{
		// if we received non-null parameters, we add them to cache in the
		// form of the query string to be sent to the server for validation
		if (imageID)
		{
			switch(document.body.className)
			{
				case "images":
					// encode values for safely adding them to an HTTP request query string
					imageID = encodeURIComponent(imageID);
					albumID = encodeURIComponent(albumID);
					categoryID = encodeURIComponent(categoryID);
					csrf = encodeURIComponent(csrf);
					getImageCache.push("categoryid="+categoryID+"&albumid="+albumID+"&imageid="+imageID+"&csrf="+csrf+"&pageid=images");
					break;
				case "taggedimages":
					// encode values for safely adding them to an HTTP request query string
					imageID = encodeURIComponent(imageID);
					albumID = encodeURIComponent(albumID);
					categoryID = encodeURIComponent(categoryID);
					tagID = encodeURIComponent(tagID);
					csrf = encodeURIComponent(csrf);
					getImageCache.push("categoryid="+categoryID+"&albumid="+albumID+"&imageid="+imageID+"&tagid="+tagID+"&csrf="+csrf+"&pageid=taggedimages");
					break;
				default:
					break;
			}//switch
		}//if
		sendXMLRequestGetImage();
	}//if
}//getImage(imageID,type)

function sendXMLRequestGetImage()
{
	// try to connect to the server
	try
	{
		// continue only if the XMLHttpRequest object isn't busy
		// and the cache is not empty
		if ((xmlHttp.readyState == 4 || xmlHttp.readyState == 0) && getImageCache.length > 0)
		{
			// get a new set of parameters from the cache
			var cacheEntry = getImageCache.shift();
			// make a server request to validate the extracted data
			xmlHttp.open("POST", getImageServerAddress, true);
			xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xmlHttp.onreadystatechange = handleRequestStateChangeGetImage;
			xmlHttp.send(cacheEntry);		
		}//if
	}//try
	catch (e)
	{
		// display an error when failing to connect to the server
		displayError(e.toString(), "getimage");
	}//catch
}//sendXMLRequestGetImage()

function readResponseGetImage()
{
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
	
	result = xmlDoc.getElementsByTagName("result")[0].firstChild.data;
	imageID = xmlDoc.getElementsByTagName("imageid")[0].firstChild.data;
	imageName = xmlDoc.getElementsByTagName("imagename")[0].firstChild.data;
	imageDescription = xmlDoc.getElementsByTagName("imagedescription")[0].firstChild.data;
	imageTags = xmlDoc.getElementsByTagName("imagetags")[0].firstChild.data;
	imageFileurl = xmlDoc.getElementsByTagName("imagefileurl")[0].firstChild.data;
	imageSubmitionTimestamp = xmlDoc.getElementsByTagName("imagesubmitiontimestamp")[0].firstChild.data;	
	
	imageAlbumID = xmlDoc.getElementsByTagName("imagealbumid")[0].firstChild.data;
	imageCategoryID = xmlDoc.getElementsByTagName("imagecategoryid")[0].firstChild.data;
	imageAlbumName = xmlDoc.getElementsByTagName("imagealbumname")[0].firstChild.data;
	imageCategoryName = xmlDoc.getElementsByTagName("imagecategoryname")[0].firstChild.data;	
	
	imagePosition = xmlDoc.getElementsByTagName("imageposition")[0].firstChild.data;
	albumImagesCount = xmlDoc.getElementsByTagName("albumimagescount")[0].firstChild.data;

	if(imageName=='null'){imageName='unnamed';}
	if(imageDescription=='null'){imageDescription='';}
	if(imageTags=='null'){imageTags='';}
	if(imageSubmitionTimestamp=='null'){imageSubmitionTimestamp='';}
	if(imageFileurl=='null'){imageFileurl='';}
	if(imagePosition=='null'){imagePosition='';}
	if(albumImagesCount=='null'){albumImagesCount='';}
	if(imageAlbumID=='null'){imageAlbumID='';}
	if(imageCategoryID=='null'){imageCategoryID='';}
	if(imageAlbumName=='null'){imageAlbumName='';}
	if(imageCategoryName=='null'){imageCategoryName=='null';}

	if(imageTags!='')
	{
		tagsArr = xmlToArray(xmlDoc.getElementsByTagName("tag"));
		if(tagsArr.length==0){imageTags='-'}
		else
		{
			imageTags='<ul class="tags">';
			for(var i=0; i<tagsArr.length; i++)
			{
				tempArr = tagsArr[i].split('.::.');
				imageTags+='<li>'+'<a href="./tags.php?tagid='+tempArr[0]+'">'+tempArr[1]+'</a>,'+'</li> ';
			}
			imageTags+='</ul>';
		}
	}

	$('image').innerHTML = imageID;
	$('imagename').innerHTML = imageName;
	$('secondarynaviimagename').innerHTML = imageName;
	$('imagedescription').innerHTML = imageDescription;
	$('imagetags').innerHTML = imageTags;
	$('imagesubmitiontimestamp').innerHTML = imageSubmitionTimestamp;
	//$('imagefileurl').src = './jdimages/fullresolution/'+imageFileurl;
	$('imagefileurl').dispose();
	var imageFile = new Element('img', {
			'id': 'imagefileurl',
			'src': './jdimages/fullresolution/'+imageFileurl+'',
			'width': 681});
	$('imagefileurlli').appendChild(imageFile);
	$('imageposition').innerHTML = imagePosition;
	$('albumimagescount').innerHTML = albumImagesCount;
	$('loadnextimage').className='hidden';

	if(document.body.className=='taggedimages')
		{$('imagealbum').innerHTML = "<a href='./albums.php?categoryid="+imageCategoryID+"&albumid="+imageAlbumID+"' title='Album Name'>"+imageAlbumName+"</a>";}
	$('imagefileurl').addEvent('click',function(e){e.stop(); loadImageFullSize($('imagefileurl').src); });

	sendXMLRequestGetImage();
}//readResponseGetImage()