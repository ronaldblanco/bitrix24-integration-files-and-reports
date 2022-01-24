<?php


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/API/API.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/dhhdbw73723934dvrgintegration/DB/DB.php");


$users = DBGet("SELECT ID,NAME,LAST_NAME,WORK_DEPARTMENT,WORK_POSITION FROM b_user;");

$tags_response = json_decode(file_get_contents('https://crm.domain.com/rest/1/23odl51wmxp7ea4p/crm.contact.fields  '),true);
$tags = $tags_response['result']['UF_CRM_1591040450352']['items'];

$positive = explode( ',', $_GET['positive'] );
$negative = explode( ',', $_GET['negative'] );

$showOptions = [50, 100, 200];

function createPositiveQuery($array){
	$queryString = array_map(function($value){
		$string = " b_uts_crm_contact.UF_CRM_1591040450352 LIKE '%i:".trim($value)."%' ";
		return $string;
	}, $array);
	$positiveAll = isset($_GET['positiveAll'])?$_GET['positiveAll']:0;
	if($positiveAll==1){
		return ' AND '. implode(" AND ", $queryString);
	}
	else{
		return ' AND '. implode(" OR ", $queryString);
	}

}

function createNegativeQuery($array){
	$queryString = array_map(function($value){
		$string = " b_uts_crm_contact.UF_CRM_1591040450352 NOT LIKE '%i:".trim($value)."%' ";
		return $string;
	}, $array);
	$negativeAll = isset($_GET['negativeAll'])?$_GET['negativeAll']:0;
	if($negativeAll>0){
		return ' AND '. implode(" AND ", $queryString);
	}
	return ' AND '. implode(" OR ", $queryString);
}

$positiveQuery = isset($_GET['positive'])?createPositiveQuery($positive):"";
$negativeQuery = isset($_GET['negative'])?createNegativeQuery($negative):"";

$assignedQuery = isset($_GET['assigned'])?" AND ASSIGNED_BY_ID=".$_GET['assigned']." ":"";

$currentPage = isset($_GET['page'])?$_GET['page']:1;
$totalQuery = DBGet("SELECT COUNT(ID) AS TOTAL FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID WHERE 1=1 ".$assignedQuery.$positiveQuery.$negativeQuery);
$total = $totalQuery[0]["TOTAL"];

$limit = isset($_GET['show'])?$_GET['show']:50;
$offset = ($currentPage-1) * $limit;
$query = "SELECT ID, NAME, SOURCE_ID, ASSIGNED_BY_ID,b_uts_crm_contact.UF_CRM_1591040450352 FROM b_crm_contact INNER JOIN b_uts_crm_contact ON b_uts_crm_contact.VALUE_ID = b_crm_contact.ID WHERE 1=1 ".$assignedQuery.$positiveQuery.$negativeQuery." ORDER BY ID LIMIT ".$limit." OFFSET ".$offset;

$contacts = DBGet($query);


function checkValueExist($option, $array){
	$exist = 0;
	foreach($array as $key => $value){

		if($option == $value){
			$exist = 1;
		}
	}
	return $exist;
}

function getTagNames($tags, $ids){
	$names = [];
	foreach($ids as $id){
		foreach($tags as $tag){

			if($tag['ID']==$id){
				array_push($names, $tag['VALUE']);
			}
		}
	}
	return implode(", ", $names);
}

function getAssignedName($id, $users){
	$name = $id;
	foreach($users as $user){
		if($user['ID']==$id){
			$name = $user['NAME'];
			break;
		}
	}
	return $name;
}

?>
<title>Contacts</title>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contacts</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
	<style>
		.pageWrapper {
			margin: 10px;
		}
		.searchWrapper {
			display: flex;
			margin-bottom: 10px;
		}
		.searchWrapper .column {
			display: flex;
			margin: 0 10px;
			flex-direction: column;
			margin-right: 5px;
			justify-content: flex-end;
		}
		.searchWrapper .requiredAll {
			float: right;
		}
		.showWrapper{
			align-self: flex-end;
			position: absolute;
			right: 30px;
			flex-direction: row !important;
			align-items: baseline;
		}
		.showWrapper label {
			margin-right: 5px;
		}
		.contactsWrapper {
			max-height: 700px;
			overflow-y: auto;
		}
		.actionsWrapper {
			display:  none;
		}
		#applyTagButton, #applyTaskButton, #showTagSelect, #showAssignSelect, #applyAssignButton, #removeTagButton {
			display: none;
		}
		#pagination {
			display: flex;
			justify-content: center;
		}
		#pagination ul {
			display: flex;
			list-style: none;
			margin: 5px;
			padding: 0;
			border-radius: 15px;
			margin-bottom: 10px;
			background: #CCC;
		}
		#pagination ul li {
			margin: 5px;
			cursor: pointer;
		}
	</style>
</head>

