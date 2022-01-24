<?php

date_default_timezone_set('America/New_York');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");

/*$start = date("Y-m-d H:m:s",strtotime($_GET['NEWDATE']));
$end = date("Y-m-d H:m:s",strtotime($_GET['NEWDATE'] . ' +1 hour'));
$deadline = date("Y-m-d H:m:s",strtotime($_GET['NEWDATE'] . ' +11 hour'));

$temp = str_replace("user_","",$_GET['RESID']);
if(strpos($temp, ", ") != false){
	$responsable = explode(", ", $temp);
} else $responsable = array($temp);*/

$task_delete = api (
    'tasks.task.delete' ,
   		[
			'taskId' => $_GET['ID']//,
			/*'fields' => [
				'DATE_START' => $start,
				'START_DATE_PLAN' => $start,
				'END_DATE_PLAN' => $end,
				'DEADLINE' => $deadline,
				'RESPONSIBLE_ID' => $responsable[0],
				'ACCOMPLICES' => $responsable
			]*/
    	]);

/*$content = $_GET;
$content["date"] = date("F j, Y, g:i a");
$content['result'] = $responsable;
file_put_contents("log_000task_update.txt", print_r($content, true), FILE_APPEND);*/

?>