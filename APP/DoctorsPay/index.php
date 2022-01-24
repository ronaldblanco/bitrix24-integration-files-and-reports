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
define('APP_ID', 'local.5fea3981478075.36512227'); // take it from Bitrix24 after adding a new application
define('APP_SECRET_CODE', '2Adq2yJUs2EzO13XyTieclaPDcHH4sDia29iTuA9Bkzg0tJq7C'); // take it from Bitrix24 after adding a new application
define('APP_REG_URL', 'https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/DoctorsPay/index.php'); // the same URL you should set when adding a new application in Bitrix24
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

//$from = date("Y-m-d");
$from = date("Y-m-d",strtotime(date("Y-m-d") . ' -30 day'));
$to = date("Y-m-d");
//$to = date("Y-m-d",strtotime(date("Y-m-d") . ' +30 day'));

if(isset($_GET['from'])) $from = $_GET['from'];
if(isset($_GET['to'])) $to = $_GET['to'];
if(isset($_GET['user'])) $userId = $_GET['user'];

//var_dump($_GET);

function compareByTimeStamp($time1, $time2) 
{

	$time11 = date('M/d/Y H:m:s', strtotime($time1["CREATED"]));
	$time22 = date('M/d/Y H:m:s', strtotime($time2["CREATED"]));
	if(strpos($time1["COMMENT"], ' SMS ') || strpos($time1["COMMENT"], ' MMS ')){
		$time11 = date('M/d/Y H:m:s', strtotime($time1["CREATED"]) - 60 * 60 * 8);
	}
	if(strpos($time2["COMMENT"], ' SMS ') || strpos($time2["COMMENT"], ' MMS ')){
		$time22 = date('M/d/Y H:m:s', strtotime($time2["CREATED"]) - 60 * 60 * 8);
	}

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

//$deals = DBGet("select b_crm_deal.ID,UF_CRM_1607116909,SUBSTRING(UF_CRM_1607456246, 12, 3) as Surgeon,OPPORTUNITY,NAME,LAST_NAME,PERSONAL_NOTES from b_crm_deal INNER JOIN b_uts_crm_deal ON b_uts_crm_deal.VALUE_ID = b_crm_deal.ID INNER JOIN b_user ON b_user.ID = SUBSTRING(UF_CRM_1607456246, 12, 3) where (UF_CRM_1607456246 like '%i:183;%' or UF_CRM_1607456246 like '%i:184;%' or UF_CRM_1607456246 like '%i:185;%' or UF_CRM_1607456246 like '%i:186;%') and UF_CRM_1607116909 between '".$from." 00:00:00' and '".$to." 23:59:59' and UF_CRM_1609345447 = '1' and UF_CRM_1607462214 = '1' limit ".$maxrows.";");
//$deal_totals = DBGet("select COUNT(ID) as count,MAX(UF_CRM_1607116909) as Maxdate,Surgeon,SUM(OPPORTUNITY) as totalsum,NAME,LAST_NAME,PERSONAL_NOTES from (select b_crm_deal.ID,UF_CRM_1607116909,SUBSTRING(UF_CRM_1607456246, 12, 3) as Surgeon,OPPORTUNITY,NAME,LAST_NAME,PERSONAL_NOTES from b_crm_deal INNER JOIN b_uts_crm_deal ON b_uts_crm_deal.VALUE_ID = b_crm_deal.ID INNER JOIN b_user ON b_user.ID = SUBSTRING(UF_CRM_1607456246, 12, 3) where (UF_CRM_1607456246 like '%i:183;%' or UF_CRM_1607456246 like '%i:184;%' or UF_CRM_1607456246 like '%i:185;%' or UF_CRM_1607456246 like '%i:186;%') and UF_CRM_1607116909 between '".$from." 00:00:00' and '".$to." 23:59:59' and UF_CRM_1609345447 = '1' and UF_CRM_1607462214 = '1') as deals group by Surgeon limit ".$maxrows.";");

	$deals = DBGet("select b_crm_deal.ID,UF_CRM_1607116909,SUBSTRING(UF_CRM_1607456246, 12, 3) as Surgeon,OPPORTUNITY,NAME,LAST_NAME,PERSONAL_NOTES from b_crm_deal INNER JOIN b_uts_crm_deal ON b_uts_crm_deal.VALUE_ID = b_crm_deal.ID INNER JOIN b_user ON b_user.ID = SUBSTRING(UF_CRM_1607456246, 12, 3) where (UF_CRM_1607456246 like '%i:183;%' or UF_CRM_1607456246 like '%i:184;%' or UF_CRM_1607456246 like '%i:185;%' or UF_CRM_1607456246 like '%i:186;%') and CONVERT_TZ(UF_CRM_1607116909,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' and UF_CRM_1609345447 = '1' and UF_CRM_1607462214 = '1' limit ".$maxrows.";");
	$deal_totals = DBGet("select COUNT(ID) as count,MAX(UF_CRM_1607116909) as Maxdate,Surgeon,SUM(OPPORTUNITY) as totalsum,NAME,LAST_NAME,PERSONAL_NOTES from (select b_crm_deal.ID,UF_CRM_1607116909,SUBSTRING(UF_CRM_1607456246, 12, 3) as Surgeon,OPPORTUNITY,NAME,LAST_NAME,PERSONAL_NOTES from b_crm_deal INNER JOIN b_uts_crm_deal ON b_uts_crm_deal.VALUE_ID = b_crm_deal.ID INNER JOIN b_user ON b_user.ID = SUBSTRING(UF_CRM_1607456246, 12, 3) where (UF_CRM_1607456246 like '%i:183;%' or UF_CRM_1607456246 like '%i:184;%' or UF_CRM_1607456246 like '%i:185;%' or UF_CRM_1607456246 like '%i:186;%') and CONVERT_TZ(UF_CRM_1607116909,'+00:00','-05:00') between '".$from." 00:00:00' and '".$to." 23:59:59' and UF_CRM_1609345447 = '1' and UF_CRM_1607462214 = '1') as deals group by Surgeon limit ".$maxrows.";");

//var_dump($deals);

$all_products = array();
$total = array();
foreach($deals as $deal){

	$get_deal_products = api (
    'crm.deal.productrows.get' ,
   		[
			'id' => $deal['ID']
    	]);

	foreach($get_deal_products["result"] as $product){
		if(strpos($product['PRODUCT_NAME'], $deal['NAME']) !== false){
			array_push($all_products,array("deal" => $deal['ID'],"doctor" => $deal['NAME']." ".$deal['LAST_NAME'], "product" => $product));
			$total[$deal['NAME']] = $total[$deal['NAME']] + ($product['PRICE'] * $product['QUANTITY']);
		} //else var_dump(strpos($product['PRODUCT_NAME'], $deal['NAME']));
	}

}

//var_dump($all_products);

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

<div class="container">
  <div class="row">
    <div class="col-md-9">
</div>
<div class="col-md-3">
<p>FROM Surgery Date &nbsp;<input type="text" name="from" id="from"  value="<?php echo $from?>"></p>
 <p>TO Surgery Date   &nbsp; &nbsp;  &nbsp; &nbsp;<input type="text" name="to" id="to"  value="<?php echo $to?>"></p>
		<input id="date" type="submit" value="Update Information">
</div>
  </div>
</div>

	</center><br/>


<div class="container">
  <div class="row">
    <div class="col-sm-12">



<table name="myTable" id="myTable"  class="table table-striped table-bordered table-hover">
	<thead class="thead-dark">
	<tr>
<th scope="col">Doctor</th>
<th scope="col">Deal#</th>
<th scope="col">Product Name</th>
	  <th scope="col">Quantity</th>
	  <th scope="col">Price</th>

    </tr>	
	</thead>
	<tbody>
	<?php foreach ($all_products as $row): array_map('htmlentities', $row);?>


<?php
			//$topay = 0;
		/*$porcentaje_pos = strpos($row['PERSONAL_NOTES'], "%");
		if($porcentaje_pos != false){ //it by porcentage
			//var_dump(substr($row['PERSONAL_NOTES'], 0, $porcentaje_pos));
			$row['topay'] = floatval($row['totalsum'])*(floatval(substr($row['PERSONAL_NOTES'], 0, -1))/100);
		} else { //it is a fixed amount
			$row['topay'] = floatval($row['count']) * floatval($row['PERSONAL_NOTES']);
		}*/
?>


	<tr>
	<td><?php echo $row['doctor'];?></td>
	<td><?php echo $row['deal'];?></td>
  <td><?php echo $row['product']['PRODUCT_NAME'];?></td>
	  <td><?php echo $row['product']['QUANTITY'];?></td>
	  <td><?php echo "$".$row['product']['PRICE'];?></td>




    </tr>
	<?php endforeach; ?>


	</tbody>
</table>



	<table name="myTable" id="myTable"  class="table table-striped table-bordered table-hover">
	<thead class="thead-dark">
	<tr>
<th scope="col">Name</th>
<th scope="col">Last Name</th>
	  <th scope="col">Deals Count</th>
	  <th scope="col">Total</th>
<th scope="col">Doctor Payment Info.</th>
<th scope="col">To Pay Doctor</th>
    </tr>	
	</thead>
	<tbody>
	<?php foreach ($deal_totals as $row): array_map('htmlentities', $row);?>


<?php
			//$topay = 0;
		$porcentaje_pos = strpos($row['PERSONAL_NOTES'], "%");
		if($porcentaje_pos != false){ //it by porcentage
			//var_dump(substr($row['PERSONAL_NOTES'], 0, $porcentaje_pos));
			$row['topay'] = floatval($total[$row['NAME']])*(floatval(substr($row['PERSONAL_NOTES'], 0, -1))/100);
		} else { //it is a fixed amount
			$row['topay'] = floatval($row['count']) * floatval($row['PERSONAL_NOTES']);
		}
?>


	<tr>
	<td><?php echo $row['NAME'];?></td>
  <td><?php echo $row['LAST_NAME'];?></td>
	  <td><?php echo $row['count'];?></td>
	  <td><?php echo "$".$total[$row['NAME']];?></td>

	  <td><?php echo $row['PERSONAL_NOTES'];?></td>
		<td><?php echo "$".$row['topay'];?></td> 

    </tr>
	<?php endforeach; ?>


	</tbody>
</table>
		<p>The Search it is done by Surgery Dates for surgerys completed!</p>
</div>
  </div>
</div>




<?php
		//var_dump($userId);	
}
?>

	<script>
		var auth = "<?php echo $arAccessParams['access_token']; ?>";
		var refresh = "<?php echo $arAccessParams['refresh_token']; ?>";
		var baseUrl = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/DoctorsPay/index.php?";
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