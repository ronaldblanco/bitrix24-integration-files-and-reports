<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/app_tags_filter/lib/addtask.php?TITLE=fromurl2&RESPONSIBLE_ID=22&DESCRIPTION=HOLAURL2&contactid=11575

$contactsid = explode(",", $_GET['contactsid']);
foreach ($contactsid as &$id) {
    $id = "C_".$id;
}
unset($id);

$task =  api(
    'tasks.task.addgg' ,
   	[
		'fields' =>
           [
                "TITLE" => $_GET['TITLE'],
                "RESPONSIBLE_ID" => $_GET['RESPONSIBLE_ID'],
				"DESCRIPTION" => $_GET['DESCRIPTION'],
			    "UF_CRM_TASK" => $contactsid //array("C_".$_GET['contactid'])

           ]
   	]);
//var_dump($task['result']['task']['id']);

if(isset($task['result']['task']['id'])){
	echo "{'result':true,'message':'Operation Completed without errors!'}";
}else{
	echo "{'result':false,'message':'Operation Failed, errors found!'}";
}

?>