// JavaScript Document

var contactMessageMaxLength = 3000;

window.addEvent('domready', function(){
	$('sender_name').addEvent('click',function(e){e.stop(); if($('sender_name').value=='[type your name][required]'){$('sender_name').value='';}});
	$('sender_email').addEvent('click',function(e){e.stop(); if($('sender_email').value=='[type your email][required]'){$('sender_email').value='';}});
	$('sender_regarding').addEvent('click',function(e){e.stop(); if($('sender_regarding').value=='[regarding][not required]'){$('sender_regarding').value='';}});
	$('sender_message').addEvent('click',function(e){e.stop(); if($('sender_message').value=='[type your message][required]'){$('sender_message').value='';}});
	$('sender_message').addEvent('focus',function(e){e.stop(); if($('sender_message').value=='[type your message][required]'){$('sender_message').value='';}});
	$('sender_message').addEvent('keyup',function(e){e.stop(); countChars('sender_message','scounter',contactMessageMaxLength);});
	$('sender_send').addEvent('click',function(e){e.stop(); validateNsubmitMultipleValues(); });
});