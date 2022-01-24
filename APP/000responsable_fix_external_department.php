<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

$lan = $_GET['LANGUAGE']?$_GET['LANGUAGE']:'English';

$contacts_ext = DBGet("SELECT ID,b_uts_crm_contact.UF_CRM_1590674689393 FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID and b_uts_crm_contact.UF_CRM_1590674689393 = '".$lan."' WHERE SOURCE_ID = '18' AND ASSIGNED_BY_ID <> '155' AND ASSIGNED_BY_ID <> '152' AND ASSIGNED_BY_ID <> '157' AND ASSIGNED_BY_ID <> '192' AND ASSIGNED_BY_ID <> '194' AND ASSIGNED_BY_ID <> '195' AND ASSIGNED_BY_ID <> '196' AND ASSIGNED_BY_ID <> '275' LIMIT 50;");

$arrContextOptions=array(
      "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  

foreach($contacts_ext as $contact){

	echo $contact['ID'].";";
	$proccess = file_get_contents("https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/find_userv3.php?CONTACT_ID=".$contact['ID']."&LANGUAGE=".$contact['UF_CRM_1590674689393']."&KEY=sjhffgJAFDHGD78V87W8E2DHGUB398YCG93YGDCWCD87CY&SOURCE=Typeform_ExtDep&SOURCED=", false, stream_context_create($arrContextOptions));
	//var_dump($proccess);

}

?>