<?php

date_default_timezone_set('America/New_York');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//$from = date("Y-m-d");
//$to = date("Y-m-d");
$from = date("Y-m-d",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));
$to = date("Y-m-d",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));

//echo date("Y-m-d h:i:s",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));

if(isset($_POST['from'])) $from = $_POST['from'];
if(isset($_POST['to'])) $to = $_POST['to'];

$to2= strtotime($to . ' +1 day');
$to3 = date("Y-m-d",$to2);

$day = date("Y-m-d");
if(isset($_POST['day'])) $day = $_POST['day'];

$daily_report = DBGet("SELECT MAX(b_crm_contact.DATE_CREATE) as Date,COUNT(b_crm_contact.ID) as Contacts_Count,MAX(b_uts_crm_contact.UF_CRM_1590674689393) as Last_Language,ASSIGNED_BY_ID as UserID,b_user.NAME,b_user.LAST_NAME,b_user.WORK_DEPARTMENT,b_user.WORK_POSITION,b_user.WORK_NOTES,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND (CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') >= '".$from."%' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') <='".$to3."%') INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user.WORK_NOTES <> 'ext' AND b_user.WORK_NOTES <> 'ext/sp' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) desc LIMIT 100;");
$total = DBGet("SELECT COUNT(ID) as Total_contacts FROM b_crm_contact WHERE CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') >= '".$from."%' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') <='".$to3."%' AND SOURCE_ID <> '18' GROUP BY TYPE_ID ORDER BY TYPE_ID LIMIT 5;");
//$activesalesmanscount = DBGet("SELECT COUNT(b_crm_contact.ID) as idcount,ASSIGNED_BY_ID FROM b_crm_contact INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID WHERE CONVERT_TZ(DATE_CREATE,'+00:00','-05:00') >= '".$from."%' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') <='".$to3."%' AND b_user.WORK_NOTES <> 'out' AND b_user.WORK_NOTES <> 'adm' AND b_user.WORK_NOTES <> 'ext' AND b_user.WORK_NOTES <> 'ext/sp' GROUP BY ASSIGNED_BY_ID LIMIT 250;");
$activesalesmanscount = DBGet("SELECT ID FROM b_user WHERE b_user.ACTIVE = 'Y' AND b_user.WORK_NOTES <> 'out' AND b_user.WORK_NOTES <> 'adm' AND b_user.WORK_NOTES <> 'ext' AND b_user.WORK_NOTES <> 'ext/sp' AND b_user.WORK_NOTES <> 'ext/all' LIMIT 250;");

/*$daily_report = DBGet("SELECT MAX(b_crm_contact.DATE_CREATE) as Date,COUNT(b_crm_contact.ID) as Contacts_Count,MAX(b_uts_crm_contact.UF_CRM_1590674689393) as Last_Language,ASSIGNED_BY_ID as UserID,b_user.NAME,b_user.LAST_NAME,b_user.WORK_DEPARTMENT,b_user.WORK_POSITION,b_user.WORK_NOTES,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND (b_crm_contact.DATE_CREATE >= '".$from."%' AND b_crm_contact.DATE_CREATE <='".$to3."%') INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user.WORK_NOTES <> 'ext' AND b_user.WORK_NOTES <> 'ext/sp' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) desc LIMIT 100;");
$total = DBGet("SELECT COUNT(ID) as Total_contacts FROM b_crm_contact WHERE b_crm_contact.DATE_CREATE >= '".$from."%' AND b_crm_contact.DATE_CREATE <='".$to3."%' AND SOURCE_ID <> '18' GROUP BY TYPE_ID ORDER BY TYPE_ID LIMIT 5;");
$activesalesmanscount = DBGet("SELECT COUNT(b_crm_contact.ID) as idcount,ASSIGNED_BY_ID FROM b_crm_contact INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID WHERE DATE_CREATE >= '".$from."%' AND b_crm_contact.DATE_CREATE <='".$to3."%' AND b_user.WORK_NOTES <> 'out' AND b_user.WORK_NOTES <> 'adm' AND b_user.WORK_NOTES <> 'ext' AND b_user.WORK_NOTES <> 'ext/sp' GROUP BY ASSIGNED_BY_ID LIMIT 250;");
*/
$codes = array('sp' => 'Only Spanish', 'out' => 'Not Receiving', 'default' => 'Only English', 'adm' => 'Manager', 'all' => 'All');

