<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//?language=English&responsable=22&limit=50

$language = $_GET['language']; //Lenguage
$responsableID = $_GET['responsable']; //Actual Responsable
$limit = $_GET['limit']; //Limit

$contacts = DBGet("SELECT ID,ASSIGNED_BY_ID FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID and b_uts_crm_contact.UF_CRM_1590674689393 = '".$language."' WHERE ASSIGNED_BY_ID = ".$responsableID." ORDER BY ID LIMIT ".$limit.";"); //Query to database
//var_dump(count($contacts));

for($i = 0;$i<=count($contacts);$i++){
	$find_user = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/find_user_fast.php?CONTACT_ID=' . $contacts[$i]['ID'] . '&LANGUAGE=' . $language . '&KEY=sjhffgJAFDHGD78V87W8E2DHGUB398YCG93YGDCWCD87CY&SOURCE=&SOURCED=');
	echo $find_user.'</br>';
}

?>

