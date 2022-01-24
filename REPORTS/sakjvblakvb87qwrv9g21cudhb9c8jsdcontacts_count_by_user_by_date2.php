<?php

date_default_timezone_set('America/New_York');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//$from = date("Y-m-d");
//$to = date("Y-m-d");
$from = date("Y-m-d",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));
$to = date("Y-m-d",strtotime(date("Y-m-d h:i:s") . ' +7 hour'));

if(isset($_POST['from'])) $from = $_POST['from'];
if(isset($_POST['to'])) $to = $_POST['to'];

$to2= strtotime($to . ' +1 day');
$to3 = date("Y-m-d",$to2);

$daily_report = DBGet("SELECT MAX(b_crm_contact.DATE_CREATE) as Date,COUNT(b_crm_contact.ID)
 as Contacts_Count,MAX(b_uts_crm_contact.UF_CRM_1590674689393) as Last_Language,ASSIGNED_BY_ID as
 UserID,b_user.NAME,b_user.LAST_NAME,b_user.WORK_DEPARTMENT,b_user.WORK_POSITION,b_user.WORK_NOTES,b_user_index.UF_DEPARTMENT_NAME
 FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND 
CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-08:00') >= '".$from."%' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-08:00') <='".$to3."%' AND (SOURCE_ID = 15 OR SOURCE_ID = 16 OR SOURCE_ID = 17 OR SOURCE_ID = 18) INNER JOIN 
b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON
 b_user_index.USER_ID = b_user.ID WHERE b_user.WORK_NOTES <> 'ext' AND b_user.WORK_NOTES <> 'ext/sp' GROUP BY ASSIGNED_BY_ID ORDER BY Date asc, COUNT(ID) asc limit 500;");

$prospects = DBGet("
SELECT ASSIGNED_BY_ID as UserID,
 COUNT(b_uts_crm_contact.UF_CRM_1590674689393) as Prospects FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID 
INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID AND b_uts_crm_contact.UF_CRM_1591040450352 LIKE '%i:167%' INNER JOIN b_user_index ON
 b_user_index.USER_ID = b_user.ID GROUP BY UserID limit 500");

$total = DBGet("SELECT COUNT(ID) as Total_contacts FROM b_crm_contact WHERE CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-08:00') >= '".$from."%' AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-08:00') <='".$to3."%' AND (SOURCE_ID = 15 OR SOURCE_ID = 16 OR SOURCE_ID = 17 OR SOURCE_ID = 18) GROUP BY TYPE_ID ORDER BY TYPE_ID LIMIT 5;");
//var_dump($total);


$period = new DatePeriod( new DateTime($from),
     new DateInterval('P1D'),
     new DateTime($to3)
);

$total9=0;
foreach($period as $date){
	$filename= '../APP/logs/total_'.$date->format("m.d.y").'.txt';
	$total9 += file_get_contents($filename, true);

}


function find_user_info($userid,$users){
	for($i = 0;$i<count($users);$i++){
		$key = array_search($userid, $users[$i]);
		if ($key != false) return $users[$i]['NAME'].' '.$users[$i]['LAST_NAME'];
	}
	return false;
}

function find_user_dep($userid,$users ){
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


?>
<title>Contacts Count by User! Quick Reports. Local server-side application in Bitrix24</title>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contacts Count by User! Quick Reports. Local server-side application in Bitrix24</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">

	<style>
  		h2 {color:blue;}
  		p {color:green;}
		.right {
	  text-align: right;

		}
		.margin-top {
    margin-top: 35px;
}
	</style>


</head>

<body>

	<center><h2 class = "margin-top">Leads Control Report from Zapier</h2>

	<form action="https://crm.api.com/bitrix/admin/dhhdbw73723934dvrgintegration/REPORTS/sakjvblakvb87qwrv9g21cudhb9c8jsdcontacts_count_by_user_by_date2.php" method="POST">
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
    <div class="col-md-6">
		<h4> Total Leads Inside Bitrix from Zapier: <?php echo $alltotal;?>  </h4>
</div>
 <div class="col-md-6">
<h4 class="right"> Total Leads Comming to Bitrix from Zapier:   <?php echo $total9;?>   </h4>
</div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-sm-12">

<table name="myTable" id="myTable" class="table table-striped table-bordered table-hover">
	<thead class="thead-dark">
	<tr>
	  <th scope="col">DATE</th>
		<th scope="col">NAME</th>
		<th scope="col">LAST NAME</th>
	  <th scope="col">CONTACTS COUNT</th>
	<th scope="col">PROSPECTS</th>
	  <th scope="col">LAST LANGUAGE</th>

		<!--<th scope="col">WORK DEPARTMENT</th>-->
		<th scope="col">WORK POSITION</th>
		<th scope="col">WORK NOTES</th>
		<th scope="col">DEPARTMENT NAME</th>

    </tr>	
	</thead>
	<tbody>
	<?php foreach ($daily_report as $row): array_map('htmlentities', $row);?>
	<tr>
	  <th scope="row"><?php echo date("Y-m-d",strtotime($row['Date']));?></th>
	<td><?php echo $row['NAME'];?></td>
	  <td><?php echo $row['LAST_NAME'];?></td>
	  <td><?php echo $row['Contacts_Count'];?></td>
<td><?php echo find_Prospects($row['UserID'])-1;?> </td>
	  <td><?php echo $row['Last_Language'];?></td>

	  <!--<td><?php echo $row['WORK_DEPARTMENT'];?></td>-->
		<td><?php echo $row['WORK_POSITION'];?></td>
	  <td><?php echo $row['WORK_NOTES'];?></td>
	  <td><?php echo $row['UF_DEPARTMENT_NAME'];?></td>

    </tr>
	<?php endforeach; ?>


	</tbody>
</table>
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