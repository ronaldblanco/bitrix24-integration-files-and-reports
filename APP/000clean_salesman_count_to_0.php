<?php
echo "Created date is " . date("Y-m-d h:i:sa");
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require("/home/bitrix/www/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require("/home/bitrix/www/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//echo "Hello!";
$users_group_len = DBGet("SELECT ID,b_user.PERSONAL_ICQ FROM b_user WHERE b_user.PERSONAL_ICQ > 0 LIMIT 250;");
var_dump($users_group_len);

foreach($users_group_len as $user){
        echo "#";
        $userupdate =  api (
                'user.update' ,
                        [
                                'ID' => $user['ID'],
                                'PERSONAL_ICQ' => 0

                ]);
        //var_dump($userupdate);
}

?>