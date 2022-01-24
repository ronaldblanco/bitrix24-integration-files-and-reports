<?php

//require_once (__DIR__.'/crest/crest.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");

//Only for TESTs!
//$myfile = fopen("log.txt", "a") or die("Unable to open file!");
//echo '**************************\n';
//echo date("Y.m.d G:i:s")."\n";
//var_dump($_GET);
//var_dump($_POST); //from bitrix24

$timeline = api (
    'crm.timeline.comment.add' ,
   	[
		'fields' =>
           [
               "ENTITY_ID" => $_GET['contactID'],
               "ENTITY_TYPE" => "contact",
			   "COMMENT" => "A SMS with text '" . $_GET['message'] . "' was send to this contact using SMS/MMS Time line Application!",
           ]
   	]);
//var_dump($timeline);

	$setmessage = api (
    	'im.notify' ,
   		[
			"to" => $_GET['coordinatorID'],
         	"message" => "You send a SMS with text '" . $_GET['message'] . "' to contat ID <a href='https://crm.domain.com/crm/contact/details/".$_GET['contactID']."/'>".$_GET['contactID']."</a>"."!",
         	"type" => 'SYSTEM',
   		]);
//var_dump($setmessage);
//Only for tests!
//fwrite($myfile, file_put_contents("log.txt", ob_get_flush()));
//fclose($myfile);

if(isset($timeline['error']) && isset($setmessage['error'])){
	echo 'ERROR FOUND: '. $timeline['error']." - > ".$timeline['error_description'];
	echo 'ERROR FOUND: '. $setmessage['error']." - > ".$setmessage['error_description'];
} else {
	sleep(2);
	header('Location: https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/smsapp/dhfskjfhkajghkghahbhagbaldb/index.php?from='.$_GET['from'].'&to='.$_GET['to'].'&user='.$_GET['user'].'&code='.$_GET['code'].'&domain='.$_GET['domain'].'&member_id='.$_GET['member_id'].'&scope='.$_GET['scope'].'&server_domain='.$_GET['server_domain']);
}

?>