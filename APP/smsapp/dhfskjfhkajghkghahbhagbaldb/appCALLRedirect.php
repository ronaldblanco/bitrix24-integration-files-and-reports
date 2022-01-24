<?php

require_once (__DIR__.'/crest/crest.php');

//Only for TESTs!
//$myfile = fopen("log.txt", "a") or die("Unable to open file!");
//echo '**************************\n';
//echo date("Y.m.d G:i:s")."\n";
//var_dump($_GET);
//var_dump($_POST); //from bitrix24

$call = ( CRest :: call (
    'telephony.externalcall.register' ,
   	[
	    "USER_PHONE_INNER" => $_GET['ext'],
        "USER_ID" => $_GET['coordinatorID'],
        "PHONE_NUMBER" => $_GET['callto'],
		"TYPE" => 1,
		"CRM_ENTITY_TYPE" => "CONTACT",
          
   	])
	);
//var_dump($timeline);

	/*$setmessage = ( CRest :: call (
    	'im.notify' ,
   		[
			"to" => $_GET['coordinatorID'],
         	"message" => "You started a CALL to contat ID ".$_GET['contactID']."!",
         	"type" => 'SYSTEM',
   		])
	);*/
//var_dump($setmessage);
//Only for tests!
//fwrite($myfile, file_put_contents("log.txt", ob_get_flush()));
//fclose($myfile);

sleep(3);
	header('Location: https://crm.domain.com/marketplace/app/15/');

?>