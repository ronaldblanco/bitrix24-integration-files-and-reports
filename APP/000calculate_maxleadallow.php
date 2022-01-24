<?php

date_default_timezone_set('America/New_York');
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//Not include the external department
$today = date("Y-m-d",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));

//305
$contactscount = DBGet("SELECT SUM(mycount.idcount) FROM (SELECT COUNT(b_crm_contact.ID) as idcount,CREATED_BY_ID,'hola' as hola FROM b_crm_contact WHERE CONVERT_TZ(DATE_CREATE,'+00:00','-05:00') LIKE '".$today."%' AND SOURCE_ID <> '18' AND SOURCE_ID <> '20' GROUP BY CREATED_BY_ID) as mycount GROUP BY mycount.hola LIMIT 2;");
//$salesmanscount = DBGet("SELECT COUNT(b_crm_contact.ID) as idcount,ASSIGNED_BY_ID FROM b_crm_contact INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID WHERE CONVERT_TZ(DATE_CREATE,'+00:00','-05:00') LIKE '".$today."%' AND b_user.WORK_NOTES <> 'out' AND b_user.WORK_NOTES <> 'adm' AND b_user.WORK_NOTES <> 'ext' AND b_user.WORK_NOTES <> 'ext/sp' AND b_user.WORK_NOTES <> 'ext/all' GROUP BY ASSIGNED_BY_ID LIMIT 250;");
$salesmanscount = DBGet("SELECT ID FROM b_user WHERE b_user.ACTIVE = 'Y' AND b_user.WORK_NOTES <> 'out' AND b_user.WORK_NOTES <> 'adm' AND b_user.WORK_NOTES <> 'ext' AND b_user.WORK_NOTES <> 'ext/sp' AND b_user.WORK_NOTES <> 'ext/all' LIMIT 250;");
//minimun 15 daily
$maxleadallow = 2;

$contactsallcount = intval($contactscount[0]["SUM(mycount.idcount)"]) + (intval($contactscount[0]["SUM(mycount.idcount)"])/12); //the extra it is to help balance with spanish salemans, who got less always.


//External Department (Sosa)
$contactscountSosa = DBGet("SELECT SUM(mycount.idcount) FROM (SELECT COUNT(b_crm_contact.ID) as idcount,CREATED_BY_ID,'hola' as hola FROM b_crm_contact WHERE CONVERT_TZ(DATE_CREATE,'+00:00','-05:00') LIKE '".$today."%' AND (SOURCE_ID = '18' OR SOURCE_ID = '20') GROUP BY CREATED_BY_ID) as mycount GROUP BY mycount.hola LIMIT 2;");
//$salesmanscountSosa = DBGet("SELECT COUNT(b_crm_contact.ID) as idcount,ASSIGNED_BY_ID FROM b_crm_contact INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID WHERE CONVERT_TZ(DATE_CREATE,'+00:00','-05:00') LIKE '".$today."%' AND b_user.WORK_NOTES <> 'out' AND b_user.WORK_NOTES <> 'adm' AND (b_user.WORK_NOTES = 'ext' OR b_user.WORK_NOTES = 'ext/sp' OR b_user.WORK_NOTES = 'ext/all') GROUP BY ASSIGNED_BY_ID LIMIT 250;");
$salesmanscountSosa = DBGet("SELECT ID FROM b_user WHERE b_user.ACTIVE = 'Y' AND b_user.WORK_NOTES <> 'out' AND b_user.WORK_NOTES <> 'adm' AND (b_user.WORK_NOTES = 'ext' OR b_user.WORK_NOTES = 'ext/sp' OR b_user.WORK_NOTES = 'ext/all') LIMIT 250;");
//minimun 5 daily
$maxleadallowSosa = 1;

//var_dump($contactscountSosa);
//var_dump($salesmanscountSosa);

$contactsallcountSosa = intval($contactscountSosa[0]["SUM(mycount.idcount)"]) + (intval($contactscountSosa[0]["SUM(mycount.idcount)"])/12); //the extra it is to help balance with spanish salemans, who got less always.

/////////////////////////////////////////////////
//Sosa check
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 1;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 1;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 1;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 1;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 1;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 2;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 2;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 2;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 2;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 2;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 5;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 5;
}
if($contactsallcountSosa > count($salesmanscountSosa) * $maxleadallowSosa){
	$maxleadallowSosa = $maxleadallowSosa + 5;
}

$newmaxleadallowSosa = api ( //sabe changes to Jadiel user
    'user.update' ,
   		[
			'ID' => 181,
			//'fields' => [
			'PERSONAL_ICQ' => $maxleadallowSosa
			//]
		]);


///////////////////////////////////////////////
//305 check
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 2;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 2;
}
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 2;
}
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 2;
}
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 2;
}
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 2;
}
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 2;
}
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 2;
}
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 2;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}
//sleep(1);
if($contactsallcount > count($salesmanscount) * $maxleadallow){
	$maxleadallow = $maxleadallow + 10;
}

//maximun 465 daily or 23 000 daily leads

$newmaxleadallow = api ( //sabe changes to Ronald user
    'user.update' ,
   		[
			'ID' => 22,
			//'fields' => [
			'PERSONAL_ICQ' => $maxleadallow
			//]
		]);

echo "For 305:";
var_dump($maxleadallow);
var_dump($newmaxleadallow);

echo "For Sosa:";
var_dump($maxleadallowSosa);
var_dump($newmaxleadallowSosa);

?>