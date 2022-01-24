<?php

date_default_timezone_set('America/New_York');

function redirect($url)
{
    Header("Location: ".$url);
    die();
}

$step = 1; //default 1

if (isset($_REQUEST['config'])) $step = 0;
if (isset($_REQUEST['portal'])) $step = 1;
if (isset($_REQUEST['code'])) $step = 2;
if (isset($_REQUEST['auth'])) $step = 3;

if (isset($_GET['mydata'])){
	$contactid = $_GET['mydata'];
}

/*Vars***********************************/
define('APP_ID', 'local.5fbe6a08e84808.55313790'); // take it from Bitrix24 after adding a new application
define('APP_SECRET_CODE', 'x3xu2Uy4iQxWntf3jmUBV2MKPiXrSdUT75wW7t8G1qUAo31zHX'); // take it from Bitrix24 after adding a new application
define('APP_REG_URL', 'https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/efficiencyreports/klabldjvbqlbjhdwbcwhbcwjhcbq/index.php'); // the same URL you should set when adding a new application in Bitrix24
$domain = 'crm.domain.com';
$server_domain = $domain;
$savetime = 20; //seconds to be sure that access_token it is valid
/*End Vars*******************************/

$btokenRefreshed = null;

$arScope = array('user');

switch ($step) {
    case 1:
        // we need to get the first authorization code from Bitrix24 where our application is _already_ installed
		requestCode($domain, $_GET['from'], $_GET['to']);

        break;

    case 2:
		$arAccessParams = requestAccessToken($_REQUEST['code'], $_REQUEST['server_domain'], $_GET['mydata'], $_GET['mydata1']);
		//var_dump($arAccessParams);
	 break;

	case 3:
		$arAccessParams['access_token'] = $_REQUEST['auth'];
	break;

    default:
        break;
}

$arCurrentB24User = executeREST($arAccessParams['client_endpoint'], 'user.current', array(
),$arAccessParams['access_token']);

