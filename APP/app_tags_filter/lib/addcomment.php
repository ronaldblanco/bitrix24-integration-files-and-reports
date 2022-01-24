<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/app_tags_filter/lib/addcomment.php?contactid=&COMMENT=

$contactsid = explode(",", $_GET['contactsid']);
$comment = $_GET['COMMENT'];

//echo "{'process':{";
$output = "";
$result = "true";
foreach ($contactsid as $id) {

	$timeline = api(
    'crm.timeline.comment.add' ,
   	[
		'fields' =>
           [
               "ENTITY_ID" => $id,
               "ENTITY_TYPE" => "contact",
               "COMMENT" => $comment,
           ]
   	]);
	//var_dump($timeline);

	if(isset($timeline['result']) && $timeline['result'] == true){
		//echo "{'result':true,'message':'Operation Completed without errors!','contactid':'".$id."'}";
	}else{
		$output = $output. "{'result':false,'message':'Operation Failed, errors found!','contactid':'".$id."'}";
	}

}

echo "{'process':'".$result."','data':{".$output."}}";

?>