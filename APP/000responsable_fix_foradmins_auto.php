<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//$language = 'English'; //Lenguage
//$responsableID = '1'; //Actual Responsable

function autofixresponsable($language,$responsableID){

	$contacts = DBGet("SELECT ID,ASSIGNED_BY_ID FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID and b_uts_crm_contact.UF_CRM_1590674689393 = '".$language."' WHERE ASSIGNED_BY_ID = ".$responsableID." ORDER BY ID LIMIT 50;"); //Query to database

	for($i = 0;$i<=count($contacts);$i++){
		$find_user = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/find_userv2.php?CONTACT_ID=' . $contacts[$i]['ID'] . '&LANGUAGE=' . $language . '&KEY=sjhffgJAFDHGD78V87W8E2DHGUB398YCG93YGDCWCD87CY&SOURCE=&SOURCED=');
		echo $find_user.'</br>';
	}

	return true;

}

echo autofixresponsable('English','1');
echo autofixresponsable('English','22');
echo autofixresponsable('Spanish','1');
echo autofixresponsable('Spanish','22');
//Other users or admins and lenguages to fix the responsables if necesary

?>