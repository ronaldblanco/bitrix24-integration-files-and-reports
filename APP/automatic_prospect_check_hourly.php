<?php

date_default_timezone_set('America/New_York');
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require("/home/bitrix/www/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require("/home/bitrix/www/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//$date = date("Y-m-d");
$date = date("Y-m-d",strtotime(date("Y-m-d h:i:s") . ' +6 hour'));
var_dump(date("Y-m-d h:i:s",strtotime(date("Y-m-d h:i:s") . ' +6 hour')));

$followupdate1 = gmdate("Y-m-d",strtotime(date("Y-m-d") . ' -3 day'));
$followupdate2 = gmdate("Y-m-d",strtotime(date("Y-m-d") . ' -2 day'));
$followupdate3 = gmdate("Y-m-d",strtotime(date("Y-m-d") . ' -1 day'));
$followupdate4 = date("Y-m-d");
$followupdate = gmdate("Y-m-d",strtotime(date("Y-m-d") . ' -4 day'));
$deadline = strtotime(date("Y-m-d") . ' +2 day');
//$deadline = gmdate("Y-m-d\TH:i:s", $deadline)."-04:00";
$deadline = gmdate("Y-m-d", $deadline);

$no_prospects_with_activity_by_day = DBGet("select noprospect_activity.ID from (select b_crm_contact.ID from b_crm_timeline inner join b_crm_timeline_bind on b_crm_timeline.ID = b_crm_timeline_bind.OWNER_ID inner join b_crm_contact on b_crm_contact.ID = ENTITY_ID INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID where CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') LIKE '".$date."%' and CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-05:00') LIKE '".$date."%' and ENTITY_TYPE_ID = '3' and b_uts_crm_contact.UF_CRM_1591040450352 not LIKE '%i:167;%' and AUTHOR_ID = ASSIGNED_BY_ID) as noprospect_activity group by noprospect_activity.ID limit 500;");
$no_prospects_by_day = DBGet("select b_crm_contact.ID from b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID where CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') LIKE '".$date."%' and b_uts_crm_contact.UF_CRM_1591040450352 not LIKE '%i:167;%' limit 500;");

//select ENTITY_ID as ID,CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') as contact_create,b_crm_contact.ASSIGNED_BY_ID,b_crm_timeline.AUTHOR_ID as ASSIGNED_BY_ID_TIMELINE,CONVERT_TZ(MAX(b_crm_timeline.CREATED),'+00:00','-05:00') as lastcomment,b_uts_crm_contact.UF_CRM_1591040450352 from b_crm_timeline inner join b_crm_timeline_bind on b_crm_timeline.ID = b_crm_timeline_bind.OWNER_ID inner join b_crm_contact on b_crm_contact.ID = ENTITY_ID INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID where b_crm_contact.ID = '277100' and b_crm_contact.ASSIGNED_BY_ID <> b_crm_timeline.AUTHOR_ID and CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') like '2021-02-06%' and b_uts_crm_contact.UF_CRM_1591040450352 not LIKE '%i:167;%' and ENTITY_TYPE_ID = '3' and CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-05:00') LIKE '2021-02-06%' group by ENTITY_ID order by b_crm_timeline.AUTHOR_ID desc limit 5;

$temp = array();
foreach($no_prospects_with_activity_by_day as $contact){
	array_push($temp,$contact["ID"]);
}
$no_prospects_with_activity_by_day = $temp;

$temp = array();
foreach($no_prospects_by_day as $contact){
	array_push($temp,$contact["ID"]);
}
$no_prospects_by_day = $temp;

//var_dump($no_prospects_with_activity_by_day);
//var_dump($no_prospects_by_day);
//var_dump(count($no_prospects_with_activity_by_day));
//var_dump(count($no_prospects_by_day));

$prospects = array_diff($no_prospects_by_day,$no_prospects_with_activity_by_day);
var_dump($prospects);

//$info = array();
//$contacts_list = array();
//$actual_responsable = '';

//$message = 'A task reminder: you have a task with ';

foreach($prospects as $contact){

	$get_contact =  api (
    	'crm.contact.get' ,
   			[
				'id' => $contact

		]);

	$responsablemanager = getresponsablemanager($get_contact['result']['ASSIGNED_BY_ID']);
	//var_dump($responsablemanager);
	$message = 'The user '.$responsablemanager['user']['NAME'].' '.$responsablemanager['user']['LAST_NAME'].' taked out the prospect tag of contact '. $get_contact['result']['NAME']. ' '. $get_contact['result']['LAST_NAME']. ' without any apparent activity on the timeline; please check the case!';
	//$sentnotificationtomanager = setmanagermessage($responsablemanager['manager']['ID'],$message);
	$sentnotificationtomanager = setmanagermessage(22,$message);

	//var_dump($get_contact['result']['UF_CRM_1591040450352']);
	/*$new_tags = $get_contact['result']['UF_CRM_1591040450352'];
	array_push($new_tags,167);

	var_dump($new_tags);*/ //adding prospects

	/*$update_contact =  api (
    	'crm.contact.update' ,
   			[
				'id' => $contact
				'fields' => [
					"UF_CRM_1591040450352" => "crm"
				]
	]);*/

}

//array_push($info,array('responsable' => $actual_responsable, 'contacts' => $contacts_list)); //save the last work before clean

//var_dump($info);

/*foreach($info as $task){

	$manager = getresponsablemanager($task['responsable'])['manager']['ID'];
	//var_dump($manager);
	//var_dump($deadline);

	$followup_task =  api (
    	'tasks.task.add' ,
   			[
 	  			'fields' => [
					'TITLE' => 'Follow Up task for contacts today!',
					"DESCRIPTION" => "A task was created to help you follow up your contacts today!",
					"RESPONSIBLE_ID" => $task['responsable'],
					"DEADLINE" => $deadline,
					"PRIORITY" => "2",
					//"deadline" => "2020-12-11T20:00:00-04:00",
					"UF_CRM_TASK" => $task['contacts'], //contacts
					"AUDITORS" => array($manager,22), //manager
					"TAGS" => "crm"
				]
			]);

	var_dump($followup_task);


}*/

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

	return array("manager" => $responsablemanager['result'][0], "user" => $responsable['result'][0]);
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