<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

//SELECT MAX(b_crm_contact.DATE_CREATE) as Date,COUNT(b_crm_contact.ID) as Contacts_Count,ASSIGNED_BY_ID as UserID,b_user.NAME,b_user.LAST_NAME,b_user.WORK_DEPARTMENT,b_user.WORK_POSITION,b_user.WORK_NOTES,b_user_index.UF_DEPARTMENT_NAME FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID and b_uts_crm_contact.UF_CRM_1590674689393 = 'English' AND b_crm_contact.DATE_CREATE LIKE '2020-09-21%' INNER JOIN b_user ON b_user.ID = b_crm_contact.ASSIGNED_BY_ID INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) desc LIMIT 100;

$language = "English";
$users_group_len = DBGet("SELECT COUNT(ID),ASSIGNED_BY_ID FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID and b_uts_crm_contact.UF_CRM_1590674689393 = '".$language."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT 100;"); //Query to database
		//var_dump($users_group_len[0]["ASSIGNED_BY_ID"]); //User with less contacts

$languageES = "Spanish";
$users_group_lenES = DBGet("SELECT COUNT(ID),ASSIGNED_BY_ID FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID and b_uts_crm_contact.UF_CRM_1590674689393 = '".$languageES."' GROUP BY ASSIGNED_BY_ID ORDER BY COUNT(ID) LIMIT 100;"); //Query to database
		//var_dump($users_group_len[0]["ASSIGNED_BY_ID"]); //User with less contacts

$total = DBGet("SELECT COUNT(ID),TYPE_ID FROM b_crm_contact GROUP BY TYPE_ID ORDER BY TYPE_ID LIMIT 500;");
//var_dump($total);

//$users = DBGet("SELECT ID,NAME,LAST_NAME,WORK_DEPARTMENT,WORK_POSITION,b_uts_user.UF_DEPARTMENT FROM b_user INNER JOIN b_uts_user ON b_uts_user.VALUE_ID = b_user.ID;"); //with department ID
$users = DBGet("SELECT ID,b_user.NAME,b_user.LAST_NAME,b_user.WORK_DEPARTMENT,b_user.WORK_POSITION,b_user.WORK_NOTES,b_user_index.UF_DEPARTMENT_NAME FROM b_user INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID;"); //with departments names

//var_dump($users);

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
    <title>All Contacts Count by User!</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		
	<style>
  		h2 {color:blue;}
  		p {color:green;}
	</style>
</head>

<body>

	<center><h2>All Contacts Count by User!</h2></center>

	<table class="table table-striped table-bordered table-hover">
	<thead class="thead-dark">
	<tr>
	  <th scope="col">Contacts Count</th>
	  <th scope="col">User ID -> User Full Name</th>
	  <th scope="col">Department text->Work Position->Dep. Name</th>
	  <th scope="col">Language</th>

    </tr>	
	</thead>
	<tbody>
	<?php foreach ($users_group_len as $row): array_map('htmlentities', $row);?>
	<tr>
	  <th scope="row"><?php echo $row['COUNT(ID)']; //echo implode('</td><td>', $row);?></th>
	  <td><?php $username = find_user_info($row['ASSIGNED_BY_ID'],$users); echo $row['ASSIGNED_BY_ID'].'->'.$username;?></td>
	  <td><?php $userdep = find_user_dep($row['ASSIGNED_BY_ID'],$users); echo $userdep;?></td>
	  <td><?php echo $language;?></td>

    </tr>
	<?php endforeach; ?>

	<?php foreach ($users_group_lenES as $row): array_map('htmlentities', $row);?>
	<tr>
	  <th scope="row"><?php echo $row['COUNT(ID)']; //echo implode('</td><td>', $row);?></th>
	  <td><?php $username = find_user_info($row['ASSIGNED_BY_ID'],$users); echo $row['ASSIGNED_BY_ID'].'->'.$username;?></td>
	  <td><?php $userdep = find_user_dep($row['ASSIGNED_BY_ID'],$users); echo $userdep;?></td>
	  <td><?php echo $languageES;?></td>

    </tr>
	<?php endforeach; ?>

	<?php foreach ($total as $row): array_map('htmlentities', $row);?>
	<tr>
	  <th scope="row"><?php echo $row['COUNT(ID)']; //echo implode('</td><td>', $row);?></th>
	  <td><?php echo $row['TYPE_ID'];?></td>
	  <td><?php echo 'DEP';?></td>
	  <td><?php echo "ALL_Contacts";?></td>

    </tr>
	<?php endforeach; ?>

	</tbody>
</table>



	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>