<?php

require_once (__DIR__.'/crest/crest.php');

$timeline = ( CRest :: call (
    'crm.timeline.comment.add' ,
   	[
		'fields' =>
           [
               "ENTITY_ID" => $_GET['fields']['ENTITY_ID'],
               "ENTITY_TYPE" => "contact",
               "COMMENT" => $_GET['fields']['COMMENT'],
           ]
   	])
);
//var_dump($timeline);

if(isset($timeline['error']) && isset($timeline['error_description'])){
	echo 'ERROR FOUND: '. $timeline['error']." - > ".$timeline['error_description'];
} else {
	sleep(2);
	header('Location: https://crm.domain.com/marketplace/app/15/');
}

?>