<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/app_tags_filter/lib/updateresp.php?contactid=&newresp=

$contactsid = explode(",", $_GET['contactsid']);
$newResponsable = $_GET['newresp']; //New Responsable

//echo "{'process':{";
$output = "";
$result = "true";
foreach ($contactsid as $id) {

	//Assing responsable to contact!
	$updatecontact = api (
    'crm.contact.update' ,
   		[
			'ID' => $id,
			'fields' => [
				'ASSIGNED_BY_ID' => $newResponsable
			]
	]);
	//var_dump($updatecontact);

	if(isset($updatecontact['result']) && $updatecontact['result'] == true){
		//echo "{'result':true,'message':'Operation Completed without errors!','contactid':'".$id."'}";
	}else{
		$output = $output. "{'result':false,'message':'Operation Failed, errors found!','contactid':'".$id."'}";
	}

}

echo "{'process':'".$result."','data':{".$output."}}";

?>

