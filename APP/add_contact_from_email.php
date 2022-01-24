<?php

date_default_timezone_set('America/New_York');
//require_once (__DIR__.'/crest/crest.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

if(isset($_GET['KEY']) && $_GET['KEY'] === 'sjhffgJAFDHGD78V87W8E2DHGUB398YCG93YGDCWCD87CY'){

$name = isset($_GET['NAME']) ? $_GET['NAME'] : '';
$last_name = isset($_GET['LAST_NAME']) ? $_GET['LAST_NAME'] : '';
$language = isset($_GET['LANGUAGE']) ? $_GET['LANGUAGE'] : 'English';
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

$body = isset($_GET['BODY']) ? $_GET['BODY'] : '';
$subject = isset($_GET['SUBJECT']) ? $_GET['SUBJECT'] : '';
$body = explode("\n", $body);
$run = false;

if(count($body) == 9 && $subject == "Quiz Form submitted on The Sosa Clinic"){
	/*
	[0] => How many pounds do you want to lose? 200
    [1] => Are you willing to go into surgery to accomplish it? Yes
    [2] => What is your name? mmmmmmm
    [3] => What’s your current weight? 450
    [4] => What about your height? 5.5
    [5] => We have some great news for you, what’s a good phone number to reach you at?
    [6] => 12365478963
    [7] => Could you also provide us with your email? mmmmmmm@gmail.com
    [8] => 
*/
$phone = $body[6];
$email = explode("? ", $body[7])[1];
$name = explode("? ", $body[2])[1];
$source_desc = $subject;

$run = true;
}

if(count($body) == 11 && $subject == "New Appointment: Appointment"){
	/*
	[0] =>  The Sosa Clinic
    [1] => *Name*
    [2] => mmmmmmmm
    [3] => *Email*
    [4] => test@test.com
    [5] => *Your Phone Number*
    [6] => 12365478963
    [7] => *Optional message*
    [8] => mmmmmmmmmmm
    [9] => Sent from The Sosa Clinic <https://www.thesosaclinic.com>
    [10] => 
*/
$phone = $body[6];
$email = $body[4];
$name = $body[2];
$source_desc = $subject;

$pos = strpos($name, " ");
if ($pos === false) {
	//echo "The string '$findme' was not found in the string '$mystring'";
} else {
	//echo "The string '$findme' was found in the string '$mystring'";
	//echo " and exists at position $pos";
	if(count(explode(" ", $body[2])) == 2){
		$name = explode(" ", $body[2])[0];
		$last_name = explode(" ", $body[2])[1];
	}
}

$run = true;
}

//log information:
	/*	$content = $body;
	$content['subject'] = $subject;
	$content['phone'] = $phone;
	$content['email'] = $email;
	$content['name'] = $name;
	file_put_contents("log_add_contact_from_mail.txt", print_r($content, true), FILE_APPEND);*/

if($run == true){ //Valid email to RUN!

$today = date("m.d.y",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));//date("m.d.y"); 
$nowForLog = date("Y-m-d h:i:s",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));//date("m.d.y");  
$file = "logs/total_".$today.".txt";
if($source == "18") $file = "logs/total_ext_".$today.".txt";
try {

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


if(isset($phone) && $phone != ""){
	//$phone = str_replace("+", "", $phone);
$phone = str_replace("+1","",str_replace(" ","",str_replace("-","",str_replace("(","",str_replace(")","",$phone)))));
//if(strlen($phone) > 10) $phone = substr($phone, 1, 10);
echo $phone;

$present_phones = DBGet("select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated where mycount > 0 and (myVALUE like '%".$phone."' or myVALUE = '".$phone."' or myVALUE = '1".$phone."') limit 2;"); //Query to database new

} else {
	echo "There it is not phone number to check!";
$present_emails = DBGet("select ID from b_crm_dp_comm_mcd where TYPE = 'EMAIL' AND VALUE = '".$email."' limit 2;");

}

if((!isset($present_phones[0]["lastid"]) && $phone != "") || (!isset($present_emails[0]["ID"]) && ($phone == "" || $phone == null))){ //Does not exist contact with that number or email

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
				'UTM_SOURCE' => $source,
				'SOURCE_DESCRIPTION' => $source_desc,
				'SOURCE_ID' => $source
			]
	 	 //'EMAIL'=> [['VALUE' => 'lola@yea.com', 'VALUE_TYPE' => 'WORK']] ,
	 	 //'PHONE'=> [['VALUE' => '123458', 'VALUE_TYPE' => 'WORK']] ,
    	]);

		echo "Contact Added! ID:".$contact['result'];

		$myfile = fopen("logs/000add_contact_control_log.txt", "a+") or die("Unable to open file!");
		fwrite($myfile, $nowForLog.";Phone:".$phone.";ContactID:".$contact['result'].";Was added to the CRM correctly!".PHP_EOL);
		fclose($myfile);

	} else { //Duplicate by number and email

		//Only for TESTs!
		$myfile = fopen("logs/000duplicate_control_log.txt", "a+") or die("Unable to open file!");

		$firstcontact = getfirstcontact($present_phones[0]["lastid"]); //Get oldest contact
		$contactLink = "<a href='https://crm.domain.com/crm/contact/details/".$firstcontact['ID']."/'>". $firstcontact['NAME'] . " " . $firstcontact['LAST_NAME']. "</a>";
		$updatefirstcontact = updatefirstcontact($present_phones[0]["lastid"],$firstcontact['UF_CRM_1604333683'] + 1); //Add 1 to duplicate count
		//$updatelastcontact = updatelastcontact($duplicate_phones[0]["lastid"]); //Duplicate set to true for must recent contact
		$responsablemanager = getresponsablemanager($firstcontact['ASSIGNED_BY_ID']);
	$message = 'A duplicate contact was found with phone:'.$phone.', email:'.$email.' used by contact '.$contactLink.' and it will be not add to the CRM!';
		$sentnotificationtomanager = setmanagermessage($responsablemanager['ID'],$message);
		echo "Contact Exist; not to be Added!";

		fwrite($myfile, date("Y-m-d h:i:sa").";Phone:".$phone.";ContactID:".$present_phones[0]["lastid"].";Was trying to get auto duplicate!".PHP_EOL);

		//Only for tests!
		fclose($myfile);
}

} else echo "No valid email to run!";

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