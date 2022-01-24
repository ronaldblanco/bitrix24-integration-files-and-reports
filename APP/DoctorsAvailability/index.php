<?php

date_default_timezone_set('America/New_York');

function redirect($url)
{
    Header("Location: ".$url);
    die();
}

$step = 1; //default 1

if (isset($_REQUEST['config'])) $step = 0;
if (isset($_REQUEST['portal'])) $step = 1;
if (isset($_REQUEST['code'])) $step = 2;
if (isset($_REQUEST['auth'])) $step = 3;

if (isset($_GET['mydata'])){
	$contactid = $_GET['mydata'];
}

/*Vars***********************************/
define('APP_ID', 'local.5fe35583abc374.49970048'); // take it from Bitrix24 after adding a new application
define('APP_SECRET_CODE', 'KTWIet0XR4YMsnCPWgTYo6b4u8TcrTD0QV74riYuSDH48AW8pE'); // take it from Bitrix24 after adding a new application
define('APP_REG_URL', 'https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/DoctorsAvailability/index.php'); // the same URL you should set when adding a new application in Bitrix24
$domain = 'crm.domain.com';
$server_domain = $domain;
$savetime = 20; //seconds to be sure that access_token it is valid
/*End Vars*******************************/

$btokenRefreshed = null;

$arScope = array('user');

switch ($step) {
    case 1:
        // we need to get the first authorization code from Bitrix24 where our application is _already_ installed
		requestCode($domain, $_GET['from'], $_GET['to']);

        break;

    case 2:
		$arAccessParams = requestAccessToken($_REQUEST['code'], $_REQUEST['server_domain'], $_GET['mydata'], $_GET['mydata1']);
		//var_dump($arAccessParams);
	 break;

	case 3:
		$arAccessParams['access_token'] = $_REQUEST['auth'];
	break;

    default:
        break;
}

$arCurrentB24User = executeREST($arAccessParams['client_endpoint'], 'user.current', array(
),$arAccessParams['access_token']);

//***************************************************************************
/*Execute Rest APIS
		**
		**
		*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$currentUser = $arCurrentB24User['result'];
$userId = $currentUser['ID'];
$user = $currentUser['ID'];

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

$from = date("Y-m-d");
//$to = date("Y-m-d");
$to = date("Y-m-d",strtotime(date("Y-m-d") . ' +30 day'));

if(isset($_GET['from'])) $from = $_GET['from'];
if(isset($_GET['to'])) $to = $_GET['to'];
if(isset($_GET['user'])) $userId = $_GET['user'];

//var_dump($_GET);

function compareByTimeStamp($time1, $time2) 
{

	$time11 = strtotime($time1["taskdate"]);
	$time22 = strtotime($time2["taskdate"]);
	
    if ($time11 < $time22) 
        return -1; 
    else if ($time11 > $time22)
        return 1; 
    else
        return 0; 
} 

if(!isset($userId)) $userId = $arCurrentB24User["result"]["ID"];

//if(isset($_GET['from']) && isset($_GET['to'])){
//if(true){

	$user_data = DBGet("SELECT b_user.NAME,b_user.LAST_NAME,b_user.WORK_DEPARTMENT,b_user.WORK_POSITION,b_user.WORK_NOTES,b_user_index.UF_DEPARTMENT_NAME FROM b_user INNER JOIN b_user_index ON b_user_index.USER_ID = b_user.ID WHERE b_user.ID = '".$userId."' ORDER BY b_user.NAME LIMIT 100;");
	//var_dump($user_data);
	$department = $user_data[0]["UF_DEPARTMENT_NAME"];
	$position = $user_data[0]["WORK_POSITION"];
	$notes = $user_data[0]["WORK_NOTES"];
	$maxrows = "250";

//$doctors = " and RESPONSIBLE_ID = '183' and RESPONSIBLE_ID = '184' and and RESPONSIBLE_ID = '185' and and RESPONSIBLE_ID = '186' ";
//$query = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and CONVERT_TZ(START_DATE_PLAN,'+00:00','-08:00') between '".$from." 00:00:00' and '".$to." 23:59:59') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";
$querywilliam = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '183' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";
$querysalas = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '184' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";
$querybrewster = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '185' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";
$querydigeronimo = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '186' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";

$query = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and CONVERT_TZ(START_DATE_PLAN,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '".$notes."' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";

/*$querywilliam = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,START_DATE_PLAN as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and START_DATE_PLAN between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '183' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";
$querysalas = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,START_DATE_PLAN as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and START_DATE_PLAN between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '184' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";
$querybrewster = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,START_DATE_PLAN as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and START_DATE_PLAN between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '185' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";
$querydigeronimo = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,START_DATE_PLAN as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and START_DATE_PLAN between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '186' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";

$query = "select count(RESPONSIBLE_ID) as tasks,RESPONSIBLE_ID,SUBSTR(taskdate, 1, 10) as taskdate,NAME,LAST_NAME,WORK_NOTES from (select RESPONSIBLE_ID,START_DATE_PLAN as taskdate,NAME,LAST_NAME,WORK_NOTES from b_tasks inner join b_user on b_user.ID = b_tasks.RESPONSIBLE_ID where WORK_POSITION = 'Surgeon' and START_DATE_PLAN between '".$from." 00:00:00' and '".$to." 23:59:59' and RESPONSIBLE_ID = '".$notes."' and ZOMBIE = 'N') as tasks group by SUBSTR(taskdate, 1, 10) order by RESPONSIBLE_ID limit ".$maxrows.";";
*/
if($position == "Main Manager" || ($position == "Manager" && $department == "Coordination")){

	$doctors_availability_william = DBGet($querywilliam);
	$doctors_availability_salas = DBGet($querysalas);
	$doctors_availability_brewster = DBGet($querybrewster);
	$doctors_availability_digeronimo = DBGet($querydigeronimo);

	$doctors_availability1 = array_merge($doctors_availability_william, $doctors_availability_salas);
	$doctors_availability2 = array_merge($doctors_availability_brewster,$doctors_availability_digeronimo);
	$doctors_availability = array_merge($doctors_availability1,$doctors_availability2);

} else if($position == "Coordinacion" && $department == "Coordination"){

	if(isset($notes) && $notes != "") $doctors_availability = DBGet($query);
	else $doctors_availability = array();

} else $doctors_availability = array();

