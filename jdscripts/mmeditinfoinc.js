// JavaScript Document

/* GLOBAL JS ARRAYS */
var profileID;
var profileInfoMaxLength = 3500;

window.addEvent('domready', 
	function(){
		profileID = parseInt($('profile').innerHTML);

		$('editprofileinformation_'+profileID).addEvent('click', function(e){e.stop(); editProfileInformation(profileID);});
		$('profileinformationsubmitbutt_'+profileID).addEvent('click', function(e){e.stop(); validateNsubmit('profile_information_'+profileID);});
		$('profileinformationcancelbutt_'+profileID).addEvent('click', function(e){e.stop(); hideEditProfileInformationField(profileID,'null');});
		$('profile_information_'+profileID).addEvent('click', function(e){e.stop(); if($('profile_information_'+profileID).value=='(type your profile information)'){$('profile_information_'+profileID).value='';}});
		$('profile_information_'+profileID).addEvent('keyup',function(e){e.stop(); countChars('profile_information_'+profileID,'picounter_'+profileID,profileInfoMaxLength);})
});

function editProfileInformation(profileID)
{
	$('editprofileinformation_'+profileID).style.display = 'none';
	$('profileinformationfrm_'+profileID).style.display = 'inline';
	$('profile_information_'+profileID).value = (html_entity_decode($('editprofileinformation_'+profileID).innerHTML,'ENT_QUOTES'));
	$('profile_information_'+profileID).select();
}//editWelcomePageText(profileID)
function hideEditProfileInformationField(profileID,fieldValue)
{
	if(fieldValue==''){fieldValue = '(type your profile information)';}
	if(fieldValue=='null'){ fieldValue = $('editprofileinformation_'+profileID).innerHTML; }
	$('editprofileinformation_'+profileID).innerHTML = fieldValue;
	$('profile_information_'+profileID+'failed').className='hidden';	
	$('profileinformationfrm_'+profileID).style.display = 'none';
	$('editprofileinformation_'+profileID).style.display = 'block';
}//hideEditWelcomePageTextField(profileID,fieldValue)