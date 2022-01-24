<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//?language=English&responsable=22&newresp=81

//$language = "English"; //Lenguage
//$responsableID = '84'; //Actual Responsable
//$newResponsable = '81'; //New Responsable
$language = $_GET['language']; //Lenguage
$responsableID = $_GET['responsable']; //Actual Responsable
$newResponsable = $_GET['newresp']; //New Responsable
$contacts = DBGet("SELECT ID,ASSIGNED_BY_ID FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID and b_uts_crm_contact.UF_CRM_1590674689393 = '".$language."' WHERE ASSIGNED_BY_ID = ".$responsableID." ORDER BY ID LIMIT 300;"); //Query to database
//$contacts = DBGet("SELECT ID,ASSIGNED_BY_ID FROM b_crm_contact WHERE ASSIGNED_BY_ID = ".$responsableID." ORDER BY ID LIMIT 300;"); //Query to database
//var_dump($contacts);

function find_user_info($userid,$users){
	for($i = 0;$i<count($users)+1;$i++){
		$key = array_search($userid, $users[$i]);
		if ($key != false) return $users[$i]['NAME'].' '.$users[$i]['LAST_NAME'];
	}
	return false;
}

function find_user_dep($userid,$users){
	for($i = 0;$i<count($users);$i++){
		$key = array_search($userid, $users[$i]);
		if ($key != false) return $users[$i]['WORK_DEPARTMENT'].'->'.$users[$i]['WORK_POSITION'];
	}
	return false;
}

for($i = 0;$i<=count($contacts);$i++){

	echo $contacts[$i]['ID'].' from '. $responsableID . ' to '. $newResponsable .'</br>';

	//Assing user to contact!
	$newcontacts = api (
    'crm.contact.update' ,
   		[
			'ID' => $contacts[$i]['ID'],
			'fields' => [
				'ASSIGNED_BY_ID' => $newResponsable
			]
		]);

}

?>

