<?php

date_default_timezone_set('America/New_York');
//to be able of cron execute!##############
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
$appfolder = "/bitrix/admin/dhhdbw73723934dvrgintegration/APP/";
//#########################################

$myfile = fopen($_SERVER["DOCUMENT_ROOT"].$appfolder."logs/000duplicate_control_log.txt", "a+") or die("Unable to open file!");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//$duplicate_phones = DBGet("select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid,MIN(ENTITY_ID) as firstid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated where mycount > 1 and firstid <> lastid limit 50;"); //Query to database (original)
$duplicate_phones = DBGet("select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid,MIN(ENTITY_ID) as firstid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated inner join b_crm_contact on b_crm_contact.id = allrepeated.lastid where mycount > 1 and firstid <> lastid and b_crm_contact.ASSIGNED_BY_ID <> 152 limit 50;"); //Query to database (not delete for Daimy)
var_dump($duplicate_phones);

$nowForLog = date("Y-m-d h:i:sa",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));//date("m.d.y");

echo '</br>';
echo '</br>';

for($i = 0;$i<count($duplicate_phones);$i++){

	$duplicate_check = api (
    	'crm.contact.update' ,
   			[
				'ID' => $duplicate_phones[$i]["lastid"],
 	 	 		'Fields' => [
					'UF_CRM_1595855990' => true,
					'ASSIGNED_BY_ID' => 152
							] //Duplicate control set to true y asignado a Daimy
	 	 		//'SELECT' => ['ID'], //UF_CRM_1594061489 -> coordinator, UF_CRM_1594061559 -> 3rd responsable
	 	 		//'EMAIL'=> [['VALUE' => 'lola@yea.com', 'VALUE_TYPE' => 'WORK']] ,
	 	 		//'PHONE'=> [['VALUE' => '123458', 'VALUE_TYPE' => 'WORK']] ,
		]);
		echo $duplicate_check.'</br>';
	echo "Contact ID with duplicate phone: ".$duplicate_phones[$i]["lastid"]."<br/>";
	fwrite($myfile, $nowForLog.";Phone:".$duplicate_phones[$i]["myVALUE"].";ContactID:".$duplicate_phones[$i]["lastid"].";Marked to be deleted!".PHP_EOL);

	$firstcontact = getfirstcontact($duplicate_phones[$i]["firstid"]); //Get oldest contact
	$updatefirstcontact = updatefirstcontact($duplicate_phones[$i]["firstid"],$firstcontact['UF_CRM_1604333683'] + 1); //Add 1 to duplicate count

	$responsablemanager = getresponsablemanager($firstcontact['ASSIGNED_BY_ID']);
	$contactLink = "<a href='https://crm.domain.com/crm/contact/details/".$firstcontact['ID']."/'>". $firstcontact['NAME'] . " " . $firstcontact['LAST_NAME']. "</a>";
	$message = 'A duplicate contact was found with phone:'.$duplicate_phones[$i]["myVALUE"].' used by contact '. $contactLink .' and it was market to be deleted!';
	$sentnotificationtomanager = setmanagermessage($responsablemanager['ID'],$message);
}

//Only for tests!
//fwrite($myfile, file_put_contents("000duplicate_control_log.txt", ob_get_flush()));
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