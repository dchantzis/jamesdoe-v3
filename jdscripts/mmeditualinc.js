// JavaScript Document

window.addEvent('domready', function(){
	if($('deleteallroutineentries'))
	{
		$('deleteallroutineentries').addEvent('click', function(e){e.stop(); deleteAllEntries();});
		$('completedeleteentries').addEvent('click', function(e){e.stop(); deleteAllUsersActionLogEntries('routine');});
		$('dontdeleteentries').addEvent('click', function(e){e.stop(); dontdeleteAllEntries();});
	}
	if($('deleteallerrorentries'))
	{
		$('deleteallerrorentries').addEvent('click', function(e){e.stop(); deleteAllEntries();});
		$('completedeleteentries').addEvent('click', function(e){e.stop(); deleteAllUsersActionLogEntries('error');});
		$('dontdeleteentries').addEvent('click', function(e){e.stop(); dontdeleteAllEntries();});
	}
});

function deleteAllEntries()
{
	$('entriesoptions').style.display='none';
	$('entriesdelete').style.display='block';
	$('usersactionlogentries').fade(0.3);
}//deleteAlbum(albumID)
function dontdeleteAllEntries()
{	
	$('entriesoptions').style.display='block';
	$('entriesdelete').style.display='none';
	$('usersactionlogentries').fade(1);
}//dontDeleteAlbum(albumID)
function completeDeleteEntries(usersActionLogType)
{
	$('entriesoptions').dispose();
	$('edituallinks').dispose();
	$('entriesdelete').dispose();
	$('usersactionlogentries').dispose();
	if(usersActionLogType=='routine')
	{
			var divItem = new Element('div', {
				'id': 'noresultsmessage',
				'html': '\n\n<span id="howcome">How come???</span><span id="reasonhowcome">No actions in the system... yet.</span>\n\n'});
			$('maincolumn').appendChild(divItem);
	}
	if(usersActionLogType=='error')
	{
			var divItem = new Element('div', {
				'id': 'noresultsmessage',
				'html': '\n\n<span id="luckyyou">Lucky You!!!</span><span id="reasonluckyyou">No errors occured in the system... yet.</span>\n\n'});
			$('maincolumn').appendChild(divItem)
	}
}//completeDeleteEntries()