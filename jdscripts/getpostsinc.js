// JavaScript Document

var getPostElementsCache = new Array();
var getPostElementsServerAddress;

function getPostElements(yearID,monthID)
{
	// holds the remote server address (for validations)
	getPostElementsServerAddress = "./jdincfunctions/functionsinc.php?type=22";
	var csrf = null;
	
	csrf = $('csrf').innerHTML;
	csrf += 'getpostelements';
	
	// only continue if xmlHttp isn't void
	if (xmlHttp)
	{
		// if we received non-null parameters, we add them to cache in the
		// form of the query string to be sent to the server for validation
		if (yearID)
		{
			switch(document.body.className)
			{
				case "postsarchive":
					// encode values for safely adding them to an HTTP request query string
					yearID = encodeURIComponent(yearID);
					monthID = encodeURIComponent(monthID);
					csrf = encodeURIComponent(csrf);
					getPostElementsCache.push("yearid="+yearID+"&monthid="+monthID+"&csrf="+csrf+"&pageid=postsarchive");
					break;
				default:
					break;
			}//switch
		}//if
		sendXMLRequestGetPostElements();
	}//if
}//getImage(imageID,type)

function sendXMLRequestGetPostElements()
{
	// try to connect to the server
	try
	{
		// continue only if the XMLHttpRequest object isn't busy
		// and the cache is not empty
		if ((xmlHttp.readyState == 4 || xmlHttp.readyState == 0) && getPostElementsCache.length > 0)
		{
			// get a new set of parameters from the cache
			var cacheEntry = getPostElementsCache.shift();
			// make a server request to validate the extracted data
			xmlHttp.open("POST", getPostElementsServerAddress, true);
			xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xmlHttp.onreadystatechange = handleRequestStateChangeGetPostElements;
			xmlHttp.send(cacheEntry);		
		}//if
	}//try
	catch (e)
	{
		// display an error when failing to connect to the server
		displayError(e.toString(), "getpostelement");
	}//catch
}//sendXMLRequestGetPostElements()

function readResponseGetPostElements()
{
	// retrieve the server's response
	var response = xmlHttp.responseText;
	var monthsArr = new Array();
	var daysArr = new Array();
	var tempMonthID, tempMonthName;
	var tempDayElementID, tempDayDBID, tempDayNumber, tempDayHeadline;
	var newPostMonths = "";
	var newPostHeadlines = "";
	var selectedPostYear = $('syearid').innerHTML;
	// server error?
	if (response.indexOf("ERRNO") >= 0 || response.indexOf("error:") >= 0 || response.length == 0){throw(response.length == 0 ? "Server error." : response);}
	// get response in XML format (assume the response is valid XML)
	responseXml = xmlHttp.responseXML;
	// get the document element
	xmlDoc = responseXml.documentElement;
	
	type = xmlDoc.getElementsByTagName("type")[0].firstChild.data;
	responsetype = xmlDoc.getElementsByTagName("responsetype")[0].firstChild.data;
	if(responsetype!='routine'){errorReporter(); return 0;}//error
	else{}//OK

	if(type=='months')
	{
		monthsArr = xmlToArray(xmlDoc.getElementsByTagName("postmonth"));//get the length
		for (var i=0; i<monthsArr.length; i++)
		{
			tempMonthID = xmlDoc.getElementsByTagName("postmonth")[i].getAttribute('id');
			tempMonthName = xmlDoc.getElementsByTagName("postmonth")[i].firstChild.data;
			newPostMonths += "<li id='postmonth_"+tempMonthID+"'>"+tempMonthName+" "+selectedPostYear+"</li>";
		}
		var newElement = new Element('ul', {
			'class': 'postsarchivemonths',
			'id': 'postsarchivemonth',
			'html': ''+newPostMonths+''});
		$('postsarchivemonth').dispose();
		$('postsarchivemonthsplaceholder').appendChild(newElement);
		createPostMonthsActionListeners();
		$('postsarchiveday').innerHTML='';
	}
	else if(type=='headlines')
	{
		daysArr = xmlToArray(xmlDoc.getElementsByTagName("postday"));//get the length
		for (var i=0; i<daysArr.length; i++)
		{
			tempDayElementID = xmlDoc.getElementsByTagName("postday")[i].getAttribute('id');
			tempDayDBID = xmlDoc.getElementsByTagName("postday")[i].getAttribute('class');
			tempDayNumber = xmlDoc.getElementsByTagName("postday")[i].getAttribute('title');
			tempDayHeadline = xmlDoc.getElementsByTagName("postday")[i].firstChild.data;
			newPostHeadlines += "<li id='"+tempDayElementID+"'>"
				+"<span class='days'>"+tempDayNumber+"</span>"
				+" <span class='postheadlines'>"
					+"<a href='./posts.php?postid="+tempDayDBID+"' id='postday_"+tempDayElementID+"' title='Click to read post'>"
					+tempDayHeadline
					+"</a>"
				+"</span>"
			+"</li>";
		}
		var newElement = new Element('ul', {
			'class': 'postsarchivedays',
			'id': 'postsarchiveday',
			'html': ''+newPostHeadlines+''});
		$('postsarchiveday').dispose();
		$('postsarchivedaysplaceholder').appendChild(newElement);
	}
	sendXMLRequestGetPostElements();
}//readResponseGetPostElements()