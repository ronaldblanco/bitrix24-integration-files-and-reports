<?php

echo "Wellcome to Merchant Payment Handler!";

$all_info = json_decode(file_get_contents("php://input"),true);

//$title = isset($all_info['TITLE']) ? $all_info['TITLE'] : '';

$content = $all_info;
$content["date"] = date("F j, Y, g:i a");
$content['POST'] = $_POST;
file_put_contents("log_payment.txt", print_r($content, true), FILE_APPEND);

?>