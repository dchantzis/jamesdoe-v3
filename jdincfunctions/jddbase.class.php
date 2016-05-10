<?php
//
if (!isset($_SESSION["SESSION"])) require ( "jdconfiginc.php");

class JDDBase
{
	function __construct()
	{
		if((isset($_SESSION['ADMIN_LOGIN'])) && ($_SESSION['ADMIN_LOGIN']==TRUE) &&
		(isset($_SESSION['ADMIN_USERNAME'])) && ($_SESSION['ADMIN_USERNAME']!='') &&
		(isset($_SESSION['ADMIN_PASSWORD'])) && ($_SESSION['ADMIN_PASSWORD']!=''))
		{
			//the administrator is logged in
			$loginResult=validateLoginCredentials();
			if($loginResult==1){adminLogout();}//if the login credentials are not found, then logout (happens when the date changes)
		}
		else
		{
			//the administrator is not logged in
			@mysql_connect(JDDB_HOST,JDDB_USER,JDDB_PASSWORD) or dbErrorHandler(804,mysql_error(),'','php','','','JDDBase__construct','true');
      @mysql_select_db(JDDB_DATABASE) or dbErrorHandler(805,mysql_error(),'','php','','','JDDBase__construct','true');
		}
	}//construct()

	function __destruct()
	{
		@mysql_close();//closes the connection to the DB
	}//destruct()

	public function createInsertQuery($DBtableName, $arrayValues)
	{
		$query = "INSERT INTO " . $DBtableName . " (";
		$i=0;
		while (list($key, $val) = each($arrayValues))
		{
			if($i==(count($arrayValues)-1))//don't add a comma after the table field name
			{
				$query .= $key . " ";
			}//
			else {$query .= $key . ", ";}
			$i++;
		}//while
		$query .=") VALUES (";

		reset ($arrayValues);
		$i=0;
		while (list($key, $val) = each($arrayValues))
		{
			if($i==(count($arrayValues)-1))//don't add a comma after the table field value
			{
				$query .= $val . " ";
			}//
			else {$query .= $val . ", ";}
			$i++;
		}//while

		$query .=");";

		$_SESSION['query'] = $query;
		return $query;
	}//createInsertQuery


	public function executeInsertQuery($query)
	{
		@mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','executeInsertQuery','false');
		return mysql_insert_id();
	}//executeInsertQuery()

	public function executeSelectQuery($query)
	{
		//do be created
	}//executeSelectQuery($query)

}//JDDBase class

?>
