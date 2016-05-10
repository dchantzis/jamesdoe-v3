<?php
require_once('jddbase.class.php');
// Class supports AJAX and PHP web form validation
class Validate
{	
	function __construct() {}
	function __destruct() {}
	
	// supports AJAX validation, verifies a single value
	public function ValidateAJAX($val, $key, $arValsRequired, $arValsMaxSize, $arValsValidations)
	{	
		$errorsExist = 0;// error flag, becomes 0 when no errors are found.
		
		if(!isset($arValsRequired[$key])) {}
		else
		{	
			if(!$this->variablesSet($key))
			{
				$_SESSION['errorformvalues'][$key] = 'error';
				$errorsExist = 101;
				return $errorsExist;
			}
		}//else

		if(!isset($arValsRequired[$key])){}
		else
		{
			if(!$this->variablesFilled($key))
			{
				$_SESSION['errorformvalues'][$key] = 'error';
				$errorsExist = 101;
				return $errorsExist;
			}
		}//else

		if(!isset($arValsMaxSize[$key])){}
		else
		{
			if(!$this->variablesCheckRange($key, $arValsMaxSize[$key]))
			{
				$_SESSION['errorformvalues'][$key] = 'error';
				$errorsExist = 102;
				return $errorsExist;
			}
		}//else

		if(!isset($arValsValidations[$key])){}
		else
		{
			if(!$this->variablesValidate($key, $arValsValidations[$key]))
			{
				$_SESSION['errorformvalues'][$key] = 'error';
				$errorsExist = 103;
				return $errorsExist;
			}
		}//else
		
		//ALL OK
		$_SESSION['errorformvalues'][$key] = 'hidden';
		return 0;
	}//ValidateAJAX
	
	// validates all form fields on form submit
	public function ValidatePHP($arValsRequired, $arValsMaxSize, $arValsValidations)
	{
		$errorsVarsArr = array();
		$errorsVarsArr['errorCode'] = 0;// error flag, becomes 0 when no errors are found.
		$errorsVarsArr['errorFieldID'] = '';
		$errorsVarsArr['errorFieldValue'] = '';

		if (isset($_SESSION['errorformvalues'])) { unset($_SESSION['errorformvalues']); } // clears the errors session flag
	
		// By default all fields are considered valid
		reset($arValsRequired); 		
		while(list($key, $val) = each ($arValsRequired))
		{
			$_SESSION['errorformvalues'][$key] = 'hidden';
		}//while
		
		reset($arValsRequired);
		while(list($key, $val) = each ($arValsRequired))
		{
			if(!$this->variablesSet($key))
			{
				$_SESSION['errorformvalues'][$key] = 'error';
				$errorsVarsArr['errorCode'] = 101;
				$errorsVarsArr['errorFieldID'] = $key;
				$errorsVarsArr['errorFieldValue'] = $val;
				return $errorsVarsArr;
			}//if
		}//while
		
		reset($arValsRequired);
		while(list($key, $val) = each ($arValsRequired))
		{
			if(!$this->variablesFilled($key))
			{
				$_SESSION['errorformvalues'][$key] = 'error';
				$errorsVarsArr['errorCode'] = 101;
				$errorsVarsArr['errorFieldID'] = $key;
				$errorsVarsArr['errorFieldValue'] = $val;
				return $errorsVarsArr;
			}//if
		}//while
		
		reset($arValsMaxSize);
		while(list($key, $val) = each ($arValsMaxSize))
		{
			if(!$this->variablesCheckRange($key, $val))
			{
				$_SESSION['errorformvalues'][$key] = 'error';
				$errorsVarsArr['errorCode'] = 102;
				$errorsVarsArr['errorFieldID'] = $key;
				$errorsVarsArr['errorFieldValue'] = $val;
				return $errorsVarsArr;
			}//if
		}//while
		
		reset($arValsValidations);
		while(list($key, $val) = each ($arValsValidations))
		{
			if(!$this->variablesValidate($key, $val))
			{
				$_SESSION['errorformvalues'][$key] = 'error';
				$errorsVarsArr['errorCode'] = 103;
				$errorsVarsArr['errorFieldID'] = $key;
				$errorsVarsArr['errorFieldValue'] = 'null'; //$errorsVarsArr['errorFieldValue'] = $val;
				return $errorsVarsArr;
			}//if
		}//while
		
		if ($errorsExist == 0) { return $errorsVarsArr; }//all ok
		else { $errorsVarsArr['errorsExist']=$errorsExist; return $errorsVarsArr; }// ERROR: return error code
	}//ValidatePHP()
	
	
	
	// function that checks to see if these variables have been set...
	private function variablesSet($key)
	{
		if ( (!isset($_SESSION['formvalues'][$key])) ){ return 0; }
		else{ return 1; }//valid
	}//variablesSet
	