/*$prospects = DBGet("
SELECT ASSIGNED_BY_ID as UserID,
 COUNT(b_uts_crm_contact.UF_CRM_1590674689393) as Prospects FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID 
INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_uts_crm_contact.UF_CRM_1591040450352 LIKE '%i:167%' INNER JOIN b_user_index ON
b_user_index.USER_ID = b_user.ID GROUP BY UserID limit 500");*/

$manual_daily = DBGet("SELECT ASSIGNED_BY_ID as UserID,COUNT(b_crm_contact.ID) as Prospects FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID WHERE (CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') >= '".$from." 00:00:00' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-05:00') <='".$to3." 23:59:59') AND (SOURCE_ID = 'CALL' OR SOURCE_ID = 'E-mail' OR SOURCE_ID = '5' OR SOURCE_ID = '6' OR SOURCE_ID = '7' OR SOURCE_ID = '8' OR SOURCE_ID = '9' OR SOURCE_ID = '10' OR SOURCE_ID = '11' OR SOURCE_ID = '12' OR SOURCE_ID = '13' OR SOURCE_ID = '14' OR SOURCE_DESCRIPTION = 'SMS') GROUP BY UserID limit 250;");
$prospects = DBGet("SELECT ASSIGNED_BY_ID as UserID,COUNT(b_crm_contact.ID) as Prospects FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID WHERE b_uts_crm_contact.UF_CRM_1591040450352 LIKE '%i:167%' GROUP BY UserID limit 250;");

/*$manual_daily = DBGet("SELECT ASSIGNED_BY_ID as UserID,COUNT(b_crm_contact.ID) as Prospects FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID WHERE (b_crm_contact.DATE_CREATE >= '".$from." 00:00:00' AND b_crm_contact.DATE_CREATE <='".$to3." 23:59:59') AND (SOURCE_ID = 'CALL' OR SOURCE_ID = 'E-mail' OR SOURCE_ID = '5' OR SOURCE_ID = '6' OR SOURCE_ID = '7' OR SOURCE_ID = '8' OR SOURCE_ID = '9' OR SOURCE_ID = '10' OR SOURCE_ID = '11' OR SOURCE_ID = '12' OR SOURCE_ID = '13' OR SOURCE_ID = '14' OR SOURCE_DESCRIPTION = 'SMS') GROUP BY UserID limit 250;");
$prospects = DBGet("SELECT ASSIGNED_BY_ID as UserID,COUNT(b_crm_contact.ID) as Prospects FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID WHERE b_uts_crm_contact.UF_CRM_1591040450352 LIKE '%i:167%' GROUP BY UserID limit 250;");
*/
//var_dump($prospects);

function find_user_info($userid,$users){
	for($i = 0;$i<count($users);$i++){
		$key = array_search($userid, $users[$i]);
		if ($key != false) return $users[$i]['NAME'].' '.$users[$i]['LAST_NAME'];
	}
	return false;
}

function find_user_dep($userid,$users){
	for($i = 0;$i<count($users);$i++){
		$key = array_search($userid, $users[$i]);
		if ($key != false) return $users[$i]['WORK_DEPARTMENT'].'->'.$users[$i]['WORK_POSITION'].'->'.$users[$i]['WORK_NOTES'].'->'.$users[$i]['UF_DEPARTMENT_NAME'];
	}
	return false;
}
function find_Prospects($userid){
	for($i = 0;$i<count($GLOBALS['prospects']);$i++){
		$key = array_search($userid, $GLOBALS['prospects'][$i]);
		if ($key != false) return $GLOBALS['prospects'][$i]['Prospects'];
	}
	return false;
}

function find_Prospects2($userid){
	for($i = 0;$i<count($GLOBALS['prospects']);$i++){
		//$key = array_search($userid, $GLOBALS['prospects'][$i]);
		if ($GLOBALS['prospects'][$i]["UserID"] == $userid) return $GLOBALS['prospects'][$i]['Prospects'];
	}
	return false;
}