<body>
	<div class="pageWrapper">
		<h2>Contact search:</h2>

		<div class="searchWrapper">
			<div class="column">
				<label>Include: <div class="requiredAll"><input type="checkbox" id="positiveRequired" name="positiveRequired" <?php echo isset($_GET['positiveAll'])?"checked":"" ?>> require all </div></label>

				<select id="positiveTags" name="positiveTags" multiple="multiple">
					<?php foreach ($tags as $row): array_map('htmlentities', $row);?>
					<option value="<?php echo $row['ID'];?>" <?php echo checkValueExist($row['ID'], $positive)==1?"selected":""?> ><?php echo $row['VALUE'];?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="column">
				<label>Exclude: <div class="requiredAll"><input type="checkbox" id="negativeRequired" name="negativeRequired" <?php echo isset($_GET['negativeAll'])?"checked":"" ?>> require all </div></label>
				<select id="negativeTags" name="negativeTags" multiple="multiple">
					<?php foreach ($tags as $row): array_map('htmlentities', $row);?>
					<option value="<?php echo $row['ID'];?>" <?php echo checkValueExist($row['ID'], $negative)==1?"selected":""?> ><?php echo $row['VALUE'];?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="column">
				<label>Assigned:</label>
					<select id="assignedList" name="assignedList" class="form-control">
						<option value="" selected>None</option>
						<?php foreach ($users as $row):?>
						<option value="<?php echo $row['ID'];?>" <?php echo $_GET['assigned']==$row['ID']?"selected":"";?> ><?php echo $row['NAME'];?></option>
						<?php endforeach; ?>
					</select>
			</div>
			<div class="column">
				<button id="search" class="btn btn-primary">search</button>
			</div>



			<div class="column showWrapper">
				<label>show:</label>
				<select id="show" name="show">
					<?php foreach ($showOptions as $option):?>
					<option value="<?php echo $option;?>" <?php echo $_GET['show']==$option?"selected":""?> ><?php echo $option;?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="contactsWrapper">
			<table class="table table-striped table-bordered table-hover">
			<thead class="thead-dark">
		
			<tr>
				<th scope="col">
					<input type="checkbox" id="checkAll" name="checkAll">
				</th>
			  <th scope="col">ID</th>
			  <th scope="col">Full Name</th>
			  <th scope="col">Tags</th>
			  <th scope="col">ASSIGNED</th>
			</tr>	
		
			</thead>
			<tbody>
			<?php foreach ($contacts as $contact):
				$tagIds = unserialize($contact['UF_CRM_1591040450352']);
			 ?>
			<tr id="rows">
				<td><input class="contactCheckBox" type="checkbox" id="<?php echo $contact['ID']; ?>" name="<?php echo $contact['ID']; ?>" value="<?php echo $contact['ID']; ?>"></td>
			  	<td><?php echo $contact['ID']; ?></td>
				<td><a href="https://crm.domain.com/crm/contact/details/<?php echo $contact['ID']; ?>/" target="_blank"><?php echo $contact['NAME']; ?></a></td>
				<td><?php echo getTagNames( $tags, $tagIds ); ?></td>
				<td><?php echo getAssignedName($contact['ASSIGNED_BY_ID'], $users);?></td>
			</tr>
			<?php endforeach; ?>
		
			</tbody>
		</table>
		</div>


		<div class="actionsWrapper form-inline">
				<select id="actions" name="actions" class="form-control">
					<option value="" selected disabled hidden>Select action</option>
					<option value="assignTo">Assign to responsable person</option>
					<option value="additionalTag">Additional tag</option>
					<option value="removeTag">Remove tag</option>
					<option value="addComment">Add comment</option>
				</select>
				<button id="applyTaskButton" class="btn btn-primary applyTaskButton">Apply</button>
				<select id="showTagSelect" name="showTagSelect" class="form-control">
					<?php foreach ($tags as $row):?>
					<option value="<?php echo $row['ID'];?>" ><?php echo $row['VALUE'];?></option>
					<?php endforeach; ?>
				</select>
				<button id="applyTagButton" class="btn btn-primary applyTagButton">Apply</button>
				<button id="removeTagButton" class="btn btn-primary removeTagButton">Apply</button>
				<select id="showAssignSelect" name="showAssignSelect" class="form-control">
					<?php foreach ($users as $row):?>
					<option value="<?php echo $row['ID'];?>" ><?php echo $row['NAME'];?></option>
					<?php endforeach; ?>
				</select>
				<button id="applyAssignButton" class="btn btn-primary applyAssignButton">Apply</button>


		</div>



		<div id="pagination"></div>
