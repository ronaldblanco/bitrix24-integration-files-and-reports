<?php


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

	//(myVALUE like '%".$phone."' or myVALUE = '".$phone."' or myVALUE = '1".$phone."')
$present_phones = DBGet("select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated where mycount > 0 and (myVALUE like '%".$phone."' or myVALUE = '".$phone."' or myVALUE = '1".$phone."') limit 2;"); //Query to database new
	//$present_phones = DBGet("select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated where mycount = 1 and myVALUE ='".$phone."' limit 2;"); //Query to database Original
	//$present_phones[$i]["lastid"]

	/*$dupcontact = api (
    'crm.contact.list' ,
   		[
 	 	 'FILTER' => ['PHONE' => $phone],
	 	 'SELECT' => ['ID'],
			//'ID' => $id,
	 	 //'EMAIL'=> [['VALUE' => 'lola@yea.com', 'VALUE_TYPE' => 'WORK']] ,
	 	 //'PHONE'=> [['VALUE' => '123458', 'VALUE_TYPE' => 'WORK']] ,
    	]);
var_dump($dupcontact['result']);*/
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
	//var_dump(count($contact['result']));
	echo "Contact Added!";
} else {
	echo "Contact Exist; not to be Added!";
}


}




?>