//***************************************************************************
/*Execute Rest APIS
		**
		**
		*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$currentUser = $arCurrentB24User['result'];
$userId = $currentUser['ID'];
$user = $currentUser['ID'];

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

$from = date("Y-m-d",strtotime(date("Y-m-d") . ' -7 day'));
$to = date("Y-m-d");
//$nowForLog = date("Y-m-d h:i:s",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));//date("m.d.y");  

if(isset($_GET['from'])) $from = $_GET['from'];
if(isset($_GET['to'])) $to = $_GET['to'];
if(isset($_GET['user'])) $userId = $_GET['user'];

//var_dump($_GET);

function compareByTimeStamp($time1, $time2) 
{

	$time11 = date('M/d/Y H:m:s', strtotime($time1["CREATED"]));
	$time22 = date('M/d/Y H:m:s', strtotime($time2["CREATED"]));
	if(strpos($time1["COMMENT"], ' SMS ') || strpos($time1["COMMENT"], ' MMS ')){
		$time11 = date('M/d/Y H:m:s', strtotime($time1["CREATED"]) - 60 * 60 * 8);
	}
	if(strpos($time2["COMMENT"], ' SMS ') || strpos($time2["COMMENT"], ' MMS ')){
		$time22 = date('M/d/Y H:m:s', strtotime($time2["CREATED"]) - 60 * 60 * 8);
	}

    if ($time11 < $time22) 
        return -1; 
    else if ($time11 > $time22)
        return 1; 
    else
        return 0; 
} 

if(!isset($userId)) $userId = $arCurrentB24User["result"]["ID"];

//if(isset($_GET['from']) && isset($_GET['to'])){
//if(true){

	$user_data = DBGet("SELECT b_user.NAME,b_user.LAST_NAME,b_user.WORK_DEPARTMENT,b_user.WORK_POSITION,b_user.WORK_NOTES,b_user_index.UF_DEPARTMENT_NAME FROM b_user INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user.ID = '".$userId."' ORDER BY b_user.NAME LIMIT 100;");
	//var_dump($user_data);
	$department = $user_data[0]["UF_DEPARTMENT_NAME"];
	$position = $user_data[0]["WORK_POSITION"];
	$maxrows = "250";

	if($position == "Main Manager"){ //General manager, see all salesmans

		$allquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'total' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY b_user.NAME LIMIT ".$maxrows.";";
		$prospectquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'prospects' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1591040450352 LIKE '%i:167;%' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$closedquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'closed' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1601048905 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$noanswerquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'noanswer' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313930 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$nocandidatequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'nocandidate' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313937 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$invalidcontactquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'invalidcontact' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958868 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$donotcontactmequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'donotcontactme' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958922 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$vipquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'donotcontactme' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1613598501 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";

		/*$allquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'total' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY b_user.NAME LIMIT ".$maxrows.";";
		$prospectquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'prospects' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1591040450352 LIKE '%i:167;%' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$closedquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'closed' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1601048905 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$noanswerquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'noanswer' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313930 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$nocandidatequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'nocandidate' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313937 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$invalidcontactquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'invalidcontact' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958868 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$donotcontactmequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'donotcontactme' as query,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958922 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
*/
	}

	if($position == "Manager"){ //Department manager, see department salesmans

		$allquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'total' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY b_user.NAME LIMIT ".$maxrows.";";
		$prospectquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'prospects' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1591040450352 LIKE '%i:167;%' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$closedquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'closed' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1601048905 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$noanswerquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'noanswer' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313930 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$nocandidatequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'nocandidate' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313937 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$invalidcontactquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'invalidcontact' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958868 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$donotcontactmequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'donotcontactme' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958922 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$vipquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'donotcontactme' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1613598501 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";

		/*$allquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'total' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY b_user.NAME LIMIT ".$maxrows.";";
		$prospectquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'prospects' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1591040450352 LIKE '%i:167;%' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$closedquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'closed' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1601048905 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$noanswerquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'noanswer' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313930 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$nocandidatequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'nocandidate' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313937 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$invalidcontactquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'invalidcontact' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958868 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$donotcontactmequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'donotcontactme' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958922 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user_index.UF_DEPARTMENT_NAME = '".$department."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
*/
	}

	if($position != "Main Manager" && $position != "Manager"){ //Normal user or salesman, see himself only

		$allquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'total' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY b_user.NAME LIMIT ".$maxrows.";";
		$prospectquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'prospects' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1591040450352 LIKE '%i:167;%' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$closedquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'closed' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1601048905 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$noanswerquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'noanswer' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313930 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$nocandidatequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'nocandidate' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313937 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$invalidcontactquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'invalidcontact' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958868 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$donotcontactmequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'donotcontactme' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958922 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$vipquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,CONVERT_TZ(MIN(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as older_contact,CONVERT_TZ(MAX(b_crm_contact.DATE_CREATE),'+00:00','-05:00') as newer_contact,'donotcontactme' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1613598501 = '1' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";

		/*$allquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'total' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY b_user.NAME LIMIT ".$maxrows.";";
		$prospectquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'prospects' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1591040450352 LIKE '%i:167;%' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$closedquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'closed' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1601048905 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$noanswerquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'noanswer' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313930 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$nocandidatequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'nocandidate' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1606313937 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$invalidcontactquery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'invalidcontact' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958868 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
		$donotcontactmequery = "SELECT COUNT(b_crm_contact.ID) as contacts,ASSIGNED_BY_ID,b_user.PERSONAL_ICQ as today_assigned,MIN(b_crm_contact.DATE_CREATE) as older_contact,MAX(b_crm_contact.DATE_CREATE) as newer_contact,'donotcontactme' as query FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND UF_CRM_1607958922 = '1' AND b_crm_contact.DATE_CREATE between '".$from." 00:00:00' and '".$to." 23:59:59' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_user.ID = '".$userId."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT ".$maxrows.";";
*/
	}

	$allcontacts_count_assigned_by_user = DBGet($allquery);
	$prospect_count_assigned_by_user = DBGet($prospectquery);
	$closed_count_assigned_by_user = DBGet($closedquery);
	$noanswer_count_assigned_by_user = DBGet($noanswerquery);
	$nocandidate_count_assigned_by_user = DBGet($nocandidatequery);
	$invalidcontact_count_assigned_by_user = DBGet($invalidcontactquery);
	$donotcontactme_count_assigned_by_user = DBGet($donotcontactmequery);
	$vip_count_assigned_by_user = DBGet($vipquery);
	//var_dump($allcontacts_count_assigned_by_user);

	$users = DBGet("select ID,NAME,LAST_NAME from b_user limit 250;");

	$totaltotal = 0;
	$totalprospect = 0;
	$totalclosed = 0;
	$totalnoanswer = 0;
	$totalnocandidate = 0;
	$totalinvalidcontact = 0;
	$totaldonotcontactme = 0;
	$totalvip = 0;

	$buildarray = array();
	foreach($allcontacts_count_assigned_by_user as $user){

		$prospect = arrayfinder_id($prospect_count_assigned_by_user,$user["ASSIGNED_BY_ID"]);
		$closed = arrayfinder_id($closed_count_assigned_by_user,$user["ASSIGNED_BY_ID"]);
		$noanswer = arrayfinder_id($noanswer_count_assigned_by_user,$user["ASSIGNED_BY_ID"]);
		$nocandidate = arrayfinder_id($nocandidate_count_assigned_by_user,$user["ASSIGNED_BY_ID"]);
		$invalidcontact = arrayfinder_id($invalidcontact_count_assigned_by_user,$user["ASSIGNED_BY_ID"]);
		$donotcontactme = arrayfinder_id($donotcontactme_count_assigned_by_user,$user["ASSIGNED_BY_ID"]);
		$vip = arrayfinder_id($vip_count_assigned_by_user,$user["ASSIGNED_BY_ID"]);

		if($prospect == false || $prospect['contacts'] == 1) $prospect['contacts'] = 0; //ignoring Round Robin contact!
		else {
			$prospect['contacts'] = $prospect['contacts'] - 1; //substracting Round Robin contact
			$user['contacts'] = $user['contacts'] - 1; //substracting from total
		} 

		if($closed == false) $closed['contacts'] = 0;
		if($noanswer == false) $noanswer['contacts'] = 0;
		if($nocandidate == false) $nocandidate['contacts'] = 0;
		if($invalidcontact == false) $invalidcontact['contacts'] = 0;
		if($donotcontactme == false) $donotcontactme['contacts'] = 0;
		if($vip == false) $vip['contacts'] = 0;

		array_push($buildarray,array("ID" => $user["ASSIGNED_BY_ID"],"fullname" => getusername($user["ASSIGNED_BY_ID"]),"total" => $user['contacts'],"prospects" => $prospect['contacts'], "closed" => $closed['contacts'],"noanswer" => $noanswer['contacts'],"nocandidate" => $nocandidate['contacts'],"invalidcontact" => $invalidcontact['contacts'],"donotcontactme" => $donotcontactme['contacts'],"vip" => $vip['contacts']));

		$totaltotal = $totaltotal + ($user['contacts']);
		$totalprospect = $totalprospect + $prospect['contacts'];
		$totalclosed = $totalclosed + $closed['contacts'];
		$totalnoanswer = $totalnoanswer + $noanswer['contacts'];
		$totalnocandidate = $totalnocandidate + $nocandidate['contacts'];
		$totalinvalidcontact = $totalinvalidcontact + $invalidcontact['contacts'];
		$totaldonotcontactme = $totaldonotcontactme + $donotcontactme['contacts'];
		$totalvip = $totalvip + $vip['contacts'];

		if($position == "Main Manager" && $user['UF_DEPARTMENT_NAME'] == "Equipo Peru"){
			$dep1name = "Dep. Equipo Peru";
			$dep1total = $dep1total + ($user['contacts']);
			$dep1prospect = $dep1prospect + $prospect['contacts'];
			$dep1closed = $dep1closed + $closed['contacts'];
			$dep1noanswer = $dep1noanswer + $noanswer['contacts'];
			$dep1nocandidate = $dep1nocandidate + $nocandidate['contacts'];
			$dep1invalidcontact = $dep1invalidcontact + $invalidcontact['contacts'];
			$dep1donotcontactme = $dep1donotcontactme + $donotcontactme['contacts'];
			$dep1vip = $dep1vip + $vip['contacts'];
		}

		if($position == "Main Manager" && $user['UF_DEPARTMENT_NAME'] == "Equipo Karel"){
			$dep2name = "Dep. Equipo Karel";
			$dep2total = $dep2total + ($user['contacts']);
			$dep2prospect = $dep2prospect + $prospect['contacts'];
			$dep2closed = $dep2closed + $closed['contacts'];
			$dep2noanswer = $dep2noanswer + $noanswer['contacts'];
			$dep2nocandidate = $dep2nocandidate + $nocandidate['contacts'];
			$dep2invalidcontact = $dep2invalidcontact + $invalidcontact['contacts'];
			$dep2donotcontactme = $dep2donotcontactme + $donotcontactme['contacts'];
			$dep2vip = $dep2vip + $vip['contacts'];
		}

		if($position == "Main Manager" && $user['UF_DEPARTMENT_NAME'] == "Equipo Daynelis "){
			$dep3name = "Dep. Equipo Daynelis";
			$dep3total = $dep3total + ($user['contacts']);
			$dep3prospect = $dep3prospect + $prospect['contacts'];
			$dep3closed = $dep3closed + $closed['contacts'];
			$dep3noanswer = $dep3noanswer + $noanswer['contacts'];
			$dep3nocandidate = $dep3nocandidate + $nocandidate['contacts'];
			$dep3invalidcontact = $dep3invalidcontact + $invalidcontact['contacts'];
			$dep3donotcontactme = $dep3donotcontactme + $donotcontactme['contacts'];
			$dep3vip = $dep3vip + $vip['contacts'];
		}

		if($position == "Main Manager" && $user['UF_DEPARTMENT_NAME'] == "Equipo Odleny"){
			$dep4name = "Dep. Equipo Odleny";
			$dep4total = $dep4total + ($user['contacts']);
			$dep4prospect = $dep4prospect + $prospect['contacts'];
			$dep4closed = $dep4closed + $closed['contacts'];
			$dep4noanswer = $dep4noanswer + $noanswer['contacts'];
			$dep4nocandidate = $dep4nocandidate + $nocandidate['contacts'];
			$dep4invalidcontact = $dep4invalidcontact + $invalidcontact['contacts'];
			$dep4donotcontactme = $dep4donotcontactme + $donotcontactme['contacts'];
			$dep4vip = $dep4vip + $vip['contacts'];
		}

		if($position == "Main Manager" && $user['UF_DEPARTMENT_NAME'] == "Equipo Regla"){
			$dep5name = "Dep. Equipo Regla";
			$dep5total = $dep5total + ($user['contacts']);
			$dep5prospect = $dep5prospect + $prospect['contacts'];
			$dep5closed = $dep5closed + $closed['contacts'];
			$dep5noanswer = $dep5noanswer + $noanswer['contacts'];
			$dep5nocandidate = $dep5nocandidate + $nocandidate['contacts'];
			$dep5invalidcontact = $dep5invalidcontact + $invalidcontact['contacts'];
			$dep5donotcontactme = $dep5donotcontactme + $donotcontactme['contacts'];
			$dep5vip = $dep5vip + $vip['contacts'];
		}


	}

	if($position == "Main Manager"){

		array_push($buildarray,array("ID" => 'Dep1',"fullname" => $dep1name,"total" => $dep1total,"prospects" => $dep1prospect, "closed" => $dep1closed, "noanswer" => $dep1noanswer,"nocandidate" => $dep1nocandidate,"invalidcontact" => $dep1invalidcontact,"donotcontactme" => $dep1donotcontactme,"vip" => $dep1vip));
		array_push($buildarray,array("ID" => 'Dep2',"fullname" => $dep2name,"total" => $dep2total,"prospects" => $dep2prospect, "closed" => $dep2closed, "noanswer" => $dep2noanswer,"nocandidate" => $dep2nocandidate,"invalidcontact" => $dep2invalidcontact,"donotcontactme" => $dep2donotcontactme,"vip" => $dep2vip));
		array_push($buildarray,array("ID" => 'Dep3',"fullname" => $dep3name,"total" => $dep3total,"prospects" => $dep3prospect, "closed" => $dep3closed, "noanswer" => $dep3noanswer,"nocandidate" => $dep3nocandidate,"invalidcontact" => $dep3invalidcontact,"donotcontactme" => $dep3donotcontactme,"vip" => $dep3vip));
		array_push($buildarray,array("ID" => 'Dep4',"fullname" => $dep4name,"total" => $dep4total,"prospects" => $dep4prospect, "closed" => $dep4closed, "noanswer" => $dep4noanswer,"nocandidate" => $dep4nocandidate,"invalidcontact" => $dep4invalidcontact,"donotcontactme" => $dep4donotcontactme,"vip" => $dep4vip));
		array_push($buildarray,array("ID" => 'Dep5',"fullname" => $dep5name,"total" => $dep5total,"prospects" => $dep5prospect, "closed" => $dep5closed, "noanswer" => $dep5noanswer,"nocandidate" => $dep5nocandidate,"invalidcontact" => $dep5invalidcontact,"donotcontactme" => $dep5donotcontactme,"vip" => $dep5vip));

	}

	array_push($buildarray,array("ID" => 'total',"fullname" => 'Total Period',"total" => $totaltotal,"prospects" => $totalprospect, "closed" => $totalclosed, "noanswer" => $totalnoanswer,"nocandidate" => $totalnocandidate,"invalidcontact" => $totalinvalidcontact,"donotcontactme" => $totaldonotcontactme,"vip" => $totalvip));

	$contacts['result'] = $buildarray;

	//} else {
	//$contacts['result'] = array();
	//$users = array();
	//}

//var_dump($contacts['result']);

function getusername($id){
	$result = false;
	foreach($GLOBALS["users"] as $user){
		if($user['ID'] == $id) $result = $user['NAME'] . " " . $user['LAST_NAME'];
	}
	//if($result == "Admin Swaypc") $result = "System";
	return $result;
}

function arrayfinder_id($newarray,$id){
	foreach($newarray as $row){
		if($row["ASSIGNED_BY_ID"] == $id){
			return $row;
		}
	}
	return false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Efficiency Report!</title>
	<link rel="stylesheet" href="css/timeline.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">

	<style type="text/css">/* Chart.js */
		@keyframes chartjs-render-animation{from{opacity:.99}to{opacity:1}}.chartjs-render-monitor{animation:chartjs-render-animation 1ms}.chartjs-size-monitor,.chartjs-size-monitor-expand,.chartjs-size-monitor-shrink{position:absolute;direction:ltr;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1}.chartjs-size-monitor-expand>div{position:absolute;width:1000000px;height:1000000px;left:0;top:0}.chartjs-size-monitor-shrink>div{position:absolute;width:200%;height:200%;left:0;top:0}
	</style>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

	<!--<script src="https://www.chartjs.org/dist/2.9.4/Chart.min.js" ></script>-->
	<script src="./node_modules/chartjs/dist/chart.js"></script>
	<!--<script src="https://www.chartjs.org/samples/latest/utils.js" ></script>-->
	<!--<script src="./utils.js" ></script>-->


	<style>
  		h2 {color:blue;}
  		p {color:green;}
	</style>
</head>
<body>
<?php
	if ($step == 0) {
?>
	<div class="container-fluid">
	<div class="alert alert-primary" role="alert">
		<h2>It is posible your aplication it is not configure to work whit Bitrix24 yet:</h2>
	</div>
    <form action="config.php" method="post" styles>
		<div class="form-group">
			<label>APP_ID:</label>
        	<input type="text" class="form-control" name="app_id" placeholder="APP_ID" value='<?php echo $config['app_id']; ?>'>
			<small id="ID" class="form-text text-muted">ID de la aplicacion en Bitrix24.</small>
		</div>
		<div class="form-group">
			<label>APP_SECRET:</label><br>
			<input type="text" class="form-control" name="app_secret" placeholder="APP_SECRET" value='<?php echo $config['app_secret']; ?>'>
			<small id="SECRET" class="form-text text-muted">Secret de la aplicacion en Bitrix24.</small>
		</div>
		<div class="form-group">
			<label>APP_REDIRECT_URL:</label><br>
			<input type="text" class="form-control" name="app_redirect_url" placeholder="APP_REDIRECT_URL" value='<?php echo $config['app_redirect_url']; ?>'>
			<small id="URL" class="form-text text-muted">URL de redireccion en Bitrix24.</small>
		</div>
		<div class="form-group">
			<label>BITRIX_DOMAIN.COM:</label><br>
			<input type="text" class="form-control" name="bitrix_domain" placeholder="BITRIX_DOMAIN.COM" value='<?php echo $config['bitrix_domain']; ?>'>
			<small id="DOMAIN" class="form-text text-muted">Domain of Bitrix24 server.</small>
		</div>
        <input type="submit" class="btn btn-primary" value="Submit">
    </form>
	</div>
<?php
} elseif ($step == 1) {
	echo '<div class="alert alert-primary" role="alert">';
	echo 'step 1 (redirecting to Bitrix24):<br/>';
	echo '</div>';
} elseif ($step == 2){
		//echo '<div class="alert alert-primary" role="alert">';
		//echo "Logged User: " . $arCurrentB24User["result"]["NAME"] . " " . $arCurrentB24User["result"]["LAST_NAME"] . ' <br/>';
		//echo '</div>';
		//echo '<div class="alert alert-success" role="alert">';
		//echo 'Bellow you will find your contacts for coordination:<br/>';
	//print_r($contacts['result']);
		//echo '</div>';
	//var_dump($arCurrentB24User["result"]);
		//$timelines = array();
?>

<!--<h4>Efficiency Reports Application:</h4>-->

<div>
	</br>
	<p style="padding-left: 10px;">
	FROM &nbsp;<input type="text" name="from" id="from"  value="<?php echo $from?>">  TO   &nbsp; &nbsp;  &nbsp; &nbsp;<input type="text" name="to" id="to"  value="<?php echo $to?>"> <button type="button" id="date">Update Data!</button></p>
</div>

<div id="mainbody" style="display: flex;overflow: hidden;padding-left: 10px;">

<ul class="nav flex-column nav-pills" id="myTab" role="tablist" aria-orientation="vertical" style="width: 300px;height: 650px;overflow-x: auto;flex-direction: row !important;">
	<?php $active = false; ?>
	<?php foreach ($contacts['result'] as $row): array_map('htmlentities', $row);
		if($active == false){
			$class = "nav-link active";
			$arial = "true";
			$active = true;
		} else {
			$class = "nav-link";
			$arial = "false";
		} 
	?>

	<?php
		$incourse = $row['total'] - $row['nocandidate'] - $row['closed'] - $row['prospects'] - $row['invalidcontact'] - $row['donotcontactme'];
		if($incourse == 0) $closeporcentage = 0;
		else $closeporcentage = round($row['closed'] * (100/$incourse), 2); //incourse % of close leads
		if($row['total'] == 0) $totalcloseporcentage = 0;
		else $totalcloseporcentage = round($row['closed'] * (100/$row['total']), 2); //total % of close leads
	?>
	
  <li class="nav-item" style="width: 100%;">
    <a class="<?php echo $class;?>" id="<?php echo $row['ID'];?>-tab" data-toggle="tab" href="#<?php echo $row['ID'];?>" role="tab" aria-controls="<?php echo $row['ID'];?>"
      aria-selected="<?php echo $arial;?>"><?php echo $row['fullname'];?></a>
	<span class="badge badge-primary badge-pill"><?php echo $totalcloseporcentage;?>%</span>
	<span class="badge badge-primary badge-pill"><?php echo $closeporcentage;?>%</span>
  </li>
	<?php endforeach; ?>
  
</ul>

<div class="tab-content" id="myTabContent" style="width: 100%;">
	
	<?php $active = false; ?>
	<?php foreach ($contacts['result'] as $row): array_map('htmlentities', $row);
		if($active == false){
			$class = "tab-pane fade show active";
			$active = true;
		} else $class = "tab-pane fade";
	?>
	
  <div class="<?php echo $class;?>" id="<?php echo $row['ID'];?>" role="tabpanel" aria-labelledby="<?php echo $row['ID'];?>-tab">

<div class="">
    <div class="row" style="height: auto;">
        <div class="col-md-12" style="max-width: 95% !important;margin-left: 15px;">
            <div class="card">
                <div class="card-body" style="height: 650px;">
					
					<?php //foreach ($timelines as $row): array_map('htmlentities', $row);

					?>
					
				<!--<ul class="nav nav-tabs" id="myTab" role="tablist">	
					

					<li class="nav-item">
    <a class="nav-link active" id="SMS-tab" data-toggle="tab" href="#SMS" role="tab" aria-controls="SMS"
      aria-selected="false">SMS</a>
  </li>-->

					<div class="tab-content" id="myTabContent" style="width: 100%">

				<div class="tab-pane fade show active" id="SMS" role="tabpanel" aria-labelledby="SMS-tab">
					<!--<a href="https://crm.domain.com/company/personal/user/<?php echo $row['ID'];?>/" target="_blank"><?php echo $row['ID'].":".$row['fullname'];?></a>-->

					<!--<form action="https://techcnet.com/SMS/qwteuuo856fg/sendSMS.php" method="post" styles>
						<div class="form-group">
							<label>SMS:</label>
							<input type="hidden" class="form-control" name="message_to" value='<?php echo $row['contactphone'];?>'>
							<input type="hidden" class="form-control" name="bindings[0][OWNER_ID]" value='<?php echo $row['IDresponsable'];?>'>
							<input type="hidden" class="form-control" name="auth[domain]" value='crm.domain.com'>
							<input type="hidden" class="form-control" name="auth[member_id]" value='959278128ab5e919e0ff8b9c66a553e3'>
							<input type="hidden" class="form-control" name="auth[application_token]" value='9041496a10dd6b2e99ef8581d3f8625c'>
							<input type="hidden" class="form-control" name="redirect" value='https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/smsapp/dhfskjfhkajghkghahbhagbaldb/appSMSRedirect.php?contactID=<?php echo $row['contactid'];?>&coordinatorID=<?php echo $row['IDresponsable'];?>'>
							<textarea class="form-control" name="message_body" placeholder="SMS text" value='' rows="3" style="width: 100%"></textarea>
							<small id="SMS" class="form-text text-muted">SMS Text to send!</small>
						</div>
        				<input type="submit" class="btn btn-primary" value="Send">
    				</form></br>-->

				</div>

						
					</div>
					

				<!--</ul>-->	

<?php
		$incourse = $row['total'] - $row['nocandidate'] - $row['closed'] - $row['prospects'] - $row['invalidcontact'] - $row['donotcontactme'];
		if($incourse == 0) $closeporcentage = 0;
		else $closeporcentage = round($row['closed'] * (100/$incourse), 2);
		if($row['total'] == 0) $totalcloseporcentage = 0;
		else $totalcloseporcentage = round($row['closed'] * (100/$row['total']), 2); //total % of close leads
		//number_format("1000000",2,",",".")
	?>

					<h6 class="card-title"><a href="https://crm.domain.com/company/personal/user/<?php echo $row['ID'];?>/" target="_blank"><?php echo $row['ID'].":".$row['fullname'];?></a><?php echo "; Total ".number_format($row['total'])."; Prospects ".number_format($row['prospects'])."; Closed ".number_format($row['closed'])."; in Progress ".number_format($incourse)."; no Candidate ".number_format($row['nocandidate'])."; Invalid ".number_format($row['invalidcontact'])."; Do not Contact ".number_format($row['donotcontactme'])." and VIP ".number_format($row['vip'])."!";?></h6>

					<?php

		/*if($closeporcentage < 20 && $row['prospects'] > 1){
							echo "<div class='alert alert-danger' role='alert'>";
							echo $row['fullname']." Close Efficiency it is bellow 20% and have pending prospects!";
							echo "</div>";
						}
						if($closeporcentage < 20 && $row['prospects'] == 1){
							echo "<div class='alert alert-danger' role='alert'>";
							echo $row['fullname']." Close Efficiency it is bellow 20%!";
							echo "</div>";
						}
						if($closeporcentage >= 20 && $row['prospects'] > 1){
							echo "<div class='alert alert-warnning' role='alert'>";
							echo $row['fullname']." have pending prospects!";
							echo "</div>";
}*/
						/*if($closeporcentage >= 20 && $row['prospects'] == 1){
							echo "<div class='alert alert-success' role='alert'>";
							echo $row['fullname']." have Good Close Efficiency!";
							echo "</div>";
						}*/
					?>

					<small>Total Close %</small>
					<div class="progress">
  						<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $totalcloseporcentage;?>%"><?php echo $totalcloseporcentage;?>%</div>
					</div>
					<small>Valid Contacts Close Efficiency %</small>
					<div class="progress">
						<div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $closeporcentage;?>%"><?php echo $closeporcentage;?>%</div>
					</div>

					<!--<div class="alert alert-info" role="alert">
  						User total count of contacts for this periot of time it is <?php echo $row['total'];?>!
					</div>-->

					<div id="canvas-holder" style="width:500px"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
						<canvas id="<?php echo "chart-area".$row['ID'];?>" style="display: block; height: 200px; width: 400px;" width="300" height="200" class="chartjs-render-monitor"></canvas>
					</div>


					<!--<div class="chart-editor" data-v-365c20ab><div class="chart-view" data-v-365c20ab><canvas></canvas></div></div>-->


					<!--<button id="randomizeData">Randomize Data</button>
					<button id="addDataset">Add Dataset</button>
					<button id="removeDataset">Remove Dataset</button>-->

					<script>

		var chart = "<?php echo 'chart-area'.$row['ID'];?>";
		var username = "<?php echo $row['fullname'];?>";

		var totals = "<?php echo $row['total'];?>";
		var prospects = "<?php echo $row['prospects'];?>";
		var closedval = "<?php echo $row['closed'];?>";
		var noanswer = "<?php echo $row['noanswer'];?>";
		var nocandidate = "<?php echo $row['nocandidate'];?>";

		var invalidcontact = "<?php echo $row['invalidcontact'];?>";
		var donotcontactme = "<?php echo $row['donotcontactme'];?>";
		var vip = "<?php echo $row['vip'];?>";

						//console.log("<?php echo $row;?>");
						/*console.log(prospects);
		console.log(closedval);
		console.log(noanswer);
		console.log(nocandidate);
		console.log(totals);
console.log("###############");*/

						//var closedporcentaje = closed * (100/totals);

		var running = (((((totals - prospects) - closedval) - nocandidate) - noanswer) - invalidcontact) - donotcontactme;

		var scalingFactor = function(value) {
			return Math.round(value);
		};

		var config = {
			type: 'pie',
			data: {
				datasets: [{
					data: [
						scalingFactor(prospects),
						scalingFactor(noanswer),
						scalingFactor(nocandidate),
						scalingFactor(running),
						scalingFactor(closedval),
						scalingFactor(invalidcontact),
						scalingFactor(donotcontactme),
						scalingFactor(vip),
					],
					backgroundColor: [
						'rgba(255, 99, 132, 0.2)',//red
						'rgba(54, 162, 235, 0.2)',//blue
						//window.chartColors.yellow,
						'rgba(153, 102, 255, 0.2)',//purple
						//window.chartColors.blue,
						'rgba(255, 206, 86, 0.2)',//yellow
						'rgba(75, 192, 192, 0.2)',//green
						'rgba(255, 159, 64, 0.2)',//orange
						'rgba(198, 184, 184, 0.2)',//grey
						'rgba(100, 162, 235, 0.2)',
					],
					label: username
				}],
				labels: [
					'Prospects',
					'No Answer',
					'No Candidate',
					'In Progress',
					'Closed',
					'Invalid',
					'Do not Contact',
					'VIP'
				]
			},
			options: {
				responsive: true
			}
		};

						//window.onload = function() {
						/*var ctx = document.getElementById(chart).getContext('2d');
						window.myPie = new Chart(ctx, config);*/

			var ctx = document.getElementById(chart).getContext('2d');
			var mychart = new Chart(ctx, config);
						//window.myPie = new Chart(ctx, config);
						//};

					</script>

                    <!--<div id="content" style="height: 400px; overflow-y: scroll;">
                        <ul class="timeline" id="timeline" style="max-width: 60%;">
							
							<?php 
								$reverse = array_reverse($row[0]);
								foreach ($reverse as $subrow): array_map('htmlentities', $subrow);

								if(strpos($subrow["COMMENT"], ' SMS ') || strpos($subrow["COMMENT"], ' MMS ') || strpos($subrow["SUBJECT"], ' SMS ')){
									$datadate = date('M/d/Y H:m:s', strtotime($subrow["CREATED"]) - 60 * 60 * 8);
									if(strpos($subrow["SUBJECT"], ' SMS ')) $datadate = date('M/d/Y H:m:s', strtotime($subrow["CREATED"]));
							?>
							
                            <li class="event" data-date="<?php echo $datadate;?>">
                                <!--<h3>ID:<?php echo $subrow["ID"];?></h3>-->
								<h3><?php echo "Done by ".getusername($subrow["AUTHOR_ID"],$users);?></h3>
								<p><?php echo $subrow["SUBJECT"];?></p>
								<p><?php echo $subrow["DESCRIPTION"];?></p>
                                <p><?php echo $subrow["COMMENT"];?></p>

                            </li>
                          
						<?php 
							}
							endforeach; 
						?>
						
						</ul>
                    </div>-->

					<?php //endforeach; ?>
				
				</div>
            </div>
        </div>
    </div>
</div>

	</div>
	
	<?php endforeach; ?>
 
</div>	
	
<?php
		//var_dump($userId);	
}
?>
	</div>

	<script>
		var auth = "<?php echo $arAccessParams['access_token']; ?>";
		var refresh = "<?php echo $arAccessParams['refresh_token']; ?>";
		var baseUrl = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/efficiencyreports/klabldjvbqlbjhdwbcwhbcwjhcbq/index.php?";
		var user = "<?php echo $userId;?>";

		//console.log(user);

		$('#date').click(function() {
			var queryParams = new URLSearchParams(window.location.search);
			//queryParams.set("date", this.value);
			queryParams.delete("from");
			queryParams.delete("to");
			//queryParams.delete("user");
			if($('#from').val() <= $('#to').val() && user != "Array"){
				queryParams.set("from", $('#from').val());
				queryParams.set("to", $('#to').val());
				queryParams.set("user", user);

				var url = baseUrl + queryParams.toString();
				window.location = url;
			} else if($('#from').val() <= $('#to').val() && user == "Array"){
				queryParams.set("from", $('#from').val());
				queryParams.set("to", $('#to').val());
				//queryParams.set("user", user);

				var url = baseUrl + queryParams.toString();
				window.location = url;
			} else alert( "From date must be less than to date!" );

		});

		$( function() {
    		$( "#from" ).datepicker({ dateFormat: 'yy-mm-dd' });

  		} );

 		$( function() {
    		$( "#to" ).datepicker({ dateFormat: 'yy-mm-dd' });

  		} );
	</script>

	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->

	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" ></script>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" ></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js" ></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" ></script>


	<script>

		document.getElementById('randomizeData').addEventListener('click', function() {
			config.data.datasets.forEach(function(dataset) {
				dataset.data = dataset.data.map(function() {
					return randomScalingFactor();
				});
			});

			window.myPie.update();
		});

		var colorNames = Object.keys(window.chartColors);
		document.getElementById('addDataset').addEventListener('click', function() {
			var newDataset = {
				backgroundColor: [],
				data: [],
				label: 'New dataset ' + config.data.datasets.length,
			};

			for (var index = 0; index < config.data.labels.length; ++index) {
				newDataset.data.push(randomScalingFactor());

				var colorName = colorNames[index % colorNames.length];
				var newColor = window.chartColors[colorName];
				newDataset.backgroundColor.push(newColor);
			}

			config.data.datasets.push(newDataset);
			window.myPie.update();
		});

		document.getElementById('removeDataset').addEventListener('click', function() {
			config.data.datasets.splice(0, 1);
			window.myPie.update();
		});
	</script>

</body>
</html>

<?php

//***************************************************************************

function executeHTTPRequest ($queryUrl, array $params = array()) {
    $result = array();
    $queryData = http_build_query($params);

	if($params != array()){
		//var_dump($queryUrl.$queryData);
		$result = json_decode(file_get_contents($queryUrl.$queryData),true);
		
	} 
	else{
		//var_dump($queryUrl);
		$result = json_decode(file_get_contents($queryUrl),true);
		//string(249) "https://oauth.bitrix.info/oauth/token/?grant_type=authorization_code&client_id=local.5f50feb3287d4397490808&client_secret=meRlN1mAM1WdSMfoUQlTu2QnD8M4sAOnRQldszqlkDz89qSWsr&code=8e12515f004c6f990047c39300000016909d033b31d7a0c3a9f47bf7af3004ce550221" array(11) { ["access_token"]=> string(70) "8120515f004c6f990047c39300000016909d03b42034e2621671bac2c59eb6236c88a3" ["expires"]=> int1599152257) ["expires_in"]=> int(3600) ["scope"]=> string(3) "app" ["domain"]=> string(17) "oauth.bitrix.info" ["server_endpoint"]=> string(31) "https://oauth.bitrix.info/rest/" ["status"]=> string(1) "L" ["client_endpoint"]=> string(39) "https://crm.domain.com/rest/" ["member_id"]=> string(32) "959278128ab5e919e0ff8b9c66a553e3" ["user_id"]=> int(22) ["refresh_token"]=> string(70) "719f785f004c6f990047c39300000016909d039833c14e03ee6ef6ca8ca5f6e4b1fa78" } string(131) "https://crm.domain.com/rest/user.current.jsonauth=8120515f004c6f990047c39300000016909d03b42034e2621671bac2c59eb6236c88a3"
		 
	} 
	//var_dump($result);
	return $result;
}


function requestCode ($domain, $add, $add1) {
    $url = 'https://' . $domain . '/oauth/authorize/' .
        '?client_id=' . urlencode(APP_ID).'&mydata='.$add.'&mydata1='.$add1;
    redirect($url);
}

function requestAccessToken ($code, $server_domain, $add, $add1) {
    $url = 'https://' . $server_domain . '/oauth/token/?' .
        'grant_type=authorization_code'.
        '&client_id='.urlencode(APP_ID).
        '&client_secret='.urlencode(APP_SECRET_CODE).
        '&code='.urlencode($code).'&mydata='.$add.'&mydata1='.$add1;
    return executeHTTPRequest($url);
}

function executeREST ($rest_url, $method, $params, $access_token) {
	//echo 'executeRest!';
    $url = $rest_url.$method.'.json?';
    return executeHTTPRequest($url, array_merge($params, array("auth" => $access_token)));
}

?>