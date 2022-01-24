<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

$contacts = DBGet("SELECT ID,ASSIGNED_BY_ID,b_uts_crm_contact.UF_CRM_1591040450352 FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID ORDER BY ID LIMIT 5;"); //Query to database
		//var_dump($users_group_len[0]["ASSIGNED_BY_ID"]); //User with less contacts
//$fields = DBGet("SELECT * FROM b_user_field_enum LIMIT 5;"); //Query to database

$items = array(
                [
                    "ID"=> "36",
                    "VALUE"=> "Influencer"
                ],
                [
                    "ID"=> "37",
                    "VALUE"=> "closed dr. tachmes"
                ],
                [
                    "ID"=> "38",
                    "VALUE"=> "English manual"
                ],
                [
                    "ID"=> "41",
                    "VALUE"=> "Chynah Hales Referrals"
                ],
                [
                    "ID"=> "42",
                    "VALUE"=> "Closed Dr Brewster"
                ],
                [
                    "ID"=> "43",
                    "VALUE"=> "texy"
                ],
                [
                    "ID"=> "44",
                    "VALUE"=> "Instagram_Deposit"
                ],
                [
                    "ID"=> "45",
                    "VALUE"=> "waiting on photos"
                ],
                [
                    "ID"=> "46",
                    "VALUE"=> "quote sent"
                ],
                [
                    "ID"=> "47",
                    "VALUE"=> "closed dr.salas"
                ],
                [
                    "ID"=> "48",
                    "VALUE"=> "wrong number"
                ],
                [
                    "ID"=> "49",
                    "VALUE"=> "call later"
                ],
                [
                    "ID"=> "50",
                    "VALUE"=> "Tiana Vanhorne Referral"
                ],
                [
                    "ID"=> "51",
                    "VALUE"=> "closed Dr Tachmes"
                ],
                [
                    "ID"=> "52",
                    "VALUE"=> "Closed Dr. Brewster"
                ],
                [
                    "ID"=> "53",
                    "VALUE"=> "Closed Dr. Salas"
                ],
                [
                    "ID"=> "54",
                    "VALUE"=> "closed Dr Salas"
                ],
                [
                    "ID"=> "55",
                    "VALUE"=> "no longer interested"
                ],
                [
                    "ID"=> "56",
                    "VALUE"=> "refunded"
                ],
                [
                    "ID"=> "57",
                    "VALUE"=> "does not want to be contacted"
                ],
                [
                    "ID"=> "58",
                    "VALUE"=> "Sent Link"
                ],
                [
                    "ID"=> "59",
                    "VALUE"=> "BOOKED"
                ],
                [
                    "ID"=> "60",
                    "VALUE"=> "sent sdms"
                ],
                [
                    "ID"=> "61",
                    "VALUE"=> "clsoed"
                ],
                [
                    "ID"=> "62",
                    "VALUE"=> "closed Dr Brewster"
                ],
                [
                    "ID"=> "63",
                    "VALUE"=> "English (Manual)"
                ],
                [
                    "ID"=> "64",
                    "VALUE"=> "Spanish (Manual)"
                ],
                [
                    "ID"=> "65",
                    "VALUE"=> "heidy artista instagram"
                ],
                [
                    "ID"=> "66",
                    "VALUE"=> "emIL"
                ],
                [
                    "ID"=> "67",
                    "VALUE"=> "email sent sms"
                ],
                [
                    "ID"=> "68",
                    "VALUE"=> "emaiol"
                ],
                [
                    "ID"=> "69",
                    "VALUE"=> "emaisl"
                ],
                [
                    "ID"=> "70",
                    "VALUE"=> "snrt sms"
                ],
                [
                    "ID"=> "71",
                    "VALUE"=> "Closed Dr. Tachmes"
                ],
                [
                    "ID"=> "72",
                    "VALUE"=> "BOOKED DR. TACHMES"
                ],
                [
                    "ID"=> "73",
                    "VALUE"=> "never answered"
                ],
                [
                    "ID"=> "74",
                    "VALUE"=> "Follow up"
                ],
                [
                    "ID"=> "75",
                    "VALUE"=> "em txt"
                ],
                [
                    "ID"=> "76",
                    "VALUE"=> "closed"
                ],
                [
                    "ID"=> "77",
                    "VALUE"=> "booked with Bass"
                ],
                [
                    "ID"=> "78",
                    "VALUE"=> "booked with William"
                ],
                [
                    "ID"=> "79",
                    "VALUE"=> "closed with Dr Bass"
                ],
                [
                    "ID"=> "80",
                    "VALUE"=> "closed with Dr william"
                ],
                [
                    "ID"=> "81",
                    "VALUE"=> "CLOSED WITH BASS"
                ],
                [
                    "ID"=> "82",
                    "VALUE"=> "CLOSED WITH WILLIAM"
                ],
                [
                    "ID"=> "83",
                    "VALUE"=> "booked"
                ],
                [
                    "ID"=> "84",
                    "VALUE"=> "Duplicate Contacts"
                ],
                [
                    "ID"=> "85",
                    "VALUE"=> "sent sm s"
                ],
                [
                    "ID"=> "86",
                    "VALUE"=> "Spanish Front Desk"
                ],
                [
                    "ID"=> "87",
                    "VALUE"=> "pending_corona"
                ],
                [
                    "ID"=> "88",
                    "VALUE"=> "Retargeting_Follow_Up_3"
                ],
                [
                    "ID"=> "89",
                    "VALUE"=> "Retargeting_Follow_Up_2"
                ],
                [
                    "ID"=> "90",
                    "VALUE"=> "Retargeting_Follow_Up_1"
                ],
                [
                    "ID"=> "91",
                    "VALUE"=> "sent evaluation"
                ],
                [
                    "ID"=> "92",
                    "VALUE"=> "Not a good candidate"
                ],
                [
                    "ID"=> "93",
                    "VALUE"=> "Exit API Check"
                ],
                [
                    "ID"=> "94",
                    "VALUE"=> "API Update Contact"
                ],
                [
                    "ID"=> "95",
                    "VALUE"=> "API New Contact"
                ],
                [
                    "ID"=> "96",
                    "VALUE"=> "Sent Follow Up"
                ],
                [
                    "ID"=> "97",
                    "VALUE"=> "Texted and emailed Follow up"
                ],
                [
                    "ID"=> "98",
                    "VALUE"=> "Busy Line"
                ],
                [
                    "ID"=> "99",
                    "VALUE"=> "Number Out Of Service"
                ],
                [
                    "ID"=> "100",
                    "VALUE"=> "Medical Condition"
                ],
                [
                    "ID"=> "101",
                    "VALUE"=> "Delete"
                ],
                [
                    "ID"=> "102",
                    "VALUE"=> "Hung Up"
                ],
                [
                    "ID"=> "103",
                    "VALUE"=> "Retargeting-Waiting-On-Pics"
                ],
                [
                    "ID"=> "104",
                    "VALUE"=> "Retargeting-No-Answ-2"
                ],
                [
                    "ID"=> "105",
                    "VALUE"=> "Retargeting-No-Answ-1"
                ],
                [
                    "ID"=> "106",
                    "VALUE"=> "Reargeting-SxOtherClinic"
                ],
                [
                    "ID"=> "107",
                    "VALUE"=> "financiamiento"
                ],
                [
                    "ID"=> "108",
                    "VALUE"=> "Retargeting-Transferred"
                ],
                [
                    "ID"=> "109",
                    "VALUE"=> "waiting on down payment"
                ],
                [
                    "ID"=> "110",
                    "VALUE"=> "Gmail Bounced Contacts"
                ],
                [
                    "ID"=> "111",
                    "VALUE"=> "received photo"
                ],
                [
                    "ID"=> "112",
                    "VALUE"=> "sent pics"
                ],
                [
                    "ID"=> "113",
                    "VALUE"=> "waiting on pics-Steph"
                ],
                [
                    "ID"=> "114",
                    "VALUE"=> "sent text and evaluation form"
                ],
                [
                    "ID"=> "115",
                    "VALUE"=> "waiting on patient"
                ],
                [
                    "ID"=> "116",
                    "VALUE"=> "putting down payment on Friday January 31st"
                ],
                [
                    "ID"=> "117",
                    "VALUE"=> "coming in for consultation"
                ],
                [
                    "ID"=> "118",
                    "VALUE"=> "prices"
                ],
                [
                    "ID"=> "119",
                    "VALUE"=> "waiting for consultation"
                ],
                [
                    "ID"=> "120",
                    "VALUE"=> "waiting on call back"
                ],
                [
                    "ID"=> "121",
                    "VALUE"=> "not a candidate"
                ],
                [
                    "ID"=> "122",
                    "VALUE"=> "Influencer Externo"
                ],
                [
                    "ID"=> "123",
                    "VALUE"=> "sent email"
                ],
                [
                    "ID"=> "124",
                    "VALUE"=> "waiting on pics-steph"
                ],
                [
                    "ID"=> "125",
                    "VALUE"=> "Sent SMS- Steph"
                ],
                [
                    "ID"=> "126",
                    "VALUE"=> "Surgery411"
                ],
                [
                    "ID"=> "127",
                    "VALUE"=> "Shopping Around"
                ],
                [
                    "ID"=> "128",
                    "VALUE"=> "going to send photos"
                ],
                [
                    "ID"=> "129",
                    "VALUE"=> "email"
                ],
                [
                    "ID"=> "130",
                    "VALUE"=> "Kristine Vacations"
                ],
                [
                    "ID"=> "131",
                    "VALUE"=> "not ready"
                ],
                [
                    "ID"=> "132",
                    "VALUE"=> "not interersted"
                ],
                [
                    "ID"=> "133",
                    "VALUE"=> "not a canidate"
                ],
                [
                    "ID"=> "134",
                    "VALUE"=> "no answer"
                ],
                [
                    "ID"=> "135",
                    "VALUE"=> "not intersted"
                ],
                [
                    "ID"=> "136",
                    "VALUE"=> "Karel Test"
                ],
                [
                    "ID"=> "137",
                    "VALUE"=> "Email Typos"
                ],
                [
                    "ID"=> "138",
                    "VALUE"=> "Clean List"
                ],
                [
                    "ID"=> "139",
                    "VALUE"=> "Sent SMS - Kirstyn"
                ],
                [
                    "ID"=> "140",
                    "VALUE"=> "Waiting on pics - Kirstyn"
                ],
                [
                    "ID"=> "141",
                    "VALUE"=> "Temporal Influencer re-assigned"
                ],
                [
                    "ID"=> "142",
                    "VALUE"=> "CARTEL_CREW"
                ],
                [
                    "ID"=> "143",
                    "VALUE"=> "Sending Evaluation Kyrstin"
                ],
                [
                    "ID"=> "144",
                    "VALUE"=> "Sending Evaluation Lizzie"
                ],
                [
                    "ID"=> "145",
                    "VALUE"=> "text and emailed medical evaluation"
                ],
                [
                    "ID"=> "146",
                    "VALUE"=> "Voicemail Kirstyn"
                ],
                [
                    "ID"=> "147",
                    "VALUE"=> "Voicemail"
                ],
                [
                    "ID"=> "148",
                    "VALUE"=> "Call Rejected"
                ],
                [
                    "ID"=> "149",
                    "VALUE"=> "Call Answered"
                ],
                [
                    "ID"=> "150",
                    "VALUE"=> "Walked in"
                ],
                [
                    "ID"=> "151",
                    "VALUE"=> "Does not respond to any call"
                ],
                [
                    "ID"=> "152",
                    "VALUE"=> "Refused by surgeon"
                ],
                [
                    "ID"=> "153",
                    "VALUE"=> "Not a Candidate"
                ],
                [
                    "ID"=> "154",
                    "VALUE"=> "English front desk"
                ],
                [
                    "ID"=> "155",
                    "VALUE"=> "Closed Dr Bass"
                ],
                [
                    "ID"=> "156",
                    "VALUE"=> "Tummy Tuck"
                ],
                [
                    "ID"=> "157",
                    "VALUE"=> "Breast Reduction"
                ],
                [
                    "ID"=> "158",
                    "VALUE"=> "Breast Lift"
                ],
                [
                    "ID"=> "159",
                    "VALUE"=> "Call them later"
                ],
                [
                    "ID"=> "160",
                    "VALUE"=> "No Answer"
                ],
                [
                    "ID"=> "161",
                    "VALUE"=> "Have Status"
                ],
                [
                    "ID"=> "162",
                    "VALUE"=> "Not Interested"
                ],
                [
                    "ID"=> "163",
                    "VALUE"=> "No Money"
                ],
                [
                    "ID"=> "164",
                    "VALUE"=> "Researching"
                ],
                [
                    "ID"=> "165",
                    "VALUE"=> "Very Interested"
                ],
                [
                    "ID"=> "166",
                    "VALUE"=> "Other"
                ],
                [
                    "ID"=> "167",
                    "VALUE"=> "Prospect"
                ],
                [
                    "ID"=> "168",
                    "VALUE"=> "Influencer Program"
                ],
                [
                    "ID"=> "169",
                    "VALUE"=> "Mommy Makeover"
                ],
                [
                    "ID"=> "170",
                    "VALUE"=> "Lipo"
                ],
                [
                    "ID"=> "171",
                    "VALUE"=> "Breast Augmentation"
                ],
                [
                    "ID"=> "172",
                    "VALUE"=> "BBL"
                ],
                [
                    "ID"=> "173",
                    "VALUE"=> "Closed Dr William"
                ],
                [
                    "ID"=> "310",
                    "VALUE"=> "Spanish Lead"
                ],
                [
                    "ID"=> "311",
                    "VALUE"=> "English Lead"
                ]
            );

var_dump($contacts);

var_dump($items);

?>