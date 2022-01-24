<?php

date_default_timezone_set('America/New_York');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

$day = date("Y-m-d");
if(isset($_POST['day'])) $day = $_POST['day'];
$daily_report = DBGet("SELECT MAX(b_crm_contact.DATE_CREATE) as Date,COUNT(b_crm_contact.ID) as Contacts_Count,MAX(b_uts_crm_contact.UF_CRM_1590674689393) as Last_Language,ASSIGNED_BY_ID as UserID,b_user.NAME,b_user.LAST_NAME,b_user.WORK_DEPARTMENT,b_user.WORK_POSITION,b_user.WORK_NOTES,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID AND CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-08:00') LIKE '".$day."%' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) desc LIMIT 100;");

$total = DBGet("SELECT COUNT(ID) as Total_contacts FROM b_crm_contact WHERE CONVERT_TZ(b_crm_contact.DATE_CREATE,'+00:00','-08:00') LIKE '".$day."%' GROUP BY TYPE_ID ORDER BY TYPE_ID LIMIT 5;");
//var_dump($total);

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contacts Count by User! Quick Reports. Local server-side application in Bitrix24</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		
	<style>
  		h2 {color:blue;}
  		p {color:green;}
	</style>
</head>

<body>

	<center><h2>Contacts Count by User per day!</h2>

	<form action="https://crm.api.com/bitrix/admin/dhhdbw73723934dvrgintegration/REPORTS/sakjvblakvb87qwrv9g21cudhb9c8jsdcontacts_count_by_user_by_date.php" method="POST">
		<p>DAY</p> <input type="text" name="day" value="<?php echo $day?>">

		<input type="submit" value="Send">
	</form>
	</center><br/>

<?php
$alltotal = 0;
	for($i = 0; $i < count($total);$i++){
		$alltotal = $alltotal + $total[$i]['Total_contacts'];
	}
?>

	<h4>Total contacts for the day: <?php echo $alltotal;?></h4>

	<table class="table table-striped table-bordered table-hover">
	<thead class="thead-dark">
	<tr>
	  <th scope="col">Date</th>
	  <th scope="col">Contacts_Count</th>
	  <th scope="col">Last_Language</th>
		<th scope="col">Name</th>
		<th scope="col">Last_Name</th>
		<th scope="col">WORK_DEPARTMENT</th>
		<th scope="col">WORK_POSITION</th>
		<th scope="col">WORK_NOTES</th>
		<th scope="col">DEPARTMENT_NAME</th>

    </tr>	
	</thead>
	<tbody>
	<?php foreach ($daily_report as $row): array_map('htmlentities', $row);?>
	<tr>
	  <th scope="row"><?php echo date("Y-m-d",strtotime($row['Date']));?></th>
	  <td><?php echo $row['Contacts_Count'];?></td>
	  <td><?php echo $row['Last_Language'];?></td>
		<td><?php echo $row['NAME'];?></td>
	  <td><?php echo $row['LAST_NAME'];?></td>
	  <td><?php echo $row['WORK_DEPARTMENT'];?></td>
		<td><?php echo $row['WORK_POSITION'];?></td>
	  <td><?php echo $row['WORK_NOTES'];?></td>
	  <td><?php echo $row['UF_DEPARTMENT_NAME'];?></td>

    </tr>
	<?php endforeach; ?>


	</tbody>
</table>



	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>

