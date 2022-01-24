<?php
//to be able of cron execute!##############
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
$appfolder = "/bitrix/admin/dhhdbw73723934dvrgintegration/APP/";
//#########################################

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");

$contacts_duplicate = api (
    'crm.contact.list' ,
   		[
 	 	 'FILTER' => ['UF_CRM_1595855990' => true],
	 	 'SELECT' => ['ID'] //UF_CRM_1594061489 -> coordinator, UF_CRM_1594061559 -> 3rd responsable
	 	 //'EMAIL'=> [['VALUE' => 'lola@yea.com', 'VALUE_TYPE' => 'WORK']] ,
	 	 //'PHONE'=> [['VALUE' => '123458', 'VALUE_TYPE' => 'WORK']] ,
    	]);
var_dump($contacts_duplicate['result']);	
for($i = 0; $i < count($contacts_duplicate['result']); $i++){
	
	$contact_delete = api (
    	'crm.contact.delete' ,
   			[
				'ID' => $contacts_duplicate['result'][$i]['ID']
    		]);
	var_dump($contact_delete['result']);
}


?>