<?php
//Only for TESTs!
$myfile = fopen("logs/000duplicate_control_log.txt", "a+") or die("Unable to open file!");
//echo '**************************\n';
//echo date("Y.m.d G:i:s")."\n";
//var_dump($_GET);
//var_dump($_POST); //from bitrix24
//userid = 0 always!
//Call http://techcnet.com/APP/gfdcxjgfjtgfcjg89/find_user.php?CONTACT_ID=133&LANGUAGE=English
//require_once (__DIR__.'/crest/crest.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

if(isset($_GET['KEY']) && $_GET['KEY'] === 'sjhffgJAFDHGD78V87W8E2DHGUB398YCG93YGDCWCD87CY'){


$contactid = isset($_GET['CONTACT_ID']) ? $_GET['CONTACT_ID'] : 0;
$phone = isset($_GET['PHONE']) ? $_GET['PHONE'] : '0000000000';
//var_dump();
$phone = str_replace("+1","",str_replace(" ","",str_replace("-","",str_replace("(","",str_replace(")","",$phone)))));

	//select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid,MIN(ENTITY_ID) as firstid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated where mycount > 1 and firstid <> lastid limit 50;
$duplicate_phones = DBGet("select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid,MIN(ENTITY_ID) as firstid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated where mycount > 1 and firstid <> lastid and (myVALUE like '%".$phone."' or myVALUE = '".$phone."' or myVALUE = '1".$phone."') limit 2;"); //Query to database new
	//$duplicate_phones = DBGet("select * from (select myVALUE,count(myVALUE) as mycount,MAX(ENTITY_ID) as lastid from (select SUBSTR(VALUE, 2, 10) as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 11 UNION select VALUE as myVALUE,ENTITY_ID,ID from b_crm_dp_comm_mcd where TYPE = 'PHONE' and ENTITY_TYPE_ID = 3 and CHAR_LENGTH(VALUE) = 10) as mygroup group by mygroup.myVALUE) as allrepeated where mycount > 1 and myVALUE = '".$phone."' limit 2;"); //Query to database Original
	//$duplicate_phones[$i]["lastid"]

if(isset($duplicate_phones[0]["lastid"])){
	$duplicate_contact = api (
    	'crm.contact.update' ,
   			[
				'ID' => $duplicate_phones[0]["lastid"],
 	 	 		'Fields' => ['UF_CRM_1595855990' => true] //Duplicate control set to true
	 	 		//'SELECT' => ['ID'], //UF_CRM_1594061489 -> coordinator, UF_CRM_1594061559 -> 3rd responsable
	 	 		//'EMAIL'=> [['VALUE' => 'lola@yea.com', 'VALUE_TYPE' => 'WORK']] ,
	 	 		//'PHONE'=> [['VALUE' => '123458', 'VALUE_TYPE' => 'WORK']] ,
    		]);
	//var_dump($duplicate_contact['result']);
	echo "Contact it is duplicate and was market!";
	fwrite($myfile, date("Y-m-d h:i:sa").";Phone:".$phone.";ContactID:".$duplicate_phones[0]["lastid"].";Marked to be deleted!".PHP_EOL);

} else {
	echo "Contact it is not duplicate!";
	//fwrite($myfile, date("Y-m-d h:i:sa")."Contact it is not duplicate!".PHP_EOL);
}
	

}
	
//Only for tests!
//fwrite($myfile, file_put_contents("000duplicate_control_log.txt", ob_get_flush()));
fclose($myfile);

?>
