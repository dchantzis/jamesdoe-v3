// JavaScript Document
// validatensubmitinc.js
/*
contains functions:
///////////////////
validateNsubmit(fieldID)
sendXMLRequestValidateNsubmit(type)
readResponseValidateNsubmit()
errorMessages(element, fieldID, errorCode)
*/	

// when set to true, display detailed error messages
var showErrors = true;
// initialize the validation requests cache
var validateNsubmitCache = new Array();
var validateNsubmitServerAddress;
var tempStrArr = new Array();

// the function handles the validation for any form field
function validateNsubmit(fieldID)
{
	// holds the remote server address (for validations)
	validateNsubmitServerAddress = "./jdincfunctions/functionsinc.php?type=";
	var ncategoryID = null;
	var nalbumID = null;
	var nimageID = null;
	var npostID = null;
	var profileID = null;
	var nvisibilityStatus = null;
	var csrf = null;

	tempStrArr = fieldID.split('_');

	if((tempStrArr[1]!='coverid')&&(tempStrArr[1]!='imagesorder')&&(tempStrArr[1]!='visibility')&&(tempStrArr[1]!='checkbox')){
		frmID = $(tempStrArr[0]+tempStrArr[1]+'frm_'+tempStrArr[2]);
		loaderID = $(tempStrArr[0]+tempStrArr[1]+'loader_'+tempStrArr[2]);
		frmID.style.display='none';
		loaderID.style.display='block';
		loaderID.innerHTML='<div>Loading... <img class="loaderimg" src="./jdlayout/images/loader.gif" /></div>';
	}//if
	
	switch(document.body.className)
	{
		case "mmeditimages":
			if(tempStrArr[0]=='cat')
			{
				switchvar = $('n'+tempStrArr[0]+'_'+tempStrArr[2]).innerHTML;
				if(switchvar.substr(0,3)!='999'){validateNsubmitServerAddress +="3";}
				else if(switchvar.substr(0,3)=='999'){validateNsubmitServerAddress +="4";}
				ncategoryID = parseInt($('n'+tempStrArr[0]+'_'+tempStrArr[2]).innerHTML);
				csrf = $('csrf').innerHTML;
				csrf += 'editcategory';
			}//if
			else if(tempStrArr[0]=='album')
			{
				switchvar = $('n'+tempStrArr[0]+'_'+tempStrArr[2]).innerHTML;
				if(switchvar.substr(0,3)!='888'){validateNsubmitServerAddress +="5";}
				else if(switchvar.substr(0,3)=='888'){validateNsubmitServerAddress +="6";}			
				ncategoryID = parseInt($('albumcategoryid_'+tempStrArr[2]).innerHTML);
				nalbumID = parseInt($('nalbum_'+tempStrArr[2]).innerHTML);
				csrf = $('csrf').innerHTML;
				csrf += 'editalbum';
			}//else
			break;
		case "mmeditalbums":
			if(tempStrArr[0]=='album')
			{
				validateNsubmitServerAddress +="5";
				ncategoryID = parseInt($('ncat_'+tempStrArr[2]).innerHTML);
				nalbumID = parseInt(tempStrArr[2]);
				csrf = $('csrf').innerHTML;
				csrf += 'editalbum';
			}//if
			else if(tempStrArr[0]=='image')
			{
				validateNsubmitServerAddress +="7";
				nimageID = parseInt(tempStrArr[2]);
				nalbumID = parseInt($('aid').innerHTML);
				ncategoryID = parseInt($('ncat_'+nalbumID).innerHTML);
				csrf = $('csrf').innerHTML;
				csrf += 'editimage';
			}//if
			break;
		case "mmeditposts":
			if(tempStrArr[0]=='post')
			{
				switchvar = $('n'+tempStrArr[0]+'_'+tempStrArr[2]).innerHTML;
				if(switchvar.substr(0,3)!='55x'){validateNsubmitServerAddress +="13";}
				else if(switchvar.substr(0,3)=='55x'){validateNsubmitServerAddress +="14";}
				npostID = $('n'+tempStrArr[0]+'_'+tempStrArr[2]).innerHTML;
				csrf = $('csrf').innerHTML;
				csrf += 'editpost';
			}//if
			else if(tempStrArr[0]=='image'){}//if
			break;
		case "mmeditsettings":
			if(fieldID == 'album_visibility')
			{
				validateNsubmitServerAddress +="31";
				inputValue = ($(fieldID).innerHTML).split('::');
				ncategoryID = parseInt(inputValue[0]);
				nalbumID = parseInt(inputValue[1]);
				nvisibilityStatus = inputValue[2];
				csrf = $('csrf').innerHTML;
				csrf += 'editalbumvisibility';
			}//if
			else if(tempStrArr[1] == 'checkbox')
			{
				validateNsubmitServerAddress +="32";
				fieldID = "toggle"+tempStrArr[0]+""+tempStrArr[1];
				profileID = parseInt(tempStrArr[2]);
				//inputValue ie the value of the fieldID is filled a little further down
				csrf = $('csrf').innerHTML;
				csrf += 'togglecheckbox';
			}
			else if(tempStrArr[0]== 'profile')
			{
				validateNsubmitServerAddress +="33";
				profileID = parseInt(tempStrArr[2]);
				csrf = $('csrf').innerHTML;
				csrf += 'editprofile';
			}
			break;
		case "mmeditwelcomepage":
			validateNsubmitServerAddress +="33";
			profileID = parseInt(tempStrArr[2]);
			csrf = $('csrf').innerHTML;
			csrf += 'editprofile';
			break;
		case "mmeditinfo":
			validateNsubmitServerAddress +="33";
			profileID = parseInt(tempStrArr[2]);
			csrf = $('csrf').innerHTML;
			csrf += 'editprofile';
			break;
		default:
			break;
	}//switch

	// only continue if xmlHttp isn't void
	if (xmlHttp)
	{
		// if we received non-null parameters, we add them to cache in the
		// form of the query string to be sent to the server for validation
		if (fieldID)
		{
			inputValue = $(fieldID).value;
			if(tempStrArr[1]=='coverid'){inputValue = parseInt($(fieldID).innerHTML);}
			if(tempStrArr[1]=='imagesorder'){inputValue = $(fieldID).innerHTML;}
			// encode values for safely adding them to an HTTP request query string
			inputValue = encodeURIComponent(inputValue);
			fieldID = encodeURIComponent(fieldID);
			switch(document.body.className)
			{
				case "mmeditimages":
					ncategoryID = encodeURIComponent(ncategoryID);
					csrf = encodeURIComponent(csrf);
					if(nalbumID !=null){nalbumID=encodeURIComponent(nalbumID); validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&ncategoryID="+ncategoryID+"&nalbumID="+nalbumID+"&csrf="+csrf+"&pageid=mmeditimages");}
					else{validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&ncategoryID="+ncategoryID+"&csrf="+csrf+"&pageid=mmeditimages");}
					break;
				case "mmeditalbums":
					ncategoryID = encodeURIComponent(ncategoryID);
					nalbumID = encodeURIComponent(nalbumID);
					csrf = encodeURIComponent(csrf);
					if(nimageID!=null){nimageID = encodeURIComponent(nimageID); validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&nimageID="+nimageID+"&ncategoryID="+ncategoryID+"&nalbumID="+nalbumID+"&csrf="+csrf+"&pageid=mmeditalbums");}
					else{validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&ncategoryID="+ncategoryID+"&nalbumID="+nalbumID+"&csrf="+csrf+"&pageid=mmeditalbums");}
					break;
				case "mmeditposts":
					npostID = encodeURIComponent(npostID);
					csrf = encodeURIComponent(csrf);
					if(nimageID!=null){nimageID = encodeURIComponent(nimageID);}
					else{validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&npostID="+npostID+"&csrf="+csrf+"&pageid=mmeditposts");}
					break;
				case "mmeditsettings":
					inputValue = encodeURIComponent($(fieldID).innerHTML);
					csrf = encodeURIComponent(csrf);
					if(nvisibilityStatus!=null){
						ncategoryID = encodeURIComponent(ncategoryID); nalbumID = encodeURIComponent(nalbumID); nvisibilityStatus = encodeURIComponent(nvisibilityStatus);
						validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&ncategoryID="+ncategoryID+"&nalbumID="+nalbumID+"&nvisibilityStatus="+nvisibilityStatus+"&csrf="+csrf+"&pageid=mmeditsettings");
					}
					else if(tempStrArr[0]=='profile'){
						inputValue = encodeURIComponent($(fieldID).value);
						profileID = encodeURIComponent(profileID);
						validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&profileID="+profileID+"&csrf="+csrf+"&pageid=mmeditsettings");
					}
					else if(tempStrArr[1]=='checkbox'){
						inputValue = encodeURIComponent($(fieldID).title);
						fieldID = encodeURIComponent("toggle_"+tempStrArr[0]+"_"+tempStrArr[1]);
						profileID = encodeURIComponent(profileID);
						validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&profileID="+profileID+"&csrf="+csrf+"&pageid=mmeditsettings");
					}
					break;
				case "mmeditwelcomepage":
					profileID = encodeURIComponent(profileID);
					csrf = encodeURIComponent(csrf);
					validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&profileID="+profileID+"&csrf="+csrf+"&pageid=mmeditwelcomepage");
					break;
				case "mmeditinfo":
					profileID = encodeURIComponent(profileID);
					csrf = encodeURIComponent(csrf);
					validateNsubmitCache.push("inputValue="+inputValue+"&fieldID="+fieldID+"&profileID="+profileID+"&csrf="+csrf+"&pageid=mmeditinfo");
					break;
				default:
					break;
			}//switch
		}//if
		sendXMLRequestValidateNsubmit('singleValue');
	}//if
}//validateNsubmit

function validateNsubmitMultipleValues(postID)
{
	// holds the remote server address (for validations)
	validateNsubmitServerAddress = "./jdincfunctions/functionsinc.php?type=";
	var csrf = null;
	var documentClassName = document.body.className;
	var conditionalFlag = false;
	
	if(documentClassName=='posts'||documentClassName=='mmeditcomments')
	{
		var nname = null;
		var nemail = null;
		var nwebsite = null;
		var nreply = null;
	
		frmID = $('newcommentfrms');
		loaderID = $('newcommentloader');
		message = $('newcommentmessagesfailed');
	}
	else if(documentClassName=='info')
	{
		var sendername = null;
		var senderemail = null;
		var senderregarding = null;
		var sendermessage = null;
		var sendercc = null;

		frmID = $('contactfrm');
		loaderID = $('senderloader');
		message = $('senderfailed');
	}
	
	frmID.style.display='none';
	loaderID.style.display='block';
	loaderID.innerHTML='<div id="loaderdiv">Loading... <img class="loaderimg" src="./jdlayout/images/loader.gif" /></div>';

	switch(document.body.className)
	{
		case "posts":
			validateNsubmitServerAddress +="28";
			postID = parseInt(postID);
			nname = $('name').value;
			nemail = $('email').value;
			nwebsite = $('website').value;
			nreply = $('reply').value;
			csrf = $('csrf').innerHTML;
			csrf += 'submitcomment';
			
			//alert('nname: '+nname+' <br/> '+'nreply: '+nreply);
		break;
		case "mmeditcomments":
			validateNsubmitServerAddress +="28";
			postID = parseInt(postID);
			nname = $('name').value;
			nemail = $('email').value;
			nwebsite = $('website').value;
			nreply = $('reply').value;
			csrf = $('csrf').innerHTML;
			csrf += 'submitcomment';
		break;
		case "info":
			validateNsubmitServerAddress +="45";
			sendername = $('sender_name').value;
			senderemail = $('sender_email').value;
			senderregarding = $('sender_regarding').value;
			sendermessage = $('sender_message').value;
			sendercc = $('sender_cc').checked;
			csrf = $('csrf').innerHTML;
			csrf += 'submitcontactjd';
		break;
		default:
		break;
	}//switch

	// only continue if xmlHttp isn't void
	if (xmlHttp)
	{
		if(documentClassName=='posts'||documentClassName=='mmeditcomments')
		{
			if((nname!='')&&(nname!='your name (required)')&&
			(nemail!='')&&(nemail!='your email (will not be published) (required)')&&(nemail!='http://www.')&&
			(nreply!='')&&(nreply!='your reply')){conditionalFlag = true;}
		}
		else if(documentClassName=='info')
		{
			if((sendername!='')&&(sendername!='[type your name][required]')&&
			(senderemail!='')&&(senderemail!='[type your email][required]')&&
			(sendermessage!='')&&(sendermessage!='[type your message][required]')){conditionalFlag = true;}
		}
		// if we received non-null parameters, we add them to cache in the
		// form of the query string to be sent to the server for validation
		if(conditionalFlag)
		{
			// encode values for safely adding them to an HTTP request query string
			switch(document.body.className)
			{
				case "posts":
					postID = encodeURIComponent(postID);
					nname = encodeURIComponent(nname);
					nemail = encodeURIComponent(nemail);
					nwebsite = encodeURIComponent(nwebsite);
					nreply = encodeURIComponent(nreply);
					csrf = encodeURIComponent(csrf);
					validateNsubmitCache.push("postid="+postID+"&name="+nname+"&email="+nemail+"&website="+nwebsite+"&reply="+nreply+"&csrf="+csrf+"&pageid=posts");
					break;
				case "mmeditcomments":
					postID = encodeURIComponent(postID);
					nname = encodeURIComponent(nname);
					nemail = encodeURIComponent(nemail);
					nwebsite = encodeURIComponent(nwebsite);
					nreply = encodeURIComponent(nreply);
					csrf = encodeURIComponent(csrf);
					validateNsubmitCache.push("postid="+postID+"&name="+nname+"&email="+nemail+"&website="+nwebsite+"&reply="+nreply+"&csrf="+csrf+"&pageid=mmeditcomments");
					break;
				case "info":
					sendername = encodeURIComponent(sendername);
					senderemail = encodeURIComponent(senderemail);
					senderregarding = encodeURIComponent(senderregarding);
					sendermessage = encodeURIComponent(sendermessage);
					sendercc = encodeURIComponent(sendercc);
					csrf = encodeURIComponent(csrf);
					validateNsubmitCache.push("name="+sendername+"&email="+senderemail+"&regarding="+senderregarding+"&message="+sendermessage+"&cc="+sendercc+"&csrf="+csrf+"&pageid=info");
					break;
				default:
					break;
			}//switch
			message.className='hidden';
			message.innerHTML='';
			sendXMLRequestValidateNsubmit('multipleValues');
		}//if
		else{
			message.innerHTML = 'Error: please fill the required fields';
			message.className = 'error';
			loaderID.innerHTML=''; 
			loaderID.style.display='none';
			frmID.style.display='block';
		}
	}//if
}//validateNsubmitMultipleValues()

function sendXMLRequestValidateNsubmit(type)
{
	// try to connect to the server
	try
	{
		// continue only if the XMLHttpRequest object isn't busy
		// and the cache is not empty
		if ((xmlHttp.readyState == 4 || xmlHttp.readyState == 0) && validateNsubmitCache.length > 0)
		{
			// get a new set of parameters from the cache
			var cacheEntry = validateNsubmitCache.shift();
			// make a server request to validate the extracted data
			xmlHttp.open("POST", validateNsubmitServerAddress, true);
			xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			if(type=='singleValue'){xmlHttp.onreadystatechange = handleRequestStateChangeValidateNSubmit;}
			else if(type=='multipleValues'){xmlHttp.onreadystatechange = handleRequestStateChangeValidateNSubmitMultipleValues;}
			xmlHttp.send(cacheEntry);		
		}//if
	}//try
	catch (e)
	{
		// display an error when failing to connect to the server
		displayError(e.toString(), "submit");
	}//catch
}//sendXMLRequestValidateNsubmit(type)

// read server's response
function readResponseValidateNsubmit()
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
	if(responsetype!='routine')
	{
		valueType = xmlDoc.getElementsByTagName("valuetype")[0].firstChild.data;
		fieldID = xmlDoc.getElementsByTagName("fieldid")[0].firstChild.data;
		fieldValue = xmlDoc.getElementsByTagName("fieldvalue")[0].firstChild.data;
		result = xmlDoc.getElementsByTagName("result")[0].firstChild.data;
		errorReporter();
		if((valueType=='null')&&(fieldID=='null')&&(fieldValue=='null')){return 0;} //csrf errors and such
	}//error
	else{
		valueType = xmlDoc.getElementsByTagName("valuetype")[0].firstChild.data;
		result = xmlDoc.getElementsByTagName("result")[0].firstChild.data;
		fieldID = xmlDoc.getElementsByTagName("fieldid")[0].firstChild.data;
		fieldValue = xmlDoc.getElementsByTagName("fieldvalue")[0].firstChild.data;
		insertID = xmlDoc.getElementsByTagName("insertid")[0].firstChild.data;
		updateDate = xmlDoc.getElementsByTagName("updatedate")[0].firstChild.data;	
	}//OK

	//alert('valueType='+valueType+', result='+result+', fieldID='+fieldID+', fieldValue='+fieldValue+', insertID='+insertID);

	tempStrArr = fieldID.split('_');
	if((tempStrArr[1]!='coverid')&&(tempStrArr[1]!='imagesorder')&&(tempStrArr[1]!='visibility')&&(tempStrArr[2]!='checkbox')){
		// find the HTML element that displays the error
		message = $(fieldID+"failed");
		// show the error or hide the error
		message.className = (result == "0") ? "hidden" : "error";
	}

	switch(valueType)
	{
		case 'category':
			if(result!=0){
				//validation or database error occured
				$('catnameloader_'+tempStrArr[2]).innerHTML=''; 
				$('catnameloader_'+tempStrArr[2]).style.display='none'; 
				$('catnamefrm_'+tempStrArr[2]).style.display='block';
				$('cat_name_'+tempStrArr[2]).select();
				if(responsetype=='routine'){errorMessages(message, tempStrArr[1], result);}
			}else if(result==0){
				//all ok
				$('catnameloader_'+tempStrArr[2]).innerHTML=''; 
				$('catnameloader_'+tempStrArr[2]).style.display='none'; 
				hideEditCategoryNameField(tempStrArr[2],fieldValue);
				if(insertID!='null'){$('ncat_'+tempStrArr[2]).innerHTML=insertID; catSectionElementsArr[insertID]='enabled';}
				updateCatComboBoxes();
			}//
			break;
		case 'album':
			if(tempStrArr[1]=='coverid'){if(result==0){$('editalbumupdated_'+tempStrArr[2]).innerHTML='Updated: '+updateDate;} break;}
			if(tempStrArr[1]=='imagesorder'){if(result==0){$('editalbumupdated_'+tempStrArr[2]).innerHTML='Updated: '+updateDate;} break;}
			if(result!=0){
				//validation or database error occured
				$('album'+tempStrArr[1]+'loader_'+tempStrArr[2]).innerHTML=''; 
				$('album'+tempStrArr[1]+'loader_'+tempStrArr[2]).style.display='none'; 
				if(tempStrArr[1]=='tagid'){hideEditAlbumCategoryField(tempStrArr[2],'');}
				else{
					$('album'+tempStrArr[1]+'frm_'+tempStrArr[2]).style.display='block';
					$('album_'+tempStrArr[1]+'_'+tempStrArr[2]).select();
					if(responsetype=='routine'){errorMessages(message, tempStrArr[1], result);}
				}
			}else if(result==0){
				//all ok
				$('album'+tempStrArr[1]+'loader_'+tempStrArr[2]).innerHTML=''; 
				$('album'+tempStrArr[1]+'loader_'+tempStrArr[2]).style.display='none';
				if(fieldValue=='null'){fieldValue='';}
				if(insertID!='null'){$('nalbum_'+tempStrArr[2]).innerHTML=insertID;}
				$('editalbumupdated_'+tempStrArr[2]).innerHTML='Updated: '+updateDate;
				
				if(tempStrArr[1]=='name'){hideEditAlbumNameField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='description'){hideEditAlbumDescriptionField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='tagid'){hideEditAlbumCategoryField(tempStrArr[2],fieldValue);}
			}//
			break;
		case 'image':
			if(result!=0)
			{
				//validation or database error occured
				$('image'+tempStrArr[1]+'loader_'+tempStrArr[2]).innerHTML='';
				$('image'+tempStrArr[1]+'loader_'+tempStrArr[2]).style.display='none';
				if(tempStrArr[1]=='album'){hideEditImageAlbumField(tempStrArr[2],'');}
				else{
					$('image'+tempStrArr[1]+'frm_'+tempStrArr[2]).style.display='block';
					$('image_'+tempStrArr[1]+'_'+tempStrArr[2]).select();
					if(responsetype=='routine'){errorMessages(message, tempStrArr[1], result);}
				}
			}else if(result==0){
				//all ok
				$('image'+tempStrArr[1]+'loader_'+tempStrArr[2]).innerHTML='';
				$('image'+tempStrArr[1]+'loader_'+tempStrArr[2]).style.display='none';
				if(fieldValue=='null'){fieldValue='';}
				$('editalbumupdated_'+parseInt($('aid').innerHTML)).innerHTML='Updated: '+updateDate;

				if(tempStrArr[1]=='name'){if(fieldValue==''){fieldValue='(type a name)';} hideEditImageNameField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='description'){if(fieldValue==''){fieldValue='(type a description)';} hideEditImageDescriptionField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='album'){hideEditImageAlbumField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='tags'){if(fieldValue==''){fieldValue='(type some tags)';}hideEditImageTagsField(tempStrArr[2],fieldValue);}
			}//
			break;
		case 'post':
			if(result!=0)
			{
				//validation or database error occured
				$('post'+tempStrArr[1]+'loader_'+tempStrArr[2]).innerHTML=''; 
				$('post'+tempStrArr[1]+'loader_'+tempStrArr[2]).style.display='none'; 
				$('post'+tempStrArr[1]+'frm_'+tempStrArr[2]).style.display='block';
				$('post_'+tempStrArr[1]+'_'+tempStrArr[2]).select();
				if(responsetype=='routine'){errorMessages(message, tempStrArr[1], result);}
			}else if(result==0){
				//all ok
				$('post'+tempStrArr[1]+'loader_'+tempStrArr[2]).innerHTML=''; 
				$('post'+tempStrArr[1]+'loader_'+tempStrArr[2]).style.display='none';
				if(fieldValue=='null'){fieldValue='';}
				if(insertID!='null')
				{
					if($('npost_'+tempStrArr[2]).innerHTML.substr(0,3)=='55x')
						{
						//if($('npost_'+postID).innerHTML.substr(0,3)=='55x'){$('submituploadfrmbutt_'+postID).dispose();
						var submtButt = new Element('input',{'class': 'button','id': 'submituploadfrmbutt_'+tempStrArr[2],'type': 'submit','value': 'upload'});
						$('uploadimagesfrm_'+tempStrArr[2]).appendChild(submtButt);}
					$('npost_'+tempStrArr[2]).innerHTML=insertID; $('postid_'+tempStrArr[2]).value=insertID; $('postcreationtimestamp_'+tempStrArr[2]).innerHTML=''+updateDate;
				}
				
				if(tempStrArr[1]=='headline'){if(fieldValue==''){fieldValue='(type post headline)';} hideEditPostHeadlineField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='body'){if(fieldValue==''){fieldValue='(type your text here)';} hideEditPostBodyField(tempStrArr[2],fieldValue);}
			}
			break;
		case "albumvisibility":
			fieldValue=fieldValue.split('::');
			if(result==0){toggleAlbumVisibility(fieldValue[0]+"::"+fieldValue[1],fieldValue[2]);}//ok
			else if(result==303){alert(''+303+': Unable to execute request.'); }//error
			break;
		case "togglesettingscheckbox":
			fieldID=fieldID.split('_'); fieldID=fieldID[0]+fieldID[1]+fieldID[2];
			if(result==0){toggleSettingsCheckbox(fieldID,fieldValue);}//ok
			else if(result==304){alertmessages('Error',304+': Unable to execute request.');}//error
			break;
		case "profile":
			if((result!=0)&&(result!=305)){
				//validation or database error occured
				$(tempStrArr[0]+tempStrArr[1]+'loader_'+tempStrArr[2]).innerHTML=''; 
				$(tempStrArr[0]+tempStrArr[1]+'loader_'+tempStrArr[2]).style.display='none'; 
				$(tempStrArr[0]+tempStrArr[1]+'frm_'+tempStrArr[2]).style.display='block';
				$(tempStrArr[0]+'_'+tempStrArr[1]+'_'+tempStrArr[2]).select();
				errorMessages(message, tempStrArr[1], result);
			}
			else if(result==0){
				//all ok
				if(fieldValue=='null'){fieldValue='';}
				$('profile'+tempStrArr[1]+'loader_'+tempStrArr[2]).innerHTML=''; 
				$('profile'+tempStrArr[1]+'loader_'+tempStrArr[2]).style.display='none';
				if(tempStrArr[1]=='username'){hideEditProfileUserNameField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='email'){hideEditProfileEmailField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='blog'){hideEditProfileBlogField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='welcomepagetext'){if(fieldValue==''){fieldValue='(type some welcoming text)';} hideEditWelcomePageTextField(tempStrArr[2],fieldValue);}
				else if(tempStrArr[1]=='information'){if(fieldValue==''){fieldValue='(type your profile information)';} hideEditProfileInformationField(tempStrArr[2],fieldValue);}
			}
			else if(result==305){
				$('profile'+tempStrArr[1]+'loader_'+tempStrArr[2]).innerHTML=''; 
				$('profile'+tempStrArr[1]+'loader_'+tempStrArr[2]).style.display='none';
				if(tempStrArr[1]=='username'){hideEditProfileUserNameField(tempStrArr[2],$('editprofile'+tempStrArr[1]+'_'+tempStrArr[2]).innerHTML);}
				else if(tempStrArr[1]=='email'){hideEditProfileEmailField(tempStrArr[2],$('editprofile'+tempStrArr[1]+'_'+tempStrArr[2]).innerHTML);}
				else if(tempStrArr[1]=='blog'){hideEditProfileBlogField(tempStrArr[2],$('editprofile'+tempStrArr[1]+'_'+tempStrArr[2]).innerHTML);}
				else if(tempStrArr[1]=='welcomepagetext'){hideEditWelcomePageTextField(tempStrArr[2],$('editprofile'+tempStrArr[1]+'_'+tempStrArr[2]).innerHTML);}
				else if(tempStrArr[1]=='information'){hideEditProfileInformationField(tempStrArr[2],$('editprofile'+tempStrArr[1]+'_'+tempStrArr[2]).innerHTML);}
				alertmesssages(''+305+': Unable to execute request.');
			}//error
			break;
		default:
			break;
	}//switch
	sendXMLRequestValidateNsubmit('singleValue');
}//readResponseValidateNsubmit()

function readResponseValidateNsubmitMultipleValues()
{
	// retrieve the server's response
	//alert('1' + xmlHttp);
	//alert('2' + xmlHttp.responseText);
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
	
	pageID = xmlDoc.getElementsByTagName("pageid")[0].firstChild.data;
	
	if(pageID=='posts' || pageID=='mmeditcomments')
	{
		newCommentName = xmlDoc.getElementsByTagName("commentname")[0].firstChild.data;
		newCommentEmail = xmlDoc.getElementsByTagName("commentemail")[0].firstChild.data;
		newCommentWebsite = xmlDoc.getElementsByTagName("commentwebsite")[0].firstChild.data;
		newCommentTimestamp = xmlDoc.getElementsByTagName("commenttimestamp")[0].firstChild.data;
		newCommentReply = xmlDoc.getElementsByTagName("commentreply")[0].firstChild.data;
		valueType = xmlDoc.getElementsByTagName("valuetype")[0].firstChild.data;
		result = xmlDoc.getElementsByTagName("result")[0].firstChild.data;
		fieldID = xmlDoc.getElementsByTagName("fieldid")[0].firstChild.data;
		fieldValue = xmlDoc.getElementsByTagName("fieldvalue")[0].firstChild.data;
		insertID = xmlDoc.getElementsByTagName("insertid")[0].firstChild.data;
		postID = xmlDoc.getElementsByTagName("post")[0].firstChild.data;
		
		// find the HTML element that displays the error
		message = $('newcommentmessagesfailed');
		// show the error or hide the error
		message.className = (result == "0") ? "hidden" : "error";
		
		frmID = $('newcommentfrms');
		loaderID = $('newcommentloader');
	}
	else if(pageID=='info')
	{
		newSenderName = xmlDoc.getElementsByTagName("sendername")[0].firstChild.data;
		newSenderEmail = xmlDoc.getElementsByTagName("senderemail")[0].firstChild.data;
		newSenderRegarding = xmlDoc.getElementsByTagName("senderregarding")[0].firstChild.data;
		newSenderMessage = xmlDoc.getElementsByTagName("sendermessage")[0].firstChild.data;
		newSenderCC = xmlDoc.getElementsByTagName("sendercc")[0].firstChild.data;
		if(newSenderRegarding=='null'){newSenderRegarding='-';}
		if(newSenderCC=='true'){newSenderCC="<li class='scc'>*A copy of this message has been send to the email that you've provided.</li>"}
		else if(newSenderCC=='false'){newSenderCC=''}
		
		valueType = xmlDoc.getElementsByTagName("valuetype")[0].firstChild.data;
		result = xmlDoc.getElementsByTagName("result")[0].firstChild.data;
		fieldID = xmlDoc.getElementsByTagName("fieldid")[0].firstChild.data;
		fieldValue = xmlDoc.getElementsByTagName("fieldvalue")[0].firstChild.data;
		insertID = xmlDoc.getElementsByTagName("insertid")[0].firstChild.data;
		
		// find the HTML element that displays the error
		message = $('senderfailed');
		// show the error or hide the error
		message.className = (result == "0") ? "hidden" : "error";
		
		frmID = $('contactfrm');
		loaderID = $('senderloader');
	}

	switch(pageID)
	{
		case 'posts':
			if(result!=0){
				//validation error occured
				loaderID.innerHTML=''; 
				loaderID.style.display='none';
				frmID.style.display='block';
				errorMessages(message, fieldID, result);
			}else if(result==0){
				//all ok
				loaderID.innerHTML=''; 
				loaderID.style.display='none';
				$('commentcounter').innerHTML=parseInt($('commentcounter').innerHTML)+1;
				if(parseInt($('commentcounter').innerHTML)==1){$('commentscounterphrase').innerHTML='comment'}
				else{$('commentscounterphrase').innerHTML='comments'}
				if($('logoutbutton')!=null)
					{deleteButton="<div class='deletecommentbuttons' id='deletecommentbutton_"+insertID+"'>delete</div>";
					commentDelete='<ul id="commentdelete_'+insertID+'" class="commentdeletes"><li class="deletecommentmsg">delete this comment?</li>'
					+'<li class="completedeletecomments"><span id="completedeletecomment_'+insertID+'">yes</span></li>'
					+'<li class="dontdeletecomments"><span id="dontdeletecomments_'+insertID+'">no</span></li></ul>';}
				else {deleteButton=""; commentDelete="";}
				var listitem = new Element('li', {
					'id': 'comment_'+insertID+'',
					'html': "\n\n<div id='commentelement_"+insertID+"'><a href='"+newCommentWebsite+"'>"+newCommentName+"</a> said,<div class='biglines'></div><div class='timestamp'>"+newCommentTimestamp+"</div><div class='reply'>"+newCommentReply+"</div></div>"+deleteButton+commentDelete+"\n\n"
				});
				
				//if($('logoutbutton')!=null){createNewComment(listitem);}
				//initDeleteElements(parseInt(insertID));
				if($('logoutbutton')!=null){createNewComment(listitem); initDeleteElements(parseInt(insertID));}
				else {createNewComment(listitem);}
			}
		break;
		case 'mmeditcomments':
			if(result!=0){
				//validation error occured
				loaderID.innerHTML=''; 
				loaderID.style.display='none';
				frmID.style.display='block';
				errorMessages(message, fieldID, result);
			}else if(result==0){
				//all ok
				loaderID.innerHTML=''; 
				loaderID.style.display='none';
				if($('logoutbutton')!=null)
					{deleteButton="<div class='deletecommentbuttons' id='deletecommentbutton_"+insertID+"'>delete</div>";
					commentDelete='<ul id="commentdelete_'+insertID+'" class="commentdeletes"><li class="deletecommentmsg">delete this comment?</li>'
					+'<li class="completedeletecomments"><span id="completedeletecomment_'+insertID+'">yes</span></li>'
					+'<li class="dontdeletecomments"><span id="dontdeletecomments_'+insertID+'">no</span></li></ul>';}
				else {deleteButton=""; commentDelete="";}
				var listitem = new Element('li', {
					'id': 'comment_'+insertID+'',
					'html': "\n\n<div id='commentelement_"+insertID+"'><a href='"+newCommentWebsite+"'>"+newCommentName+"</a> said,<div class='biglines'></div><div class='timestamp'>"+newCommentTimestamp+"</div><div class='reply'>"+newCommentReply+"</div></div>"+deleteButton+commentDelete+"\n\n"
				});
				if($('logoutbutton')!=null){createNewComment(listitem);}
				initDeleteElements(parseInt(insertID));
			}
		break;
		case "info":
			if(result!=0){
				//validation error occured
				loaderID.innerHTML=''; 
				loaderID.style.display='none';
				frmID.style.display='block';
				errorMessages(message, fieldID, result);
			}else if(result==0){
				//all ok
				loaderID.innerHTML=''; 
				loaderID.style.display='none';
				
				var sentEmail = new Element ('div', {
					'id': 'sentemail',
					'html': "<div id='secb'><img src='./jdlayout/images/closebutttransparent.png' id='sentemailclosebutton' title='Close sent email' alt='close sent email'/></div><div id='sentemailbanner'>Your message has been sent successfully</div><ul id='sentemailcontent'><li class='sname'>Name: <span class='italics'>"+newSenderName+"</span></li><li class='semail'>E-Mail: <span class='italics'>"+newSenderEmail+"</span></li><li class='sregarding'>Regarding: <span class='italics'>"+newSenderRegarding+"</span></li><li class='smessage'>Message: <div id='sentemailmessage'>"+nl2br(newSenderMessage)+"</div></li>"+newSenderCC+"</ul>"
				});
				$('sentemailanchor').appendChild(sentEmail);
				
				$('sentemailclosebutton').addEvent('click',function(e)
				{e.stop(); 
					$('sentemail').dispose(); 
					$('sender_name').value='[type your name][required]';
					$('sender_email').value='[type your email][required]';
					$('sender_regarding').value='[regarding][not required]';
					$('sender_message').value='[type your message][required]';
					$('scounter').innerHTML=contactMessageMaxLength;
					frmID.style.display='block';
				});
			}		
		break;
		default:
		break;
	}//switch
	
	sendXMLRequestValidateNsubmit('multipleValues');
}//readResponseValidateNsubmitMultipleValues()

//errorMessages()
function errorMessages(element, fieldID, errorCode)
{
	var $errVals = new Array();
	$errVals[101] = "Please type a "+fieldID+".";
	$errVals[102] = "Please type a shorter "+fieldID+".";
	$errVals[103] = "Please type a valid "+fieldID+".";
	$errVals[104] = "This "+fieldID+" already exists.";
	
	$errVals[201] = "This "+fieldID+" already exists in this category.";
	$errVals[202] = "An image with this "+fieldID+" already exists.";
	$errVals[205] = "Please name your new category first.";
	
	element.innerHTML = $errVals[errorCode];
}//errorMessages(element, fieldID, errorCode)