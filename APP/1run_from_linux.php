<?php

$arrContextOptions=array(
      "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  

$send_to_CRM2 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/send_contacts_to_CRM2.php?limit=50', false, stream_context_create($arrContextOptions));
sleep(2);

$spanish_user1 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_auto.php?language=Spanish&responsable=1&limit=50', false, stream_context_create($arrContextOptions));
sleep(2);
$spanish_user2 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_auto.php?language=Spanish&responsable=22&limit=50', false, stream_context_create($arrContextOptions));
sleep(2);
//$spanish_user22 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_auto.php?language=Español&responsable=22&limit=50');
//var_dump($spanish_user22);
//$spanish_user222 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_auto.php?language=Portuguese&responsable=1&limit=50');
//var_dump($spanish_user222);
$spanish_user3 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_auto.php?language=Spanish&responsable=23&limit=50', false, stream_context_create($arrContextOptions));
sleep(2);
$english_user1 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_auto.php?language=English&responsable=1&limit=50', false, stream_context_create($arrContextOptions));
sleep(2);
$english_user2 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_auto.php?language=English&responsable=22&limit=50', false, stream_context_create($arrContextOptions));
sleep(2);
$english_user3 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_auto.php?language=English&responsable=23&limit=50', false, stream_context_create($arrContextOptions));
sleep(5);
$mms_folder_fix = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/MMS_folder_checkv2.php', false, stream_context_create($arrContextOptions));
sleep(5);
$mms_folder_fix1 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/MMS_folder_checkv2.php', false, stream_context_create($arrContextOptions));
sleep(5);
$mms_folder_fix2 = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/MMS_folder_checkv2.php', false, stream_context_create($arrContextOptions));
//sleep(5);
//$duplicate_fix = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/1phone_duplicate_control.php');
//sleep(5);
//$duplicate_delete = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/1duplicate_delete.php');

sleep(5);
$sosa_resp_fix = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_external_department.php?LANGUAGE=English', false, stream_context_create($arrContextOptions));
sleep(5);
$sosa_resp_fix = file_get_contents('https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/000responsable_fix_external_department.php?LANGUAGE=Spanish', false, stream_context_create($arrContextOptions));


?>