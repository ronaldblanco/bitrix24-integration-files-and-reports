<?php

date_default_timezone_set('America/New_York');
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require("/home/bitrix/www/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require("/home/bitrix/www/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//$from = date("Y-m-d");
//$to = date("Y-m-d");
$followupdate1 = gmdate("Y-m-d",strtotime(date("Y-m-d") . ' -3 day'));
$followupdate2 = gmdate("Y-m-d",strtotime(date("Y-m-d") . ' -2 day'));
$followupdate3 = gmdate("Y-m-d",strtotime(date("Y-m-d") . ' -1 day'));
$followupdate4 = date("Y-m-d");
$followupdate = gmdate("Y-m-d",strtotime(date("Y-m-d") . ' -4 day'));
$deadline = strtotime(date("Y-m-d") . ' +2 day');
//$deadline = gmdate("Y-m-d\TH:i:s", $deadline)."-04:00";
$deadline = gmdate("Y-m-d", $deadline);
//$to3 = date("Y-m-d",$to2);
//var_dump($followupdate4);

//$Follow_up_contacts_by_activities = DBGet("select b_crm_contact.ID,ASSIGNED_BY_ID from b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID inner join b_crm_act on b_crm_act.OWNER_ID = b_crm_contact.ID Where b_uts_crm_contact.UF_CRM_1607628815 = '1' and (CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') LIKE '".$followupdate."%') and (CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') not LIKE '".$followupdate1."%') and (CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') not LIKE '".$followupdate2."%') and (CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') not LIKE '".$followupdate3."%') and (CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') not LIKE '".$followupdate4."%') order by ASSIGNED_BY_ID limit 50;");
//$Follow_up_contacts_by_comments = DBGet("select ENTITY_ID as ID,AUTHOR_ID as ASSIGNED_BY_ID,CONVERT_TZ(MAX(CREATED),'+00:00','-08:00') as lastcomment from b_crm_timeline inner join b_crm_timeline_bind on b_crm_timeline.ID = b_crm_timeline_bind.OWNER_ID inner join b_crm_contact on b_crm_contact.ID = ENTITY_ID and b_crm_contact.ASSIGNED_BY_ID = AUTHOR_ID where ENTITY_TYPE_ID = '3' and (CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') LIKE '".$followupdate."%') and (CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') not LIKE '".$followupdate1."%') and (CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') not LIKE '".$followupdate2."%') and (CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') not LIKE '".$followupdate3."%') and (CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') not LIKE '".$followupdate4."%') group by ENTITY_ID order by AUTHOR_ID desc limit 50;");

//var_dump(count($Follow_up_contacts_by_activities));
//var_dump($Follow_up_contacts_by_comments);

$Follow_up_contacts = DBGet("select ENTITY_ID as ID,b_crm_timeline.AUTHOR_ID as ASSIGNED_BY_ID,CONVERT_TZ(MAX(b_crm_timeline.CREATED),'+00:00','-08:00') as lastcomment,CONVERT_TZ(MAX(b_crm_act.created),'+00:00','-08:00') as lastactivity from b_crm_timeline inner join b_crm_timeline_bind on b_crm_timeline.ID = b_crm_timeline_bind.OWNER_ID inner join b_crm_contact on b_crm_contact.ID = ENTITY_ID and b_crm_contact.ASSIGNED_BY_ID = b_crm_timeline.AUTHOR_ID inner join b_crm_act on b_crm_act.OWNER_ID = b_crm_contact.ID INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID where b_uts_crm_contact.UF_CRM_1607628815 = '1' and ENTITY_TYPE_ID = '3' and b_crm_timeline.AUTHOR_ID = '113' and (((CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') LIKE '".$followupdate."%' or CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') not LIKE '".$followupdate."%') and (CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') not LIKE '".$followupdate1."%') and (CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') not LIKE '".$followupdate2."%') and (CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') not LIKE '".$followupdate3."%') and (CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-08:00') not LIKE '".$followupdate4."%')) and ((CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') LIKE '".$followupdate."%' or CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') not LIKE '".$followupdate."%')) and (CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') not LIKE '".$followupdate1."%') and (CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') not LIKE '".$followupdate2."%') and (CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') not LIKE '".$followupdate3."%') and (CONVERT_TZ(b_crm_act.created,'+00:00','-08:00') not LIKE '".$followupdate4."%')) group by ENTITY_ID order by b_crm_timeline.AUTHOR_ID desc limit 1000;");

//var_dump(count($Follow_up_contacts));

$info = array();

$contacts_list = array();
$actual_responsable = '';

$message = 'A task reminder: you have a task with ';

foreach($Follow_up_contacts as $contact){

	if($actual_responsable == '') $actual_responsable = $contact['ASSIGNED_BY_ID']; // first itteration

	if($actual_responsable == $contact['ASSIGNED_BY_ID']){ //we are on the same responsable
		array_push($contacts_list,"C_".$contact['ID']);
		//$message = $message . "<a href='https://crm.domain.com/crm/contact/details/".$contact['ID']."/'></a>";
	} else { //We have a new responsable
		array_push($info,array('responsable' => $actual_responsable, 'contacts' => $contacts_list)); //save the work before clean

		$actual_responsable = $contact['ASSIGNED_BY_ID']; //new responsable
		$contacts_list = array(); //new contact list
		array_push($contacts_list,"C_".$contact['ID']);
	}

}

array_push($info,array('responsable' => $actual_responsable, 'contacts' => $contacts_list)); //save the last work before clean

//var_dump($info);

foreach($info as $task){

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

	/*$managermessage = api (
    	'im.notify',
   			[
				"to" => $task['responsable'],
         		"message" => $message,
         		"type" => 'TASKS',
		]);*/

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

	return array("manager" => $responsablemanager['result'][0], "user" => $responsable['result'][0]);
}

?>