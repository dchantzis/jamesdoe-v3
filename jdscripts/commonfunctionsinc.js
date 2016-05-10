// JavaScript Document

function removeHTMLElement(obj)
{
	while(obj.hasChildNodes())
	{ obj.removeChild( obj.lastChild ); }
}//removeHTMLElement()

function sleep(naptime)
{
	naptime = naptime * 1000;
	var sleeping = true;
	var now = new Date();
	var alarm;
	var startingMSeconds = now.getTime();
	while(sleeping)
	{
		alarm = new Date();
		alarmMSeconds = alarm.getTime();
		if(alarmMSeconds - startingMSeconds > naptime){ sleeping = false; }
	}
	return 0;
}//sleep(naptime)

function trim(str, chars)
{
	return ltrim(rtrim(str, chars), chars);
}

function ltrim(str, chars)
{
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}

function rtrim(str, chars)
{
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

function getCurrentTimeStamp()
{
	var currentTimeStamp = new Date();
	var month = currentTimeStamp.getMonth()+1;
	var day = currentTimeStamp.getDate();
	var year = currentTimeStamp.getFullYear();
	var hours = currentTimeStamp.getHours();
	var minutes = currentTimeStamp.getMinutes();
	var seconds = currentTimeStamp.getSeconds();
	var fullTimeStamp = year+'-'+month+'-'+day+' '+hour+':'+'minute'+':'+'seconds';
	return fullTimeStamp;
}//getCurrentTimeStamp()

function getCurrentConvertedTimeStamp(dateType)
{
	var monthNames = new Array;
	var currentTimeStamp = new Date();
	var month = currentTimeStamp.getMonth()+1;
	var day = currentTimeStamp.getDate();
		day = day.toString();
		if(day.length==1){day='0'+day;}
	var year = currentTimeStamp.getFullYear();
		year = year.toString();
		if(year.length==1){year='0'+year;}
	var hours = currentTimeStamp.getHours();
		hours = hours.toString();
		if(hours.length==1){hours='0'+hours;}
	var minutes = currentTimeStamp.getMinutes();
		minutes = minutes.toString();
		if(minutes.length==1){minutes='0'+minutes;}
	var seconds = currentTimeStamp.getSeconds();
		seconds = seconds.toString();
		if(seconds.length==1){seconds='0'+seconds;}
	
	if(dateType=='full')
	{
		monthNames[0] = ''; monthNames[1] = 'January';
		monthNames[2] = 'February'; monthNames[3] = 'March'; monthNames[4] = 'April'; monthNames[5] = 'May'; monthNames[6] = 'June'; monthNames[7] = 'July';
		monthNames[8] = 'August'; monthNames[9] = 'September'; monthNames[10] = 'October'; monthNames[11] = 'November'; monthNames[12] = 'December';
	}else if(dateType=='short')
	{
		monthNames[0] = ''; monthNames[1] = 'Jan'; monthNames[2] = 'Feb'; monthNames[3] = 'Mar'; monthNames[4] = 'Apr'; monthNames[5] = 'May'; monthNames[6] = 'June';
		monthNames[7] = 'July'; monthNames[8] = 'Aug'; monthNames[9] = 'Sept'; monthNames[10] = 'Octr'; monthNames[11] = 'Nov'; monthNames[12] = 'Dec';
	}
	
	var fullConvertedTimeStamp = monthNames[month]+' '+day+' '+year+', '+hours+':'+minutes;
	return fullConvertedTimeStamp;
}//convertTimeStamp(dateStr,dateType)

function countChars(fieldID,counterID,maxChars)
{
	var remainingChars = maxChars-$(fieldID).value.length;
	$(counterID).innerHTML = remainingChars;
	if(remainingChars<=99) {$(counterID).style.color="#FF0000";}
	if(remainingChars>99) {$(counterID).style.color="#999999";}
}//countChars(elementID)

function alertmessages(messageType,message)
{
	$('alertmessage').innerHTML = '<span class="red">'+messageType+':</span> '+message;
	$('maincolumn').fade(0);
	$('alertcontent').fade(1);
}//alertmessages($message)
