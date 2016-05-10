<?php 
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	header ("Content-type: text/html; charset=utf-8");
	###################################################################################
	
	session_start(); //start session
	session_regenerate_id(true); //regenerate session id
	//regenerate session id if PHP version is lower thatn 5.1.0
	if(!version_compare(phpversion(),"5.1.0",">=")){ setcookie( session_name(), session_id(), ini_get("session.cookie_lifetime"), "/" );}
	
	require("./jdincfunctions/functionsinc.php");
	
	$csrf_password_generator = hash('sha256', "browsersettings").CSRF_PASS_GEN;
	
	if (isset($_GET["e"])) {$flg = $_GET["e"];}
	$errVars['javascriptEnabled'] = '0';
	if( $flg == hash('sha256', "javascript")){ $errVars['javascriptEnabled'] = '1';} 
	if( $flg == hash('sha256', "cookies")){ $errVars['cookiesEnabled'] = '1';}
	checkCookiesAvailability($errVars);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>.::jAMES dOE::.</title>
<script type="text/javascript" src="./jdscripts/mootools/mootools.js"></script>
<script type="text/javascript" src="./jdscripts/commonfunctionsinc.js"></script>
<script type="text/javascript" src="./jdscripts/commonajaxfunctionsinc.js"></script>
<script type="text/javascript" src="./jdscripts/layoutinc.js"></script>
<style type="text/css" media="screen"> 
	@import url(./jdlayout/css/allstylesinc.css); 
</style>
<link rel="stylesheet" rev="stylesheet" href="./jdlayout/css/printout.css" type="text/css" media="print" />
</head>