/*var_dump($doctors_availability_william);
var_dump($doctors_availability_salas);
var_dump($doctors_availability_brewster);
var_dump($doctors_availability_digeronimo);*/
//var_dump($doctors_availability);

$buildarray = array();
$doctor = 0;
$doctorname = "";
$capacity = "";
$doctorinfo = array();

//taskdate

foreach($doctors_availability as $doctorday){

	if($doctor == 0){
		$doctor = $doctorday['RESPONSIBLE_ID']; //first itteration
		$doctorname = $doctorday['NAME']." ".$doctorday['LAST_NAME']; //first itteration
		$capacity = $doctorday['WORK_NOTES'];
	} 
	if($doctor == $doctorday['RESPONSIBLE_ID']) array_push($doctorinfo,$doctorday);
	else {

		usort($doctorinfo,"compareByTimeStamp");

		array_push($buildarray,array('ID' => $doctor, 'fullname' => $doctorname, 'daily_capacity' => $capacity, 'information' => $doctorinfo));
		$doctor = $doctorday['RESPONSIBLE_ID'];
		$doctorname = $doctorday['NAME']." ".$doctorday['LAST_NAME'];
		$capacity = $doctorday['WORK_NOTES'];
		$doctorinfo = array();
		array_push($doctorinfo,$doctorday);

	}

}

usort($doctorinfo,"compareByTimeStamp");

array_push($buildarray,array('ID' => $doctor, 'fullname' => $doctorname, 'daily_capacity' => $capacity, 'information' => $doctorinfo));

//$users = DBGet("select ID,NAME,LAST_NAME from b_user limit 250;");

	$contacts['result'] = $buildarray;

	//} else {
	//$contacts['result'] = array();
	//$users = array();
	//}

//var_dump($contacts['result']);

function getusername($id){
	$result = false;
	foreach($GLOBALS["users"] as $user){
		if($user['ID'] == $id) $result = $user['NAME'] . " " . $user['LAST_NAME'];
	}
	//if($result == "Admin Swaypc") $result = "System";
	return $result;
}

