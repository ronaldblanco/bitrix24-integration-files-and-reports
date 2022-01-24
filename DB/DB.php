<?php
//######################################################
//Simple Functions to Query directly the Database!
//Ronald
//######################################################
define("UPDATE_SYSTEM_VERSION", "9.0.2");
error_reporting(E_ALL & ~E_NOTICE);

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/lib/loader.php");
$application = \Bitrix\Main\HttpApplication::getInstance();
$application->initializeBasicKernel();

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/dbconn.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/classes/".$DBType."/database.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/tools.php");

$DB = new CDatabase;
$DB->debug = $DBDebug;
$DB->Connect($DBHost, $DBName, $DBLogin, $DBPassword);

$errorMessage = "";
$successMessage = "";

//$query = "SELECT VALUE FROM b_option WHERE MODULE_ID='main' AND NAME='".$DB->ForSql($name)."'";
//$query = "SELECT * FROM b_crm_contact LIMIT 100";
//var_dump($query);
function DBGet($query) //need the select query as string and return and array with the information
{
	global $DB;

	//$value = "";
	$rows = $DB->Query($query, false);
	//var_dump($dbQuery);
	$result = array();
	while ($row = $rows->Fetch())
		array_push($result,$row);
	//if ($queryData = $dbQuery->Fetch())
	//$value = $queryData;

	return $result; //array(33) { ["ID"]=> string(3) "504" ["DATE_CREATE"]=> string(19) 
}
//var_dump(DBGet($query));
//$query = "SELECT * FROM b_crm_contact WHERE SOURCE_ID='CALL'";
//$contacts = DBGet("SELECT count('ID'),ASSIGNED_BY_ID FROM b_crm_contact GROUP BY ASSIGNED_BY_ID ORDER BY count('ID') LIMIT 500");
//$contacts = DBGet("SELECT ID,ASSIGNED_BY_ID FROM b_crm_contact LIMIT 50");
//var_dump($contacts);

function DBSet($query) //need the query as string and return a message of the process
{
	global $DB;

	$DB->Query($query);

	return $DB->db_Conn->stat;
}

function DBInsert($query, $values = array()) //need the insert query as string, values as array and return and message of the process
{
	global $DB;

	$fixValues = "VALUES(";
	for ($i = 0; $i <= sizeof($values);$i++) {
		$fixValues = $fixValues . "'" . strval($values[$i]) . "'";
		if($i < sizeof($values) - 1) $fixValues = $fixValues . ",";
		//echo "iteration! ";
	}
	$fixValues = $fixValues . ') ';
	//var_dump($values);
	//var_dump($query . " " . $fixValues);

	$DB->Query($query . " " . $fixValues);

	return $DB->db_Conn->stat;
}
//var_dump(DBInsert($query,$values));
//DBInsert($query,$values);

?>