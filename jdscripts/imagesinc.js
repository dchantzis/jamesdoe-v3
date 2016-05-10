// JavaScript Document

window.addEvent('domready', 
	function(){
	$('previousimage').addEvent('click',function(e){e.stop(); imageID=$('image').innerHTML; getImage(imageID,'previous');});
	$('nextimage').addEvent('click',function(e){e.stop(); imageID=$('image').innerHTML; getImage(imageID,'next');});
	$('imagefileurl').addEvent('click',function(e){e.stop(); loadImageFullSize($('imagefileurl').src); });
});

function loadImageFullSize(imageurl)
{
	window.open(imageurl ,"_blank","fullscreen=no,status=no,toolbar=no,menubar=no,resizable=yes,scrollbars=yes");
}