</div>

	<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
	<script src="./bootstrap-paginator.min.js"></script>


	<script>
		var baseUrl = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/0000_contact_tags_search.php?";


		$(document).ready(function(){
			$('#positiveTags, #negativeTags').select2();

			$("input:checkbox").click(function(){
				console.log("checkbox selected");
				if($('.contactCheckBox:checkbox:checked').length > 0){
					$(".actionsWrapper").show();
				}
			});

			var options = {
				currentPage: "<?php echo $currentPage; ?>",
				totalPages: "<?php echo  $total/$limit+1; ?>",
				useBootstrapTooltip:true,
				onPageClicked: function(e,originalEvent,type,page){
					var queryParams = new URLSearchParams(window.location.search);
					queryParams.set("page", page);
					var url = baseUrl + queryParams.toString();
					window.location = url;
            	}
			}

			$('#pagination').bootstrapPaginator(options);
		});

		$("#checkAll").click(function(){
			if ($('#checkAll').is(':checked')) {
			  $('.contactCheckBox').attr('checked','checked');
		  } else {
			  $('.contactCheckBox').removeAttr('checked');
		  }       
		});

		$('#actions').on('change', function() {
			$("#showTagSelect").hide();
			$("#applyTagButton").hide();
			$("#showAssignSelect").hide();
			$("#applyAssignButton").hide();
			$("#removeTagButton").hide();

			var action = this.value;
			switch(action) {
			  case 'additionalTag':
				$("#showTagSelect").show();
				$("#applyTagButton").show();
				break;
			  case 'removeTag':
				$("#showTagSelect").show();
				$("#removeTagButton").show();
				break;
			  case 'assignTo':
				$("#showAssignSelect").show();
				$("#applyAssignButton").show();
				break;
			  case 'addComment':
					$("#commentModal").modal({
						keyboard: false
					})
				break;
			  default:
			}
		});



		$("#applyTagButton").click(function() {
			var chain = '';
			$('.contactCheckBox:checkbox:checked').each(function () {
				chain += ','+this.value;
			})
			var tagId = $("#showTagSelect").val();
			var contactids = chain.substring(1);

			var url = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/app_tags_filter/lib/addtag.php?contactsid="+contactids+"&tagid="+tagId;
			console.log(url);

			$.get( url, function( data ) {
				location.reload();
				alert( "tag added to contact/s" );
			});
			
		})

		$("#removeTagButton").click(function() {
			var chain = '';
			$('.contactCheckBox:checkbox:checked').each(function () {
				chain += ','+this.value;
			})
			var tagId = $("#showTagSelect").val();
			var contactids = chain.substring(1);

			var url = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/app_tags_filter/lib/rmtag.php?contactsid="+contactids+"&tagid="+tagId;
			console.log(url);

			$.get( url, function( data ) {
				location.reload();
				alert( "tag removed to contact/s" );
			});
			
		})

		$("#applyAssignButton").click(function() {
			var chain = '';
			$('.contactCheckBox:checkbox:checked').each(function () {
				chain += ','+this.value;
			})
			var resId = $("#showAssignSelect").val();
			var contactids = chain.substring(1);

			var url = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/app_tags_filter/lib/updateresp.php?contactsid="+contactids+"&newresp="+resId;
			console.log(url);

			$.get( url, function( data ) {
				location.reload();
				alert( "asigned to new responsable" );
			});
			
		})

		$("#addCommentButton").click(function() {
			var chain = '';
			$('.contactCheckBox:checkbox:checked').each(function () {
				chain += ','+this.value;
			})
			var comment = $("#addCommentBox").val();
			var contactids = chain.substring(1);

			var url = "https://crm.domain.com/bitrix/admin/dhhdbw73723934dvrgintegration/APP/app_tags_filter/lib/addcomment.php?contactsid="+contactids+"&COMMENT="+comment;
			$("#addCommentBox").val('');
			$("#actions").val("");
			$('#commentModal').modal('hide');

			$.get( url, function( data ) {
				location.reload();
				alert( "comment added" );
			});
			
		})

		$("#commentModal").on("hidden.bs.modal", function () {
			$("#addCommentBox").val('');
			$("#actions").val("");
		});

		$('#show').on('change', function() {
			var queryParams = new URLSearchParams(window.location.search);
			queryParams.set("show", this.value);
			var url = baseUrl + queryParams.toString();
			window.location = url;
		});

		$('#search').click(function() {
			var positive = [];
			$('#positiveTags option:selected').each(function() {
				var value = $(this).val()
				positive.push(value);
			});
			var positiveValues = positive.join(", ");

			var negative = [];
			$('#negativeTags option:selected').each(function() {
				var value = $(this).val()
				negative.push(value);
			});
			var negativeValues = negative.join(", ");

			// Construct URLSearchParams object instance from current URL querystring.
			var queryParams = new URLSearchParams(window.location.search);
			 
			// Set new or modify existing parameter value. 

			if(positiveValues!=''){
				queryParams.set("positive", positiveValues);
			}
			else{
				queryParams.delete("positive");
			}

			if(negativeValues!=''){
				queryParams.set("negative", negativeValues);
			}
			else{
				queryParams.delete("negative");
			}

			if($('#positiveRequired').is(':checked')) {
				queryParams.set("positiveAll", 1);
			}
			else{
				queryParams.delete("positiveAll");
			}


			if($('#negativeRequired').is(':checked')) {
				queryParams.set("negativeAll", 1);
			}
			else{
				queryParams.delete("negativeAll");
			}

			if($("#assignedList").val()!=""){
				queryParams.set("assigned", $("#assignedList").val());
			}
			else{
				queryParams.delete("assigned");
			}


			queryParams.set("page", 1);

			var url = baseUrl + queryParams.toString();
			window.location = url;
		});
	</script>
</body>
</html>