function find_manual_Prospects2($userid){
	for($i = 0;$i<count($GLOBALS['manual_daily']);$i++){
		//$key = array_search($userid, $GLOBALS['prospects'][$i]);
		if ($GLOBALS['manual_daily'][$i]["UserID"] == $userid) return $GLOBALS['manual_daily'][$i]['Prospects'];
	}
	return false;
}

function code_traslation($code){
	//var_dump($code);
	if($code == "") return $GLOBALS['codes']['default'];
	else return $GLOBALS['codes'][$code];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contacts Count by User!</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">

	<style>
  		h2 {color:blue;}
  		p {color:green;}
		.green{color:green;}
		.right { text-align: right;		}
		.margin-tops {  margin-top: 35px !important;}
	</style>
</head>

<body>

	<center><h2 class = "margin-tops">Contacts Count by User</h2>

	<form action="https://crm.api.com/bitrix/admin/dhhdbw73723934dvrgintegration/REPORTS/sakjvblakvb87qwrv9g21cudhb9c8jsdcontacts_count_by_user_by_date3.php" method="POST">
<div class="container">
  <div class="row">
    <div class="col-md-9">
</div>
<div class="col-md-3">
<p>FROM &nbsp;<input type="text" name="from" id="from"  value="<?php echo $from?>"></p>
 <p>TO   &nbsp; &nbsp;  &nbsp; &nbsp;<input type="text" name="to" id="to"  value="<?php echo $to?>"></p>
		<input type="submit" value="Send">
</div>
  </div>
</div>
	</form>
	</center><br/>

<?php
$alltotal = 0;
	for($i = 0; $i < count($total);$i++){
		$alltotal = $alltotal + $total[$i]['Total_contacts'];
	}
?>

<div class="container">
  <div class="row">
    <div class="col-sm-6">
		<h4>From <?php echo $from?> To <?php echo $to?></h4>
		<h4>Total contacts: <span class = "green">  <?php echo $alltotal;?> </span></h4>
</div>
 <div class="col-sm-6">
	 <h4 class="right"> Active Salesmans: <span class = "green"><?php echo count($activesalesmanscount);?></span></h4>
	 <h4 class="right"> Count Contacts Promedy per Salesman: <span class = "green"><?php echo round($alltotal / count($activesalesmanscount),2);?></span></h4>
</div>

  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-sm-12">

	<table name="myTable" id="myTable"  class="table table-striped table-bordered table-hover">
	<thead class="thead-dark">
	<tr>
<th scope="col">Name</th>
<th scope="col">L Name</th>
	  <th scope="col">Contacts Count</th>
	  <th scope="col">Last Language</th>
<th scope="col">Actual Prospects</th>
<th scope="col">Daily Manual Contacts</th>

		<!--<th scope="col">Work Deparment</th>-->
		<th scope="col">Work Position</th>
		<th scope="col">Work Notes</th>
		<th scope="col">Department Name</th>

    </tr>	
	</thead>
	<tbody>
	<?php foreach ($daily_report as $row): array_map('htmlentities', $row);?>
	<tr>
	<td><?php echo $row['NAME'];?></td>
  <td><?php echo $row['LAST_NAME'];?></td>
	  <td><?php echo $row['Contacts_Count'];?></td>
	  <td><?php echo $row['Last_Language'];?></td>
<td><?php echo find_Prospects2($row['UserID'])-1;?> </td>
<td><?php echo find_manual_Prospects2($row['UserID']);?> </td>

	  <!--<td><?php echo $row['WORK_DEPARTMENT'];?></td>-->
		<td><?php echo $row['WORK_POSITION'];?></td>
	  <td><?php echo code_traslation($row['WORK_NOTES'])." (".$row['WORK_NOTES'].")";?></td>
	  <td><?php echo $row['UF_DEPARTMENT_NAME'];?></td>

    </tr>
	<?php endforeach; ?>


	</tbody>
</table>
		<p>This report does not containt the external department information!</p>
</div>
  </div>
</div>

<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" ></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" ></script>



  <script>
  $( function() {
    $( "#from" ).datepicker({ dateFormat: 'yy-mm-dd' });

  } );

 $( function() {
    $( "#to" ).datepicker({ dateFormat: 'yy-mm-dd' });

  } );
$(document).ready( function () {
    $('#myTable').DataTable();
} );
  </script>
</body>
</html>

