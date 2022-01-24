<?php

date_default_timezone_set('America/New_York');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

$users = DBGet("SELECT ID,b_user.NAME,b_user.LAST_NAME,b_user.WORK_DEPARTMENT,b_user.WORK_POSITION,b_user.WORK_NOTES,b_user_index.UF_DEPARTMENT_NAME,EMAIL,LAST_LOGIN,DATE_REGISTER,PERSONAL_MOBILE,WORK_PHONE,LAST_ACTIVITY_DATE,PERSONAL_ICQ from b_user inner join b_user_index on b_user_index.USER_ID = b_user.ID where b_user_index.UF_DEPARTMENT_NAME <> 'Chat bots' and b_user_index.UF_DEPARTMENT_NAME <> 'IT' limit 250;");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users Information!</title>
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

	<center><h2 class = "margin-tops">Users Information!</h2>

	<form action="https://crm.api.com/bitrix/admin/dhhdbw73723934dvrgintegration/REPORTS/sakjvblakvb87qwrv9g21cudhb9c8jsdcontacts_count_by_user_by_date3.php" method="POST">
<div class="container">
  <div class="row">
    <div class="col-md-9">
</div>
<div class="col-md-3">
<!--<p>FROM &nbsp;<input type="text" name="from" id="from"  value="<?php echo $from?>"></p>
 <p>TO   &nbsp; &nbsp;  &nbsp; &nbsp;<input type="text" name="to" id="to"  value="<?php echo $to?>"></p>
		<input type="submit" value="Send">-->
</div>
  </div>
</div>
	</form>
	</center><br/>

<?php
	/*$alltotal = 0;
	for($i = 0; $i < count($total);$i++){
		$alltotal = $alltotal + $total[$i]['Total_contacts'];
}*/
?>

<div class="container">
  <div class="row">
    <div class="col-sm-6">

</div>
 <div class="col-sm-6">

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

		<!--<th scope="col">Work Deparment</th>-->
		<th scope="col">Work Position</th>
		<th scope="col">Work Notes</th>
		<th scope="col">Department Name</th>

		<th scope="col">Email</th>
		<th scope="col">Mobile</th>
		<th scope="col">Work Phone</th>
		<th scope="col">Daily Count</th>
		<th scope="col">Last Activity</th>

    </tr>	
	</thead>
	<tbody>
	<?php foreach ($users as $row): array_map('htmlentities', $row);?>
	<tr>
	<td><?php echo $row['NAME'];?></td>
  <td><?php echo $row['LAST_NAME'];?></td>

	  <!--<td><?php echo $row['WORK_DEPARTMENT'];?></td>-->
		<td><?php echo $row['WORK_POSITION'];?></td>
	  <td><?php echo $row['WORK_NOTES'];?></td>
	  <td><?php echo $row['UF_DEPARTMENT_NAME'];?></td>

		<td><?php echo $row['EMAIL'];?></td>
		<td><?php echo $row['PERSONAL_MOBILE'];?></td>
		<td><?php echo $row['WORK_PHONE'];?></td>
		<td><?php echo $row['PERSONAL_ICQ'];?></td>
		<td><?php echo $row['LAST_ACTIVITY_DATE'];?></td>

<!--EMAIL,LAST_LOGIN,DATE_REGISTER,PERSONAL_MOBILE,WORK_PHONE,LAST_ACTIVITY_DATE-->

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

