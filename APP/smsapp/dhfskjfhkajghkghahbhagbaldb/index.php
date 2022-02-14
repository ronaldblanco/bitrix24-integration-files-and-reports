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
define('APP_ID', 'local.5ee28428664694.25575673'); // take it from Bitrix24 after adding a new application
define('APP_SECRET_CODE', 'Y8CQSdbqYd36s7sLJgUcY37It1JVob5YdoOp2NsJuQcgS7PXHH'); // take it from Bitrix24 after adding a new application
define('APP_REG_URL', 'https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/smsapp/dhfskjfhkajghkghahbhagbaldb/index.php'); // the same URL you should set when adding a new application in Bitrix24
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

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");

$from = date("Y-m-d",strtotime(date("Y-m-d") . ' -3 day'));
$to = date("Y-m-d");

if(isset($_GET['from'])) $from = $_GET['from'];
if(isset($_GET['to'])) $to = $_GET['to'];
if(isset($_GET['user'])) $user = $_GET['user'];

//var_dump($_GET);

function compareByTimeStamp($time1, $time2) 
{

	//$time11 = date('M/d/Y H:m:s', strtotime($time1["CREATED"]));
	//$time22 = date('M/d/Y H:m:s', strtotime($time2["CREATED"]));
	//if(strpos($time1["COMMENT"], ' SMS ') || strpos($time1["COMMENT"], ' MMS ')){
		$time11 =  strtotime($time1["CREATED"]);
	//}
	//if(strpos($time2["COMMENT"], ' SMS ') || strpos($time2["COMMENT"], ' MMS ')){
		$time22 =  strtotime($time2["CREATED"]);
	//}

	//var_dump($time11);
	//var_dump($time22);

    if ($time11 < $time22) return -1; 
    else if ($time11 > $time22) return 1; 
    else return 0; 
} 

function compareByTimeStampNew($time1, $time2) 
{

	$time11 = date('M/d/Y H:m:s', strtotime($time1["CREATED"]) - 60 * 60 * 8);
	$time22 = date('M/d/Y H:m:s', strtotime($time2["CREATED"]) - 60 * 60 * 8);

	var_dump($time11);
	var_dump($time22);
	var_dump($time11 - $time22);

    return $time11 - $time22;
} 

$arCurrentB24User = executeREST($arAccessParams['client_endpoint'], 'user.current', array(
),$arAccessParams['access_token']);

if(!isset($user)) $user = $arCurrentB24User["result"]["ID"];

if(isset($_GET['from']) && isset($_GET['to'])){
	$allContacts = DBGet("select b_crm_contact.ID,b_crm_contact.NAME,b_crm_contact.LAST_NAME,b_crm_contact.ASSIGNED_BY_ID,b_crm_dp_comm_mcd.VALUE as PHONE,CONVERT_TZ(MAX(b_crm_timeline.CREATED),'+00:00','-05:00') as lastupdate from b_crm_timeline inner join b_crm_timeline_bind on b_crm_timeline.ID = b_crm_timeline_bind.OWNER_ID inner join b_crm_contact on b_crm_contact.ID = b_crm_timeline_bind.ENTITY_ID inner join b_crm_dp_comm_mcd on b_crm_dp_comm_mcd.ENTITY_ID = b_crm_contact.ID where b_crm_dp_comm_mcd.TYPE = 'PHONE' and b_crm_dp_comm_mcd.ENTITY_TYPE_ID = 3 and b_crm_contact.ASSIGNED_BY_ID = ".$user." and (COMMENT like '% SMS %' or COMMENT like '% MMS %') and CONVERT_TZ(b_crm_timeline.CREATED,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' group by b_crm_contact.ID order by MAX(b_crm_timeline.CREATED) desc limit 70;");
	//$allContacts = DBGet("select contactinfo.ID,contactinfo.NAME,contactinfo.LAST_NAME,contactinfo.ASSIGNED_BY_ID,contactinfo.PHONE,contactinfo.lastupdate from (select b_crm_contact.ID,b_crm_contact.NAME,b_crm_contact.LAST_NAME,b_crm_contact.ASSIGNED_BY_ID,b_crm_dp_comm_mcd.VALUE as PHONE,CONVERT_TZ(MAX(b_crm_act.CREATED),'+00:00','-05:00') as lastupdate from b_crm_contact inner join b_crm_dp_comm_mcd on b_crm_dp_comm_mcd.ENTITY_ID = b_crm_contact.ID inner join b_crm_act on b_crm_act.OWNER_ID = b_crm_contact.ID and b_crm_act.PROVIDER_TYPE_ID = 'SMS' and b_crm_act.OWNER_TYPE_ID = 3 and CONVERT_TZ(b_crm_act.CREATED,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' and b_crm_dp_comm_mcd.TYPE = 'PHONE' and b_crm_dp_comm_mcd.ENTITY_TYPE_ID = 3 group by b_crm_contact.ID) as contactinfo where ASSIGNED_BY_ID = ".$user." LIMIT 70;");
	$contacts['result'] = $allContacts;
	$users = DBGet("select ID,NAME,LAST_NAME from b_user limit 250;");
	//var_dump($users);
} else {
	$contacts['result'] = array();
	$users = array();
}


