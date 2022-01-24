<?php
date_default_timezone_set('America/New_York');
//echo date('Y-m-d h:m:s');

//$date = new DateTime(date('Y-m-d'), new DateTimeZone('America/New_York'));
//echo $date->format('Y-m-d') . "\n";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//$usercontactscount = DBGet("SELECT SUM(mycount.idcount) FROM (SELECT COUNT(b_crm_contact.ID) as idcount,CREATED_BY_ID,'hola' as hola FROM b_crm_contact WHERE CONVERT_TZ(DATE_CREATE,'+00:00','-08:00') LIKE '".date('Y-m-d')."%' AND b_crm_contact.ASSIGNED_BY_ID = "."115"." GROUP BY CREATED_BY_ID) as mycount GROUP BY mycount.hola LIMIT 2;");
//var_dump($usercontactscount[0]["SUM(mycount.idcount)"]);

class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open(__DIR__.'/db/queue');
    }
}

$db = new MyDB();

//$db->exec('CREATE TABLE foo (bar STRING)');
$execution = $db->exec("INSERT INTO apicalls VALUES('test url',0,'".date()."')");
//var_dump($execution);
if($execution){
	echo "URL added to the queue succesfully!";
} 
else {
	echo "An error happen with the url!";
}

?>

<!--<iframe src="https://crm.domain.com/crm/contact/details/<?php echo "189330"?>"></iframe>-->

<!--<object data="https://crm.domain.com/crm/contact/details/<?php echo "189330"?>" width="600" height="400">
    <embed src="https://crm.domain.com/crm/contact/details/<?php echo "189330"?>" width="600" height="400"> </embed>
    Error: Embedded data could not be displayed.
</object>-->