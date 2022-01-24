<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/app_tags_filter/lib/addtag.php?contactsid=&tagid=

$contactsid = explode(",", $_GET['contactsid']);
$newtag = $_GET['tagid'];

//var_dump($_GET);

//echo "{'process':{";
$output = "";
$result = "true";
foreach ($contactsid as $id) {
	//var_dump($id);
	$contact = api (
    'crm.contact.get' ,
   		[
			'id' => $id,
			/*'fields' => [
				'UF_CRM_1591040450352' => array('36','37','38')
			]*/
	]);
	//var_dump($contact['result']['UF_CRM_1591040450352']);

	$newtags = $contact['result']['UF_CRM_1591040450352'];
	if(array_search($newtag, $newtags) === false){ //check if tags does not exist
		//var_dump($newtags);
		//var_dump($newtag);
		array_push($newtags, $newtag); //set new tag if does not have it
	}

	//Add tag to contact!
	$updatecontact = api (
    'crm.contact.update' ,
   		[
			'ID' => $id,
			'fields' => [
				'UF_CRM_1591040450352' => $newtags
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