	// function that checks if the form variables have something in them...
	private function variablesFilled($key)
	{
		if ($_SESSION['formvalues'][$key] == "" ){ return 0; }
		else{ return 1; }//valid
	}//variablesFilled()
	
	//function that checks if the fields are within the proper range...
	private function variablesCheckRange($key, $val)
	{
		if (strlen($_SESSION['formvalues'][$key]) > $val){ return 0; }
		else{ return 1; }//valid
	}//variable
	
	//function that makes sure fields are within the proper range... else cuts off any extra...
	private function  variablesCheckRangeCutExtra($key, $val)
	{	
		while (list($key, $val) = each($arrayValues))
		{
			if (strlen($_SESSION['formvalues'][$key]) > $val) { $_SESSION[$key] = substr($_SESSION[$key],0,$val); }
		}//while
	}//variablesCheckRangeCutExtra
	
	private function variablesValidate($key, $val)
	{
		if($_SESSION['formvalues'][$key] == NULL){return 1;}
		else
		{
			if(!preg_match($val, $_SESSION['formvalues'][$key] )) { return 0; } 
			else { return 1; }
		}
	}//variablesValidate
	
	public function checkPost()
	{
		//check if $_POST is set. If it's not set, then the form was not submitted normaly.
		if(!isset($_POST)){ return 0; } //error
		else { return 1; }//all ok
	}//checkPost()
	
	public function checkCSRF($submittedCSRF, $referenceCSRF, $submittedForm)
	{
		//check for CSRF (Cross Site Request Forgery)
		$referenceCSRF = hash('sha256', $submittedForm) . $referenceCSRF;
		if($submittedCSRF != $referenceCSRF){ return 0; }//error
		else { return 1; } //all ok
	}//checkCSRF($submittedCSRF, $referenceCSRF, $submittedForm)
	
	//uploadErrorMessages()
	public function uploadFilesErrorMessages($errorid)
	{	
		/*
		Since PHP 4.2.0, PHP returns an appropriate error code along with the file array. 
		The error code can be found in the error segment of the file array that is created 
		during the file upload by PHP. In other words, the error might be found in $_FILES['userfile']['error'].
		*/
		switch($errorid)
		{
			case 0:
				//UPLOAD_ERR_OK
				//There is no error, the file uploaded with success.
				return 0; //no error
				break;
			case 1:
				//UPLOAD_ERR_INI_SIZE
				//The uploaded file exceeds the upload_max_filesize directive in php.ini.
				return 191;
				break;
			case 2:
				//UPLOAD_ERR_FORM_SIZE
				//The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
				return 192;
				break;
			case 3:
				//UPLOAD_ERR_PARTIAL
				//The uploaded file was only partially uploaded.
				return 193;
				break;
			case 4:
				//UPLOAD_ERR_NO_FILE
				//No file was uploaded.
				return 194;
				break;
			case 6:
				//UPLOAD_ERR_NO_TMP_DIR
				//Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.
				return 196;
				break;
			case 7:
				//UPLOAD_ERR_CANT_WRITE
				//Failed to write file to disk. Introduced in PHP 5.1.0.
				return 197;
				break;
			case 8:
				//UPLOAD_ERR_EXTENSION
				//File upload stopped by extension. Introduced in PHP 5.2.0.
				return 198;
				break;
			default:
				//do nothing
				break;
		}//switch
	}//uploadFilesErrorMessages()
	
	public function checkDBValueAvailability($tableName, $field, $value)
	{
		//check in $tableName if $field with $value already exists.
		//prevents duplicate entries.
		$dbobj = new JDDBase();
	}//checkDBValueAvailability()
	
	//variablesnumber: how many variable should the $_GET array have for this page
	//on error redirect to $redirectpage 
	//$variablestype: what type of variable should each $_GET be
	public function checkGetVariable($variablesNumber,$redirectPage,$variablesType)
	{
		######################
		######################
		if(count($_GET) == $variablesNumber)
		{
			//OK
			reset($_GET);
			while(list($key, $val) = each ($_GET))
			{
				if(!isset($_GET[$key])){ redirects($redirectPage,""); }//if
				else
				{
					//OK
					if($_GET[$key] == ""){ redirects($redirectPage,"",""); }//if
					else
					{
						//OK
						if ( preg_match($variablesType[$key],$_GET[$key])){ redirects($redirectPage,"",""); }//if
						else
						{
							//ALL OK
							$validated_vars[$key] = trim($_GET[$key]);
						}//
					}//
				}//else
			}//while
			return $validated_vars;
		}//if
		else
		{
			Redirects(0,"","");
		}//
		######################
		######################
	}//checkGetVariable
	
}//class
?>