function arrayfinder_id($newarray,$id){
	foreach($newarray as $row){
		if($row["ASSIGNED_BY_ID"] == $id){
			return $row;
		}
	}
	return false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Efficiency Report!</title>
	<!--<link rel="stylesheet" href="css/timeline.css">-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">

	<style>
    body, html {
      font-family: arial, sans-serif;
      font-size: 11pt;
    }

    div.vis-editable,
    div.vis-editable.vis-selected {
      /* custom styling for editable items... */
	  background-color: #008000;
      border-color: green;
      color: white;
    }

    div.vis-readonly,
    div.vis-readonly.vis-selected {
      /* custom styling for readonly items... */
      background-color: #ff4500;
      border-color: red;
      color: white;
    }
  </style>
<link rel="stylesheet" href="https://visjs.github.io/vis-timeline/styles/vis-timeline-graph2d.min.css" type="text/css">
<script src="https://visjs.github.io/vis-timeline/standalone/umd/vis-timeline-graph2d.min.js" ></script>

	<style type="text/css">/* Chart.js */
		@keyframes chartjs-render-animation{from{opacity:.99}to{opacity:1}}.chartjs-render-monitor{animation:chartjs-render-animation 1ms}.chartjs-size-monitor,.chartjs-size-monitor-expand,.chartjs-size-monitor-shrink{position:absolute;direction:ltr;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1}.chartjs-size-monitor-expand>div{position:absolute;width:1000000px;height:1000000px;left:0;top:0}.chartjs-size-monitor-shrink>div{position:absolute;width:200%;height:200%;left:0;top:0}
	</style>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

	<script src="https://www.chartjs.org/dist/2.9.4/Chart.min.js" ></script>
	<script src="https://www.chartjs.org/samples/latest/utils.js" ></script>

	<style>
  		h2 {color:blue;}
  		p {color:green;}
	</style>
</head>
<body>
<?php
	if ($step == 0) {
?>
	<div class="container-fluid">
	<div class="alert alert-primary" role="alert">
		<h2>It is posible your aplication it is not configure to work whit Bitrix24 yet:</h2>
	</div>
    <form action="config.php" method="post" styles>
		<div class="form-group">
			<label>APP_ID:</label>
        	<input type="text" class="form-control" name="app_id" placeholder="APP_ID" value='<?php echo $config['app_id']; ?>'>
			<small id="ID" class="form-text text-muted">ID de la aplicacion en Bitrix24.</small>
		</div>
		<div class="form-group">
			<label>APP_SECRET:</label><br>
			<input type="text" class="form-control" name="app_secret" placeholder="APP_SECRET" value='<?php echo $config['app_secret']; ?>'>
			<small id="SECRET" class="form-text text-muted">Secret de la aplicacion en Bitrix24.</small>
		</div>
		<div class="form-group">
			<label>APP_REDIRECT_URL:</label><br>
			<input type="text" class="form-control" name="app_redirect_url" placeholder="APP_REDIRECT_URL" value='<?php echo $config['app_redirect_url']; ?>'>
			<small id="URL" class="form-text text-muted">URL de redireccion en Bitrix24.</small>
		</div>
		<div class="form-group">
			<label>BITRIX_DOMAIN.COM:</label><br>
			<input type="text" class="form-control" name="bitrix_domain" placeholder="BITRIX_DOMAIN.COM" value='<?php echo $config['bitrix_domain']; ?>'>
			<small id="DOMAIN" class="form-text text-muted">Domain of Bitrix24 server.</small>
		</div>
        <input type="submit" class="btn btn-primary" value="Submit">
    </form>
	</div>
<?php
} elseif ($step == 1) {
	echo '<div class="alert alert-primary" role="alert">';
	echo 'step 1 (redirecting to Bitrix24):<br/>';
	echo '</div>';
} elseif ($step == 2){
		//echo '<div class="alert alert-primary" role="alert">';
		//echo "Logged User: " . $arCurrentB24User["result"]["NAME"] . " " . $arCurrentB24User["result"]["LAST_NAME"] . ' <br/>';
		//echo '</div>';
		//echo '<div class="alert alert-success" role="alert">';
		//echo 'Bellow you will find your contacts for coordination:<br/>';
	//print_r($contacts['result']);
		//echo '</div>';
	//var_dump($arCurrentB24User["result"]);
		//$timelines = array();
?>

<!--<h4>Efficiency Reports Application:</h4>-->

<div>
	</br>
	<p>FROM &nbsp;<input type="text" name="from" id="from"  value="<?php echo $from?>">  TO   &nbsp; &nbsp;  &nbsp; &nbsp;<input type="text" name="to" id="to"  value="<?php echo $to?>"> <button type="button" id="date">Update Data!</button></p>
</div>

<div id="mainbody" style="display: flex;overflow: hidden;">

<ul class="nav flex-column nav-pills" id="myTab" role="tablist" aria-orientation="vertical" style="width: 300px;height: 500px;overflow-x: auto;flex-direction: row !important;">
	<?php $active = false; ?>
	<?php foreach ($contacts['result'] as $row): array_map('htmlentities', $row);
		if($active == false){
			$class = "nav-link active";
			$arial = "true";
			$active = true;
		} else {
			$class = "nav-link";
			$arial = "false";
		} 
	?>

	<?php
		/*$incourse = $row['total'] - $row['nocandidate'] - $row['closed'] - $row['prospects'] - $row['invalidcontact'] - $row['donotcontactme'];
		if($incourse == 0) $closeporcentage = 0;
		else $closeporcentage = round($row['closed'] * (100/$incourse), 2); //incourse % of close leads
		if($row['total'] == 0) $totalcloseporcentage = 0;
		else $totalcloseporcentage = round($row['closed'] * (100/$row['total']), 2);*/ //total % of close leads
	?>
	
  <li class="nav-item" style="width: 100%;">
    <a class="<?php echo $class;?>" id="<?php echo $row['ID'];?>-tab" data-toggle="tab" href="#<?php echo $row['ID'];?>" role="tab" aria-controls="<?php echo $row['ID'];?>"
      aria-selected="<?php echo $arial;?>"><?php echo $row['fullname'];?></a>
	<!--<span class="badge badge-primary badge-pill"><?php echo $totalcloseporcentage;?>%</span>
	<span class="badge badge-primary badge-pill"><?php echo $closeporcentage;?>%</span>-->
  </li>
	<?php endforeach; ?>
  
</ul>

<div class="tab-content" id="myTabContent" style="width: 100%;">
	
	<?php $active = false; ?>
	<?php foreach ($contacts['result'] as $row): array_map('htmlentities', $row);
		if($active == false){
			$class = "tab-pane fade show active";
			$active = true;
		} else $class = "tab-pane fade";
	?>
	
  <div class="<?php echo $class;?>" id="<?php echo $row['ID'];?>" role="tabpanel" aria-labelledby="<?php echo $row['ID'];?>-tab">

<div class="">
    <div class="row" style="height: auto;">
        <div class="col-md-12" style="max-width: 95% !important;margin-left: 15px;">
            <div class="card">
                <div class="card-body" style="height: 900px;">
					
					<?php //foreach ($timelines as $row): array_map('htmlentities', $row);

					?>
					
				<!--<ul class="nav nav-tabs" id="myTab" role="tablist">	
					

					<li class="nav-item">
    <a class="nav-link active" id="SMS-tab" data-toggle="tab" href="#SMS" role="tab" aria-controls="SMS"
      aria-selected="false">SMS</a>
  </li>-->

					<div class="tab-content" id="myTabContent" style="width: 100%">

				<div class="tab-pane fade show active" id="SMS" role="tabpanel" aria-labelledby="SMS-tab">
					<!--<a href="https://crm.domain.com/company/personal/user/<?php echo $row['ID'];?>/" target="_blank"><?php echo $row['ID'].":".$row['fullname'];?></a>-->

					<!--<form action="https://techcnet.com/SMS/qwteuuo856fg/sendSMS.php" method="post" styles>
						<div class="form-group">
							<label>SMS:</label>
							<input type="hidden" class="form-control" name="message_to" value='<?php echo $row['contactphone'];?>'>
							<input type="hidden" class="form-control" name="bindings[0][OWNER_ID]" value='<?php echo $row['IDresponsable'];?>'>
							<input type="hidden" class="form-control" name="auth[domain]" value='crm.domain.com'>
							<input type="hidden" class="form-control" name="auth[member_id]" value='959278128ab5e919e0ff8b9c66a553e3'>
							<input type="hidden" class="form-control" name="auth[application_token]" value='9041496a10dd6b2e99ef8581d3f8625c'>
							<input type="hidden" class="form-control" name="redirect" value='https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/smsapp/dhfskjfhkajghkghahbhagbaldb/appSMSRedirect.php?contactID=<?php echo $row['contactid'];?>&coordinatorID=<?php echo $row['IDresponsable'];?>'>
							<textarea class="form-control" name="message_body" placeholder="SMS text" value='' rows="3" style="width: 100%"></textarea>
							<small id="SMS" class="form-text text-muted">SMS Text to send!</small>
						</div>
        				<input type="submit" class="btn btn-primary" value="Send">
    				</form></br>-->

				</div>

						
					</div>
					

				<!--</ul>-->	

<?php
		/*$incourse = $row['total'] - $row['nocandidate'] - $row['closed'] - $row['prospects'] - $row['invalidcontact'] - $row['donotcontactme'];
		if($incourse == 0) $closeporcentage = 0;
		else $closeporcentage = round($row['closed'] * (100/$incourse), 2);
		if($row['total'] == 0) $totalcloseporcentage = 0;
		else $totalcloseporcentage = round($row['closed'] * (100/$row['total']), 2);*/ //total % of close leads
	?>

					<h6 class="card-title">Information for: <a href="https://crm.domain.com/company/personal/user/<?php echo $row['ID'];?>/" target="_blank"><?php echo $row['ID'].":".$row['fullname'];?></a> daily capacity of <?php echo $row['daily_capacity'];?>!</h6>

					<?php

		/*if($closeporcentage < 20 && $row['prospects'] > 1){
							echo "<div class='alert alert-danger' role='alert'>";
							echo $row['fullname']." Close Efficiency it is bellow 20% and have pending prospects!";
							echo "</div>";
						}
						if($closeporcentage < 20 && $row['prospects'] == 1){
							echo "<div class='alert alert-danger' role='alert'>";
							echo $row['fullname']." Close Efficiency it is bellow 20%!";
							echo "</div>";
						}
						if($closeporcentage >= 20 && $row['prospects'] > 1){
							echo "<div class='alert alert-warnning' role='alert'>";
							echo $row['fullname']." have pending prospects!";
							echo "</div>";
}*/
						/*if($closeporcentage >= 20 && $row['prospects'] == 1){
							echo "<div class='alert alert-success' role='alert'>";
							echo $row['fullname']." have Good Close Efficiency!";
							echo "</div>";
						}*/
					?>

					<!--<small>Total Close %:</small>
					<div class="progress">
  						<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $totalcloseporcentage;?>%"><?php echo $totalcloseporcentage;?>%</div>
					</div>
					<small>Close Efficiency %:</small>
					<div class="progress">
						<div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $closeporcentage;?>%"><?php echo $closeporcentage;?>%</div>
					</div>-->

					<!--<div class="alert alert-info" role="alert">
  						User total count of contacts for this periot of time it is <?php echo $row['total'];?>!
					</div>-->

					<div id="canvas-holder" style="width:650px"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
						<canvas id="<?php echo "chart-area".$row['ID'];?>" style="display: block; height: 304px; width: 608px;" width="760" height="380" class="chartjs-render-monitor"></canvas>
					</div>

					<div id="visualization_<?php echo $row['ID'];?>"></div>

					<!--<button id="randomizeData">Randomize Data</button>
					<button id="addDataset">Add Dataset</button>
					<button id="removeDataset">Remove Dataset</button>-->

	<?php
		$dataall = array();
		$data['full'] = array();
		$data['over'] = array();
		$data['capacity'] = array();
		$labels = array();
		$color = array();
		foreach($row['information'] as $day){
			array_push($dataall,$day['tasks']);
			array_push($labels,$day['taskdate']);
			if($day['tasks'] > $day['WORK_NOTES']){
				array_push($data['over'],$day['tasks']);
				array_push($data['full'],0);
				array_push($data['capacity'],0);
			} 
			else if($day['tasks'] == $day['WORK_NOTES']){
				array_push($data['full'],$day['tasks']);
				array_push($data['over'],0);
				array_push($data['capacity'],0);
			}
			else {
				array_push($data['capacity'],$day['tasks']);
				array_push($data['full'],0);
				array_push($data['over'],0);
			} 
		}

		//var_dump($data);
		//var_dump($labels);
		//var_dump($color);

	?>

					<script>
						//var data = new Array();
		var dataall = "<?php echo implode(', ', $dataall); ?>";
		var datafull = "<?php echo implode(', ', $data['full']); ?>";
		var dataover = "<?php echo implode(', ', $data['over']); ?>";
		var datacapacity = "<?php echo implode(', ', $data['capacity']); ?>";
		var labels = "<?php echo implode(', ', $labels); ?>";
		var color = "<?php echo implode(', ', $color); ?>";

var dataall = dataall.split(", ");
var datafull = datafull.split(", ");
var dataover = dataover.split(", ");
var datacapacity = datacapacity.split(", ");
var labels = labels.split(", ");
var color = color.split(", ");
//var charid = "<?php echo 'chart-area'.$row['ID'];?>";
						//console.log("Chart!!!!!!!");
if(dataover != [""]){
	var datasetover = {
				type: 'bar',
				label: 'Over Full',
				backgroundColor: window.chartColors.red,
				data: dataover,
				borderColor: 'white',
				borderWidth: 2
			}
		} else datasetover = {};

if(datafull != [""]){
	var datasetfull = {
				type: 'bar',
				label: 'Full',
				backgroundColor: window.chartColors.yellow,
				data: datafull,
				borderColor: 'white',
				borderWidth: 2
			}
		} else datasetfull = {};

if(datacapacity != [""]){
	var datasetcapacity = {
				type: 'bar',
				label: 'Capacity',
				backgroundColor: window.chartColors.green,
				data: datacapacity,
				borderColor: 'white',
				borderWidth: 2
			}
		} else datasetcapacity = {};

						/*console.log(datafull);
console.log(dataover);
console.log(datacapacity);
console.log(labels);
console.log(color);*/

var charid = "<?php echo 'chart-area'.$row['ID'];?>";

		var scalingFactor = function(value) {
			return Math.round(value);
		};

	var randomScalingFactor = function() {
			return Math.round(14);
		};

						/*var config = {
			type: 'bar',
			label: 'Doctor Capacity',
			backgroundColor: window.chartColors.blue,
			borderColor: 'white',
			borderWidth:2,
			data: {
				datasets: [{
					data: [5,10,14],
					backgroundColor: [window.chartColors.yellow,window.chartColors.yellow,window.chartColors.yellow],
					label: username
				}],
				labels: ['test','test1','test2']
			},
			options: {
				responsive: true
			}
		};

						//window.onload = function() {
			var ctx = document.getElementById(chart).getContext('2d');
window.myPie = new Chart(ctx, config);*/
						//};



		var chartData = {
			labels: labels,
			options: {
				responsive: true
			},
			datasets: [{
				type: 'line',
				label: 'Line Guide',
				borderColor: window.chartColors.blue,
				borderWidth: 2,
				fill: true,
				data: dataall
			},  datasetover,
			 	datasetfull,
			 	datasetcapacity
			]

		};
						//window.onload = function() {
			var ctx = document.getElementById(charid).getContext('2d');
			window.myMixedChart = new Chart(ctx, {
				type: 'bar',
				data: chartData,
				options: {
					responsive: true,
					title: {
						display: true,
						text: 'Doctor Capacity!'
					},
					tooltips: {
						mode: 'index',
						intersect: true
					}
				}
			});
			//};



					</script>



<script>

var maximun = parseInt("<?php echo $row['information'][0]['WORK_NOTES'];?>");
var doctorname = "<?php echo $row['fullname'];?>";
var dataall = "<?php echo implode(', ', $dataall); ?>";
var dataall = dataall.split(", ");
console.log(dataall);

var labels = "<?php echo implode(', ', $labels); ?>";
var labels = labels.split(", ");

var data = [];
var mycount = 1;
dataall.forEach(myFunction);
function myFunction(item, index) {
	console.log();
	if(maximun > parseInt(item)) myeditable = true;
	else myeditable = false;
	data.push({id: mycount, content: item, editable: myeditable, start: labels[index], group: 1});
	mycount = mycount + 1;
}
	//console.log(data);
  // create a DataSet with items
	var items = new vis.DataSet(data/*[
    {id: 1, content: 'Editable', editable: true, start: '2010-08-23', group: 1},
    {id: 2, content: 'Editable', editable: true, start: '2010-08-23T23:00:00', group: 2},
    {id: 3, content: 'Read-only', editable: false, start: '2010-08-24T16:00:00', group: 1},
    {id: 4, content: 'Read-only', editable: false, start: '2010-08-26', end: '2010-09-02', group: 2},
    {id: 5, content: 'Editable', editable: true, start: '2010-08-28', group: 1},
    {id: 6, content: 'Read-only', editable: false, start: '2010-08-29', group: 2},
    {id: 7, content: 'Editable', editable: true, start: '2010-08-31', end: '2010-09-03', group: 1},
    {id: 8, content: 'Read-only', editable: false, start: '2010-09-04T12:00:00', group: 2},
    {id: 9, content: 'Default', start: '2010-09-04', group: 1},
    {id: 10, content: 'Default', start: '2010-08-24', group: 2}
]*/);

  var groups = [
    {
      id: 1,
      content: doctorname
    }
  ]

var visualizationid = "visualization_<?php echo $row['ID'];?>";

  var container = document.getElementById(visualizationid);
  var options = {
    editable: {
      add: true,
      remove: true,
      updateGroup: false,
      updateTime: true,
      overrideItems: false
    }  // default for all items
  };

  var timeline = new vis.Timeline(container, items, groups, options);

	/*var updateEditOptions = function(e){
    var changedOption = e.target.name;
    var options = { editable: { } };
    options.editable[changedOption] = e.target.checked;
    timeline.setOptions(options);
  };

  var cbs = document.getElementsByTagName("input");
  [].forEach.call(cbs, function(cb){
    cb.onchange = updateEditOptions;
});*/

</script>


                    <!--<div id="content" style="height: 400px; overflow-y: scroll;">
                        <ul class="timeline" id="timeline" style="max-width: 60%;">
							
							<?php 
								$reverse = array_reverse($row[0]);
								foreach ($reverse as $subrow): array_map('htmlentities', $subrow);

								if(strpos($subrow["COMMENT"], ' SMS ') || strpos($subrow["COMMENT"], ' MMS ') || strpos($subrow["SUBJECT"], ' SMS ')){
									$datadate = date('M/d/Y H:m:s', strtotime($subrow["CREATED"]) - 60 * 60 * 8);
									if(strpos($subrow["SUBJECT"], ' SMS ')) $datadate = date('M/d/Y H:m:s', strtotime($subrow["CREATED"]));
							?>
							
                            <li class="event" data-date="<?php echo $datadate;?>">
                                <!--<h3>ID:<?php echo $subrow["ID"];?></h3>-->
								<h3><?php echo "Done by ".getusername($subrow["AUTHOR_ID"],$users);?></h3>
								<p><?php echo $subrow["SUBJECT"];?></p>
								<p><?php echo $subrow["DESCRIPTION"];?></p>
                                <p><?php echo $subrow["COMMENT"];?></p>

                            </li>
                          
						<?php 
							}
							endforeach; 
						?>
						
						</ul>
                    </div>-->

					<?php //endforeach; ?>
				
				</div>
            </div>
        </div>
    </div>
</div>

	</div>
	
	<?php endforeach; ?>
 
</div>	
	
<?php
		//var_dump($userId);	
}
?>
	</div>

	<script>
		var auth = "<?php echo $arAccessParams['access_token']; ?>";
		var refresh = "<?php echo $arAccessParams['refresh_token']; ?>";
		var baseUrl = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/DoctorsAvailability/index.php?";
		var user = "<?php echo $userId;?>";

		//console.log(user);

		$('#date').click(function() {
			var queryParams = new URLSearchParams(window.location.search);
			//queryParams.set("date", this.value);
			queryParams.delete("from");
			queryParams.delete("to");
			//queryParams.delete("user");
			if($('#from').val() <= $('#to').val() && user != "Array"){
				queryParams.set("from", $('#from').val());
				queryParams.set("to", $('#to').val());
				queryParams.set("user", user);

				var url = baseUrl + queryParams.toString();
				window.location = url;
			} else if($('#from').val() <= $('#to').val() && user == "Array"){
				queryParams.set("from", $('#from').val());
				queryParams.set("to", $('#to').val());
				//queryParams.set("user", user);

				var url = baseUrl + queryParams.toString();
				window.location = url;
			} else alert( "From date must be less than to date!" );

		});

		$( function() {
    		$( "#from" ).datepicker({ dateFormat: 'yy-mm-dd' });

  		} );

 		$( function() {
    		$( "#to" ).datepicker({ dateFormat: 'yy-mm-dd' });

  		} );
	</script>

	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->

	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" ></script>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" ></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js" ></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" ></script>

	<script src="https://www.chartjs.org/samples/latest/utils.js" ></script>

	<script>

		/*document.getElementById('randomizeData').addEventListener('click', function() {
			config.data.datasets.forEach(function(dataset) {
				dataset.data = dataset.data.map(function() {
					return randomScalingFactor();
				});
			});

			window.myPie.update();
		});

		var colorNames = Object.keys(window.chartColors);
		document.getElementById('addDataset').addEventListener('click', function() {
			var newDataset = {
				backgroundColor: [],
				data: [],
				label: 'New dataset ' + config.data.datasets.length,
			};

			for (var index = 0; index < config.data.labels.length; ++index) {
				newDataset.data.push(randomScalingFactor());

				var colorName = colorNames[index % colorNames.length];
				var newColor = window.chartColors[colorName];
				newDataset.backgroundColor.push(newColor);
			}

			config.data.datasets.push(newDataset);
			window.myPie.update();
		});

		document.getElementById('removeDataset').addEventListener('click', function() {
			config.data.datasets.splice(0, 1);
			window.myPie.update();
	});*/
	</script>

</body>
</html>

<?php

//***************************************************************************

function executeHTTPRequest ($queryUrl, array $params = array()) {
    $result = array();
    $queryData = http_build_query($params);

	if($params != array()){
		//var_dump($queryUrl.$queryData);
		$result = json_decode(file_get_contents($queryUrl.$queryData),true);
		
	} 
	else{
		//var_dump($queryUrl);
		$result = json_decode(file_get_contents($queryUrl),true);
		//string(249) "https://oauth.bitrix.info/oauth/token/?grant_type=authorization_code&client_id=local.5f50feb3287d4397490808&client_secret=meRlN1mAM1WdSMfoUQlTu2QnD8M4sAOnRQldszqlkDz89qSWsr&code=8e12515f004c6f990047c39300000016909d033b31d7a0c3a9f47bf7af3004ce550221" array(11) { ["access_token"]=> string(70) "8120515f004c6f990047c39300000016909d03b42034e2621671bac2c59eb6236c88a3" ["expires"]=> int1599152257) ["expires_in"]=> int(3600) ["scope"]=> string(3) "app" ["domain"]=> string(17) "oauth.bitrix.info" ["server_endpoint"]=> string(31) "https://oauth.bitrix.info/rest/" ["status"]=> string(1) "L" ["client_endpoint"]=> string(39) "https://crm.domain.com/rest/" ["member_id"]=> string(32) "959278128ab5e919e0ff8b9c66a553e3" ["user_id"]=> int(22) ["refresh_token"]=> string(70) "719f785f004c6f990047c39300000016909d039833c14e03ee6ef6ca8ca5f6e4b1fa78" } string(131) "https://crm.domain.com/rest/user.current.jsonauth=8120515f004c6f990047c39300000016909d03b42034e2621671bac2c59eb6236c88a3"
		 
	} 
	//var_dump($result);
	return $result;
}


function requestCode ($domain, $add, $add1) {
    $url = 'https://' . $domain . '/oauth/authorize/' .
        '?client_id=' . urlencode(APP_ID).'&mydata='.$add.'&mydata1='.$add1;
    redirect($url);
}

function requestAccessToken ($code, $server_domain, $add, $add1) {
    $url = 'https://' . $server_domain . '/oauth/token/?' .
        'grant_type=authorization_code'.
        '&client_id='.urlencode(APP_ID).
        '&client_secret='.urlencode(APP_SECRET_CODE).
        '&code='.urlencode($code).'&mydata='.$add.'&mydata1='.$add1;
    return executeHTTPRequest($url);
}

function executeREST ($rest_url, $method, $params, $access_token) {
	//echo 'executeRest!';
    $url = $rest_url.$method.'.json?';
    return executeHTTPRequest($url, array_merge($params, array("auth" => $access_token)));
}

?>