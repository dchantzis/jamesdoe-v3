// JavaScript Document
// validatensubmitinc.js
/*
contains functions:
///////////////////
validateNsubmitLogin(token)
sendXMLRequestValidateNsubmitLogin()
readResponseValidateNsubmitLogin()
errorMessagesLogin(element, fieldID, errorCode)
*/	

// when set to true, display detailed error messages
var showErrors = true;
// initialize the validation requests cache
var validateNsubmitLoginCache = new Array();
var validateNsubmitLoginServerAddress;
var tempStrArr = new Array();

function validateNsubmitLogin(token)
{
	// holds the remote server address (for validations)
	validateNsubmitLoginServerAddress = "./jdincfunctions/functionsinc.php?type=";
	var csrf = null;
	var username = $('mmloginusrnamefield').value;
	var passwordfragment1 = $('mmpasswrdfieldfragment1').value;
	var passwordfragment2 = $('mmpasswrdfieldfragment2').value;
	var passwordfragment3 = $('mmpasswrdfieldfragment3').value;
	
	$('mmloginfrms').style.display='none';
	$('mmloginloader').style.display='block';
	$('mmloginloader').innerHTML='<div><img class="loaderimg" src="./jdlayout/images/loader.gif" /></div>';
	
	validateNsubmitLoginServerAddress +="25";
	csrf = $('csrf').innerHTML;
	csrf += 'login';
	
	// only continue if xmlHttp isn't void
	if (xmlHttp)
	{
		// if we received non-null parameters, we add them to cache in the
		// form of the query string to be sent to the server for validation
		if (token)
		{
			// encode values for safely adding them to an HTTP request query string
			username = encodeURIComponent(username);
			passwordfragment1 = encodeURIComponent(passwordfragment1);
			passwordfragment2 = encodeURIComponent(passwordfragment2);
			passwordfragment3 = encodeURIComponent(passwordfragment3);
			csrf = encodeURIComponent(csrf);
			switch(document.body.className)
			{
				case "mmlogin":
					validateNsubmitLoginCache.push("mmloginusrnamefield="+username
						+"&mmpasswrdfieldfragment1="+passwordfragment1+"&mmpasswrdfieldfragment2="+passwordfragment2+"&mmpasswrdfieldfragment3="
						+passwordfragment3+"&csrf="+csrf+"&pageid=mmlogin");
					break;
				default:
					break;
			}//switch
		}//if
		sendXMLRequestValidateNsubmitLogin();
	}//if
}//validateNsubmitLogin()

function sendXMLRequestValidateNsubmitLogin()
{
	// try to connect to the server
	try
	{
		// continue only if the XMLHttpRequest object isn't busy
		// and the cache is not empty
		if ((xmlHttp.readyState == 4 || xmlHttp.readyState == 0) && validateNsubmitLoginCache.length > 0)
		{
			// get a new set of parameters from the cache
			var cacheEntry = validateNsubmitLoginCache.shift();
			// make a server request to validate the extracted data
			xmlHttp.open("POST", validateNsubmitLoginServerAddress, true);
			xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xmlHttp.onreadystatechange = handleRequestStateChangeValidateNSubmitLogin;
			xmlHttp.send(cacheEntry);		
		}//if
	}//try
	catch (e)
	{
		// display an error when failing to connect to the server
		displayError(e.toString(), "submit");
	}//catch
}//sendXMLRequestValidateNsubmit()

// read server's response
function readResponseValidateNsubmitLogin()
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
	if(result!=0){
		//validation error occured
		$('mmloginloader').innerHTML=''; 
		$('mmloginloader').style.display='none'; 
		$('mmloginfrms').style.display='block';
		errorMessagesLogin(result,$('mmloginmessages'));
	}else if(result==0){
		//all ok
		$('mmloginmessages').dispose();
		$('mmloginloader').innerHTML='';
		$('mmloginfrms').innerHTML='<div class="successfulloginmessege1">authentication complete</div>';
		$('mmloginfrms').innerHTML+='<div class="successfulloginmessege2">welcome master James</div>';
		$('mmloginfrms').innerHTML+='<div class="successfulloginmessege3">(The administrator menu is on the left, dude)</div>'
		$('mmloginfrms').style.display='block';
		
		var logoutButt = new Element('div',{ 'class': 'logout', 'html': '<a href="./jdincfunctions/functionsinc.php?type=26">LogOut</a>'});
		var separator = new Element('div', {'class': 'separator'});
		var adminModeBanner = new Element('div', {'id': 'adminmodebanner', 'html': 'master controls'});
		var adminNavi = new Element('ul',{'class': 'navi', 'id': 'adminnavi', 'html': '<li title="Edit posts."><a href="./mmeditposts.php">posts</a></li><li title="Edit images."><a href="./mmeditimages.php">images</a></li><li title="Edit personal info."><a href="./mmeditinfo.php">info</a></li><li title="Edit site settings."><a href="./mmeditsettings.php">settings</a></li><li title="Edit site welcome page"><a href="./mmeditwelcomepage.php">welcome page</a></li><li title="View site action log."><li class="subnavitogglers" title="View site action log"><span id="subnavitoggler_0">action log</span><ul class="subnavi" id="subnavisection_0"><li><a href="./mmeditualcomments.php" title="View posts comments">comments</a></li><li><a href="./mmeditualroutine.php" title="View routine system actions">routine actions</a></li><li><a href="./mmeditualerrors.php" title="View system errors">errors</a></li></ul></li>'});
		var sideFooter = new Element('div', {'id': 'sidefooter'});

		$('adminsidebarplaceholder').appendChild(logoutButt);
		$('adminsidebarplaceholder').appendChild(separator);
		$('adminsidebarplaceholder').appendChild(adminModeBanner);
		$('adminsidebarplaceholder').appendChild(adminNavi);
		$('adminsidebarplaceholder').appendChild(sideFooter);
		
		mainNaviIDArr[mainNaviIDArr.length]=0;
		initMainNaviSliders(0);
	}//
}//readResponseValidateNsubmitLogin

//errorMessagesLogin()
function errorMessagesLogin(errorCode,element)
{
	var $errVals = new Array();
	$errVals[101] = "Empty login fields.";
	$errVals[101] = "Invalid Login Credentials.";
	$errVals[102] = $errVals[101];
	$errVals[103] = $errVals[101];
	element.innerHTML = 'Error: '+$errVals[errorCode];
}//errorMessages(fieldID, errorCode)