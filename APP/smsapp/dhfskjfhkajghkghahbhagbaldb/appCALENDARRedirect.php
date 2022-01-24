<?php

require_once (__DIR__.'/crest/crest.php');

//Only for TESTs!
//$myfile = fopen("log.txt", "a") or die("Unable to open file!");
//echo '**************************\n';
//echo date("Y.m.d G:i:s")."\n";
//var_dump($_GET);
//var_dump($_POST); //from bitrix24

$meeting = ( CRest :: call (
    'calendar.event.add' ,
   	[
	 'type'=> 'user',
     'ownerId'=> $_POST['IDresponsable'],
     'name'=> $_POST['name'],
     'description'=> $_POST['description'],
     'from'=> $_POST['from'],
     'to'=> $_POST['to'],
     'skipTime'=> 'Y',
     'section'=> 5,
     'color'=> '#9cbe1c',
     'text_color'=> '#283033',
     'accessibility'=> 'absent',
     'importance'=> 'normal',
     'is_meeting'=> 'Y',
     'private_event'=> 'N',
     'remind'=>
		[
			[
			 'type'=> 'min',
			 'count'=> 20
			]
		],
     'location'=> 'Miami',
     'attendees'=> [$_POST['contactid']],
     'host'=> $_POST['IDresponsable'],
		'meeting' =>
           [
               	'text'=> 'inviting text',
      			'open'=> true,
     		 	'notify'=> true,
      			'reinvite'=> false
           ],
		'auth' => [
			'access_token' => $_POST['access_token']
		]
   	])
);
//var_dump($meeting);

//var_dump($setmessage);
//Only for tests!
//fwrite($myfile, file_put_contents("log.txt", ob_get_flush()));
//fclose($myfile);

if(isset($meeting['error']) && isset($meeting['error_description'])){
	echo 'ERROR FOUND: '. $meeting['error']." - > ".$meeting['error_description'];
} else {
	sleep(2);
	header('Location: https://crm.domain.com/marketplace/app/15/');
}


?>