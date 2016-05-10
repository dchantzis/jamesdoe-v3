// JavaScript Document
/*
initialize help button and helpcontent.
can be used for every page with ONE help button
*/

window.addEvent('domready', 
	function(){
		$('helpcontent').style.visibility="hidden";
		$('helpbutt').addEvent('click', function(e) {
			e.stop();
			$('maincolumn').fade(0);
			$('helpcontent').fade(1);
		});
		$('helpclosebutt').addEvent('click', function(e)
		{
			e.stop();
			$('maincolumn').fade(1);
			$('helpcontent').fade(0);
		});
	}
);