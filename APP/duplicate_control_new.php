<?php

date_default_timezone_set('America/New_York');
//Only for TESTs!
$myfile = fopen("logs/000duplicate_control_log.txt", "a+") or die("Unable to open file!");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

if(isset($_GET['KEY']) && $_GET['KEY'] === 'sjhffgJAFDHGD78V87W8E2DHGUB398YCG93YGDCWCD87CY'){

$contactid = isset($_GET['CONTACT_ID']) ? $_GET['CONTACT_ID'] : 0;
$phone = isset($_GET['PHONE']) ? $_GET['PHONE'] : '0000000000';
$queryphone = str_replace("+1","",str_replace(" ","",str_replace("-","",str_replace("(","",str_replace(")","",$phone)))));

$duplicate_phones = DBGet("select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid,MIN(ENTITY_ID) as firstid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated where mycount > 1 and firstid <> lastid and (myVALUE like '%".$queryphone."' or myVALUE = '".$queryphone."' or myVALUE = '1".$queryphone."') limit 2;"); //Query to database new

if(isset($duplicate_phones[0]["lastid"])){

	$firstcontact = getfirstcontact($duplicate_phones[0]["firstid"]); //Get oldest contact
	$contactLink = "<a href='https://crm.domain.com/crm/contact/details/".$firstcontact['ID']."/'>". $firstcontact['NAME'] . " " . $firstcontact['LAST_NAME']. "</a>";
	$updatefirstcontact = updatefirstcontact($duplicate_phones[0]["firstid"],$firstcontact['UF_CRM_1604333683'] + 1); //Add 1 to duplicate count
	$updatelastcontact = updatelastcontact($duplicate_phones[0]["lastid"]); //Duplicate set to true for must recent contact
	$responsablemanager = getresponsablemanager($firstcontact['ASSIGNED_BY_ID']);
	$message = 'A duplicate contact was found with phone:'.$phone.' used by contact '. $contactLink .' and it was market to be deleted!';
	$sentnotificationtomanager = setmanagermessage($responsablemanager['ID'],$message);

	echo "Contact it is duplicate and was market!";
	fwrite($myfile, date("Y-m-d h:i:sa").";Phone:".$phone.";ContactID:".$duplicate_phones[0]["lastid"].";Marked to be deleted!".PHP_EOL);

} else {
	echo "Contact it is not duplicate!";
}
	

} else {
	echo "Access Denied!";
}
	
//Only for tests!
fclose($myfile);

function getfirstcontact($id){
	$firstcontact = api (
    	'crm.contact.get',
   			[
				'ID' => $id
 	 	 	]);
	return $firstcontact['result'];
}

function updatefirstcontact($id,$count){
	$firstcontact = api (
    	'crm.contact.update',
   			[
				'ID' => $id,
				'Fields' => ['UF_CRM_1604333683' => $count] //Set new count for duplicates
    		]);
	return $firstcontact['result'];
}

function updatelastcontact($id){
	$lastcontact = api (
    	'crm.contact.update' ,
   			[
				'ID' => $id,
 	 	 		'Fields' => ['UF_CRM_1595855990' => true] //Duplicate control set to true
    		]);
	return $lastcontact['result'];
}

function getresponsablemanager($id){
	$responsable = api (
    	'user.get',
   			[
				'FILTER'=>[
					'ID' => $id
				]
    		]);

	$responsablemanager = api (
    	'user.get',
   			[
				'FILTER'=>[
					'UF_DEPARTMENT' => $responsable['result'][0]['UF_DEPARTMENT'],
 	 	 			'WORK_POSITION' => 'Manager'
				]
    		]);

	return $responsablemanager['result'][0];
}

function setmanagermessage($id,$message){
	$managermessage = api (
    	'im.notify',
   			[
				"to" => $id,
         		"message" => $message,
         		"type" => 'SYSTEM',
    		]);
	return $managermessage['result'];
}

?>