function getusername($id,$users){
	$result = false;
	foreach($users as $user){
		if($user['ID'] == $id) $result = $user['NAME'] . " " . $user['LAST_NAME'];
	}
	if($result == "Admin Swaypc") $result = "System";
	return $result;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SMS Application</title>
	<link rel="stylesheet" href="css/timeline.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

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
	$timelines = array();
?>

<!--<h4>SMS/MMS Time Line Application:</h4>-->

<div>
	<!--<small>The application it is limited to 70 contacts only as maximun at the same time!</small>-->
	<p>User comunication with contacts between:</p>
 	<p>FROM &nbsp;<input type="text" name="from" id="from"  value="<?php echo $from?>">  TO   &nbsp; &nbsp;  &nbsp; &nbsp;<input type="text" name="to" id="to"  value="<?php echo $to?>"> <button type="button" id="date">Refresh!</button></p>
</div>

			<!--<form id="contact" action="" method="get" styles>
				<label>Select Contact: </label>
				<select id="mydata" name="mydata">
					<?php foreach ($allContacts as $row): array_map('htmlentities', $row); var_dump($row);?>
<option value="<?php echo $row['ID'];?>"><?php echo $row['ID'].":".$row['NAME']." ".$row['LAST_NAME'];?></option>
					<?php endforeach; ?>
				</select>
				<input type="submit" class="btn btn-primary" value="Get Info.">
			</form>-->

<script>
	/*var queryParams = new URLSearchParams(window.location.search);
	var baseurl = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/smsapp/dhfskjfhkajghkghahbhagbaldb/index.php";
	var url = baseurl + queryParams.toString();
	document.getElementById("contact").action = url;*/
</script>

<!--<table class="table">
	<tr>
	  <td>ID</td>
	  <td>NAME</td>
	  <td>LAST NAME</td>
	  <td>PHONE</td>
	  <td>EMAIL</td>
	  <td>RESPONSABLE</td>
    </tr>-->	
	<?php foreach ($contacts['result'] as $row): array_map('htmlentities', $row);
		
	$responsable = api (
    'user.get' ,
   		[
 	 	 'FILTER' => ['ID' => $row['ASSIGNED_BY_ID']],
	 	 'SELECT' => ['ID','NAME','LAST_NAME'], //UF_CRM_1594061489 -> coordinator, UF_CRM_1594061559 -> 3rd responsable
    	]);
		
	$timeline = api (
    'crm.timeline.comment.list' ,
   		[
 	 	 'FILTER' => ['ENTITY_ID' => $row['ID'], "ENTITY_TYPE" => "contact"],
			//'SELECT' => ['COMMENT','SMS','FILES','CREATION','ID'], //UF_CRM_1594061489 -> coordinator, UF_CRM_1594061559 -> 3rd responsable
    	]);	
		//var_dump($timeline['total']);
		if($timeline['total'] > 50){
			$start = explode(".",$timeline['total']/50)[0] * 50;
			$resturlm = $restonly."/crm.timeline.comment.list?FILTER[ENTITY_ID]=".$row['ID']."&FILTER[ENTITY_TYPE]=contact&start=".$start;
			$timeline11 = json_decode(file_get_contents($resturlm),true);
			$timeline = $timeline11;
		}
		//var_dump($timeline);
	$timelineactivity = api (
    'crm.activity.list' ,
   		[
 	 	 'FILTER' => ['OWNER_ID' => $row['ID'], "OWNER_TYPE_ID" => 3],
	 	 //'SELECT' => ['COMMENT','EMAIL','TASK','SMS','CALL','FILES','CREATION','ID'], //UF_CRM_1594061489 -> coordinator, UF_CRM_1594061559 -> 3rd responsable
    	]);
		if($timelineactivity['total'] > 50){
			$start = explode(".",$timelineactivity['total']/50)[0] * 50;
			$resturlm = $restonly."/crm.timeline.comment.list?FILTER[ENTITY_ID]=".$row['ID']."&FILTER[ENTITY_TYPE]=contact&start=".$start;
			$timelineactivity11 = json_decode(file_get_contents($resturlm),true);
			$timelineactivity = $timelineactivity11;
		}

		$timeline['result'] = array_merge($timeline['result'],$timelineactivity['result']);

		//var_dump($timeline['result'][0]);

		$temp = array();
		foreach($timeline['result'] as $rowline){
			if(strpos($rowline["COMMENT"], ' SMS ') || strpos($rowline["COMMENT"], ' MMS ') || strpos($rowline["SUBJECT"], ' SMS ')){
				array_push($temp,$rowline);
			}
		}
		$timeline['result'] = $temp;

		//echo "Before organization!";
		//var_dump($timeline['result']);
		//var_dump($temp[0]);

		//usort($timeline['result'], "compareByTimeStampNew");

		usort($timeline['result'], "compareByTimeStamp");

		//echo "After organization!";
		//var_dump($timeline['result']);

	array_push($timelines, array('contactid' => $row['ID'],'lastupdate' => $row['lastupdate'], 'contactname' => $row['NAME']. " " . $row['LAST_NAME'] ,'IDresponsable' => $row['ASSIGNED_BY_ID'],'contactphone' => $row['PHONE'], $timeline['result']));

		//var_dump($timeline);

	?>

	<!--<iframe scrolling="no" src="https://crm.domain.com/crm/contact/details/<?php echo $row['ID']?>/" style="border: 0px none; margin-left: 100px; height: 200px; margin-top: 100px; width: 200px;">
	</iframe>-->

	<!--<iframe src="https://crm.domain.com/crm/contact/details/<?php echo $row['ID']?>/#">
	</iframe>-->

    <!--<tr>
	  <td><a href="https://crm.domain.com/crm/contact/details/<?php echo $row['ID']?>/"><?php echo $row['ID']?></a></td>
      <td><?php echo $row['NAME']; //echo implode('</td><td>', $row);?></td>
	  <td><?php echo $row['LAST_NAME'];?></td>
	  <td><?php echo "<a href='callto:" . $row['PHONE'][0]['VALUE'] . "'>" . $row['PHONE'][0]['VALUE'] . "</a>";//"<a href='https://crm.domain.com/rest/1/23odl51wmxp7ea4p/telephony.externalcall.register/?TYPE=1&PHONE_NUMBER=" . $row['PHONE'][0]['VALUE'] . "&USER_ID=" . $arCurrentB24User["result"]['ID'] . "&USER_PHONE_INNER=" . $arCurrentB24User["result"]['UF_PHONE_INNER'] ."'>" . $row['PHONE'][0]['VALUE'] . "</a>";?></td>
	  <td><?php echo "<a href='mailto:" . $row['EMAIL'][0]['VALUE'] . "'>" . $row['EMAIL'][0]['VALUE'] . "</a>";?></td>
	  <td><?php echo $responsable['result'][0]['NAME'] . ' ' . $responsable['result'][0]['LAST_NAME'];?></td>
    </tr>-->
	<?php endforeach; ?>
<!--</table>-->

<div id="mainbody" style="display: flex;">

<ul class="nav flex-column nav-pills" id="myTab" role="tablist" aria-orientation="vertical" style="width: 500px;">
	<?php $active = false; 

	?>
	<?php foreach ($timelines as $row): array_map('htmlentities', $row);
		if($active == false){
			$class = "nav-link active";
			$arial = "true";
			$active = true;
		} else {
			$class = "nav-link";
			$arial = "false";
		} 
	?>
	
  <li class="nav-item">
    <a class="<?php echo $class;?>" id="<?php echo $row['contactid'];?>-tab" data-toggle="tab" href="#<?php echo $row['contactid'];?>" role="tab" aria-controls="<?php echo $row['contactid'];?>"
	  aria-selected="<?php echo $arial;?>"><?php echo $row['contactname']."; recent: ".date("m/d/Y h:m:s",strtotime($row['lastupdate']));?></a>
  </li>
	<?php endforeach; ?>
  
</ul>

<div class="tab-content" id="myTabContent" style="width: 100%;">
	
	<?php $active = false; ?>
	<?php foreach ($timelines as $row): array_map('htmlentities', $row);
		if($active == false){
			$class = "tab-pane fade show active";
			$active = true;
		} else $class = "tab-pane fade";
	?>
	
  <div class="<?php echo $class;?>" id="<?php echo $row['contactid'];?>" role="tabpanel" aria-labelledby="<?php echo $row['contactid'];?>-tab">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
					
					<?php //foreach ($timelines as $row): array_map('htmlentities', $row);

//$_GET['code'];
//$_GET['domain'];
//$_GET['member_id'];
//$_GET['scope'];
//$_GET['server_domain'];

					?>
					
				<!--<ul class="nav nav-tabs" id="myTab" role="tablist">	
					

					<li class="nav-item">
    <a class="nav-link active" id="SMS-tab" data-toggle="tab" href="#SMS" role="tab" aria-controls="SMS"
      aria-selected="false">SMS</a>
  </li>-->


					
					<!--</br>
					</br>-->
					
					
					<div class="tab-content" id="myTabContent" style="width: 100%">

				<div class="tab-pane fade show active" id="SMS" role="tabpanel" aria-labelledby="SMS-tab">
					SMS/MMS Time Line <a href="https://crm.domain.com/crm/contact/details/<?php echo $row['contactid'];?>/" target="_blank"><?php echo $row['contactid'].":".$row['contactname'];?></a><?php echo ", PHONE: " . $row['contactphone'];?>

					<form action="https://techcnet.com/SMS/1234/sendSMS.php" method="post" styles>
						<div class="form-group">
							<input type="hidden" class="form-control" name="message_to" value='<?php echo $row['contactphone'];?>'>
							<input type="hidden" class="form-control" name="bindings[0][OWNER_ID]" value='<?php echo $row['contactid'];?>'>
							<input type="hidden" class="form-control" name="auth[domain]" value='crm.domain.com'>
							<input type="hidden" class="form-control" name="auth[member_id]" value='959278128ab5e919e0ff8b9c66a553e3'>
							<input type="hidden" class="form-control" name="auth[application_token]" value='9041496a10dd6b2e99ef8581d3f8625c'>
							<input type="hidden" class="form-control" name="redirect" value='https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/smsapp/dhfskjfhkajghkghahbhagbaldb/appSMSRedirect.php?contactID=<?php echo $row['contactid'];?>&coordinatorID=<?php echo $row['IDresponsable'];?>&from=<?php echo $from;?>&to=<?php echo $to;?>&user=<?php echo $user;?>&code=<?php echo $_GET['code'];?>&domain=<?php echo $_GET['domain'];?>&member_id=<?php echo $_GET['menber_id'];?>&scope=<?php echo $_GET['scope'];?>&server_domain=<?php echo $_GET['server_domain'];?>'>
							<textarea class="form-control" name="message_body" placeholder="SMS text" value='' rows="2" style="width: 70%"></textarea>
						</div>
        				<input type="submit" class="btn btn-primary" value="Send SMS to <?php echo $row['contactname'];?>!">
    				</form></br>

				</div>

						
					</div>
					
				
				<!--</ul>-->	
					
			<!--<h6 class="card-title">SMS/MMS Time Line for: <?php echo $row['contactname'] . ", ID: " . $row['contactid'] . ", PHONE: " . $row['contactphone'];?></h6>-->
                    <div id="content" style="height: 400px; overflow-y: scroll;">
                        <ul class="timeline" id="timeline" style="max-width: 60%;">
							
							<?php 
								$reverse = array_reverse($row[0]);
								foreach ($reverse as $subrow): array_map('htmlentities', $subrow);

								if(strpos($subrow["COMMENT"], ' SMS ') || strpos($subrow["COMMENT"], ' MMS ') || strpos($subrow["SUBJECT"], ' SMS ')){
									$datadate = date('M/d/Y H:m:s', strtotime($subrow["CREATED"]) - (60 * 60 * 10));
									//$datadate = date('M/d/Y H:m:s', strtotime($subrow["CREATED"]));
									if(strpos($subrow["SUBJECT"], ' SMS ')) $datadate = date('M/d/Y H:m:s', strtotime($subrow["CREATED"]) - (60 * 60 * 10));
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
                    </div>


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
	//var_dump($timelines);	
}
?>
	</div>

	<script>
		var auth = "<?php echo $arAccessParams['access_token']; ?>";
		var refresh = "<?php echo $arAccessParams['refresh_token']; ?>";
		var baseUrl = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/smsapp/dhfskjfhkajghkghahbhagbaldb/index.php?";
		var user = "<?php echo $user; ?>";
		$('#date').click(function() {
			var queryParams = new URLSearchParams(window.location.search);
			//queryParams.set("date", this.value);
			queryParams.delete("from");
			queryParams.delete("to");
			if($('#from').val() <= $('#to').val()){
				queryParams.set("from", $('#from').val());
				queryParams.set("to", $('#to').val());
				queryParams.set("user", user);
				//queryParams.set("auth", auth);
				//queryParams.set("code", refresh);

				//queryParams.delete("code");
				/*queryParams.delete("state");
				queryParams.delete("domain");
				queryParams.delete("member_id");
				queryParams.delete("scope");
				queryParams.delete("server_domain");*/

				//queryParams.set("code", auth);
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