<body class='browsersettings'>
	<div id="wrapper">
    <?=sidebarSlim()?>
    <div id='topbar'></div>
    <div id="content">
		<div id="pagetitle">
        	<ul class='secondarynavi'>
                <li class='highlight'>browser settings</li>
            </ul>
        </div>
    	<div id="spacer"></div>
		<div id="maincolumn">
        
        <div class='browsersettingsbanners'>
			<span class='red'>ERROR: </span>
			<?php 
			if( $flg == hash('sha256', "javascript")){echo "Javascript disabled"; } 
			if( $flg == hash('sha256', "cookies")){echo "Cookies are disabled"; } 
			?>
		</div>
        
		<div class="notes">
            Sorry for the inconvenience. <br />
            To browse through this website, <span class='bold'>javascript</span> and <span class='bold'>cookies</span> must be enabled in your browser. <br />
			To enable <span class='bold'>javascript</span> in your browser follow these <a href="#js_instructions" class="commonlinks">instructions</a>. <br />
            To enable <span class='bold'>cookies</span> in your browser follow these <a href="#c_instructions" class="commonlinks">instructions</a>. <br />
        </div> 

        <ul id="js_instructions">
        	<h3>Enable javascript: </h3>
            <li>
            	<span class="red">For Mozilla Firefox 1.5 & 2 users: </span>
            	<ul class='subul'>
                    <li>Click on the Tools menu.</li>
                    <li>Select Options.</li>
                    <li>Click the Content tab with the Earth graphic.</li>
                    <li>Check "Enable JavaScript".</li>
                    <li>Click OK to close the dialogue.</li>
                    <li>Click the Reload button or hit F5 to refresh the page.</li>
          		</ul>
            </li>
            <li>
            	<span class="red">For Internet Explorer 7 users: </span>
                <ul class='subul'>
                    <li>Click on the Tools button or "Tools" from the program menu.</li>
                    <li>Click on Internet Options.</li>
                    <li>Click the Security tab.</li>
                    <li>In the "Security level for this zone" box, click on Custom level.</li>
                    <li>Scroll toward the bottom of the Settings box to Scripting.</li>
                    <li>Enable active scripting.</li>
                    <li>Click OK to close the dialogue.</li>
                    <li>Click the Refresh button or hit F5 to refresh the page.</li>
          		</ul>
            </li>
            <li>
            	<span class="red">For Internet Explorer 5.X & 6.X  users: </span>
                <ul class='subul'>
                    <li>Select Internet Options from the Tools menu.</li>
                    <li>In Internet Options dialog box select the Security tab.</li>
                    <li>Click Custom level button at bottom.</li>
                    <li>Under Scripting category enable Active Scripting, Allow paste options via script and Scripting of Java applets.</li>
                    <li>Click OK to close the dialogue.</li>
                    <li>Click the Refresh button or hit F5 to refresh the page.</li>
          		</ul>
            </li>
            <li>
            	<span class="red">For Opera 9 users: </span>
                <ul class='subul'>
                    <li>Select the Tools menu.</li>
                    <li>Select Preferences.</li>
                    <li>Click the Advanced tad.</li>
                    <li>Select the Content option.</li>
                    <li>Check "Enable JavaScript".</li>
                    <li>Click OK to close the dialogue.</li>
                    <li>Click the Refresh button or hit F5 to refresh the page.</li>
                </ul>
          	</li>
            <li>
            	<span class="red">For Netscape 7.X users: </span>
                <ul class='subul'>
                    <li>Select Preferences from the Edit menu.</li>
                    <li>Click the arrow next to Advanced.</li>
                    <li>Click Scripts & Plugins.</li>
                    <li>Check Navigator beneath "Enable Javascript for".</li>
                    <li>Click OK.</li>
                    <li>Click the Refresh button or hit F5 to refresh the page.</li>
                </ul>
            </li>
            <li>
            	<span class="red">For Safari 2.X & 3.X users: </span>
                <ul class='subul'>
                    <li>Click on the Tools menu.</li>
                    <li>Select Preferencies.</li>
                    <li>From the Security Tab, check "Enable Javascript".</li>
                    <li>Close the dialogue.</li>
                    <li>Click the Refresh button or hit F5 to refresh the page.</li>
                </ul>
            </li>
        </ul>
        
       	<ul id="c_instructions">
        	<h3>Enable cookies: </h3>
            <li>
            	<span class="red">For Mozilla Firefox 1.5 & 2 users: </span>
                <ul class='subul'>
                    <li>Click on the Tools menu.</li>
                    <li>Select Options.</li>
                    <li>Click the Privacy tab with the Lock graphic.</li>
                    <li>Check "Accept cookies from sites".</li>
                    <li>Click OK to close the dialogue.</li>
                    <li>Click the Reload button or hit F5 to refresh the page.</li>
            	</ul>
            </li>
            <li>
            	<span class="red">For Internet Explorer 5.X & 6.X & 7 users: </span>
				<ul class='subul'>
                    <li>Click on the Tools button or "Tools" from the program menu.</li>
                    <li>Click on Internet Options.</li>
                    <li>Click the Privacy tab.</li>
                    <li>Under Settings, click Advanced button.</li>
                    <li>Check the box Override automatic cookie handling under Cookies section in Advanced Privacy Settings window.</li>
                    <li>Under First-party Cookies, select Accept.</li>
                    <li>Under Third-party Cookies, select Accept.</li>
                    <li>Check the box Always allow session cookies.</li>
                    <li>Click OK to close the dialogue.</li>
                    <li>Click the Refresh button or hit F5 to refresh the page.</li>
            	</ul>
            </li>
            <li>
            	<span class="red">For Opera 9 users: </span>
                <ul class='subul'>
                    <li>Select the Tools menu.</li>
                    <li>Select Preferences.</li>
                    <li>Click the Advanced tad.</li>
                    <li>Select the Cookies option.</li>
                    <li>Select "Accept cookies".</li>
                    <li>Click OK to close the dialogue.</li>
                    <li>Click the Refresh button or hit F5 to refresh the page.</li>
                </ul>
            </li>
           	<li>
            	<span class="red">For Netscape 7.X users: </span>
                <ul class='subul'>
                    <li>Select Preferences from the Edit menu.</li>
                    <li>From the Preferences dialog box, under Category, double-click Privacy & Security.</li>
                    <li>Under Privacy & Security, click to select Cookies.</li>
                    <li>Under Cookies, click to select "Allow all cookies".</li>
                    <li>Click OK.</li>
                    <li>Click the Refresh button or hit F5 to refresh the page.</li>
                </ul>
            </li>
            <li>
            	<span class="red">For Safari 2.X & 3.X users: </span>
                <ul class='subul'>
                    <li>Click on the Tools menu.</li>
                    <li>Select Preferencies.</li>
                    <li>From the Security Tab, select Accept Cookies: "Only from sites you navigate to".</li>
                    <li>Close the dialogue.</li>
                    <li>Click the Refresh button or hit F5 to refresh the page.</li>
                </ul>
            </li>
        </ul>

        </div><!--mainColumn-->
		<div id='mainfooter'></div>
    </div><!--content-->
    </div><!--wrapper-->
    <span id='csrf' class='hidden'><?=$csrf_password_generator?></span>
</body>
</html>
</html>
