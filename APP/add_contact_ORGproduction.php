<?php

date_default_timezone_set('America/New_York');
//require_once (__DIR__.'/crest/crest.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

try {

	$today = date("m.d.y"); 
	$file = "logs/total_".$today.".txt";

	// default the counter value to 1
	$counter = 1;
	
	// add the previous counter value if the file exists    
	if (file_exists($file)) {
		$counter += (int)file_get_contents($file);
	}

	// write the new counter value to the file
	file_put_contents($file, $counter);

} catch (Exception $e) {

}

if(isset($_GET['KEY']) && $_GET['KEY'] === 'sjhffgJAFDHGD78V87W8E2DHGUB398YCG93YGDCWCD87CY'){

$name = isset($_GET['NAME']) ? $_GET['NAME'] : '';
$last_name = isset($_GET['LAST_NAME']) ? $_GET['LAST_NAME'] : '';
$language = isset($_GET['LANGUAGE']) ? $_GET['LANGUAGE'] : '';
$email = isset($_GET['EMAIL']) ? $_GET['EMAIL'] : '';
$phone = isset($_GET['PHONE']) ? $_GET['PHONE'] : '';
$web = isset($_GET['WEB']) ? $_GET['WEB'] : '';
$comment = isset($_GET['COMMENT']) ? $_GET['COMMENT'] : '';
$address = isset($_GET['ADDRESS']) ? $_GET['ADDRESS'] : '';
$address_2 = isset($_GET['ADDRESS_2']) ? $_GET['ADDRESS_2'] : '';
$city = isset($_GET['CITY']) ? $_GET['CITY'] : '';
$postal = isset($_GET['POSTAL']) ? $_GET['POSTAL'] : '';
$state = isset($_GET['STATE']) ? $_GET['STATE'] : '';
$country = isset($_GET['COUNTRY']) ? $_GET['COUNTRY'] : '';
$source = isset($_GET['SOURCE']) ? $_GET['SOURCE'] : 'OTHER';
$source_desc = isset($_GET['SOURCE_D']) ? $_GET['SOURCE_D'] : '';

if(isset($phone) && $phone != ""){
	//$phone = str_replace("+", "", $phone);
$phone = str_replace("+1","",str_replace(" ","",str_replace("-","",str_replace("(","",str_replace(")","",$phone)))));
if(strlen($phone) > 10) $phone = substr($phone, 1, 10);
echo $phone;

$present_phones = DBGet("select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated where mycount > 0 and (myVALUE like '%".$phone."' or myVALUE = '".$phone."' or myVALUE = '1".$phone."') limit 2;"); //Query to database new

} else {
	echo "There it is not phone number to check!";
}

if(!isset($present_phones[0]["lastid"])){ //Does not exist contact with that number

$contact = api (
    'crm.contact.add' ,
   		[
 	 	 //'FILTER' => ['UF_CRM_1590674689393' => $language],
	 	 //'SELECT' => ['ASSIGNED_BY_ID'],
			'fields' => [
				'NAME' => $name,
				'LAST_NAME' => $last_name,
				'COMMENTS' => $comment,
				'UF_CRM_1590674689393' => $language,
				'EMAIL'=> [['VALUE' => $email, 'VALUE_TYPE' => 'OTHER']] ,
	 	 		'PHONE'=> [['VALUE' => $phone, 'VALUE_TYPE' => 'MOBILE']] ,
				'WEB'=> [['VALUE' => $web, 'VALUE_TYPE' => 'OTHER']] ,
				'ADDRESS' => $address,
				'ADDRESS_2' => $address_2,
				'ADDRESS_CITY' => $city,
				'ADDRESS_POSTAL_CODE' => $postal,
				'ADDRESS_PROVINCE' => $state,
				'ADDRESS_COUNTRY' => $country,
				'SOURCE_DESCRIPTION' => $source_desc,
				'SOURCE_ID' => $source
			]
	 	 //'EMAIL'=> [['VALUE' => 'lola@yea.com', 'VALUE_TYPE' => 'WORK']] ,
	 	 //'PHONE'=> [['VALUE' => '123458', 'VALUE_TYPE' => 'WORK']] ,
    	]);

		echo "Contact Added!";
	} else {

		//Only for TESTs!
		$myfile = fopen("logs/000duplicate_control_log.txt", "a+") or die("Unable to open file!");

		$firstcontact = getfirstcontact($present_phones[0]["lastid"]); //Get oldest contact
		$contactLink = "<a href='https://crm.domain.com/crm/contact/details/".$firstcontact['ID']."/'>". $firstcontact['NAME'] . " " . $firstcontact['LAST_NAME']. "</a>";
		$updatefirstcontact = updatefirstcontact($present_phones[0]["lastid"],$firstcontact['UF_CRM_1604333683'] + 1); //Add 1 to duplicate count
		//$updatelastcontact = updatelastcontact($duplicate_phones[0]["lastid"]); //Duplicate set to true for must recent contact
		$responsablemanager = getresponsablemanager($firstcontact['ASSIGNED_BY_ID']);
		$message = 'A duplicate contact was found with phone:'.$phone.' used by contact '.$contactLink.' and it will be not add to the CRM!';
		$sentnotificationtomanager = setmanagermessage($responsablemanager['ID'],$message);
		echo "Contact Exist; not to be Added!";

		fwrite($myfile, date("Y-m-d h:i:sa").";Phone:".$phone.";ContactID:".$present_phones[0]["lastid"].";Was trying to get auto duplicate!".PHP_EOL);

		//Only for tests!
		fclose($myfile);
	}

} else {
	echo "Access Denied!";
}

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