<?php
//Var only for local API cases with start parameter
$restonly = 'https://crm_webhook'; //Functional Webhook with SSL!!
//######################################################
//Simple Function to Query directly the Rest API!
//Ronald
//######################################################
function api($function = 'profile',$query = array()){

	/*
	$QueryExample =  array( //Query to find Manager of department
	  					'FILTER' =>
								[
										"UF_DEPARTMENT" => $myresponsable['result'][0]['UF_DEPARTMENT'],
										"WORK_POSITION" => "Manager"
								]
	    			);
	*/

	//###################################################################
	$restonly = 'https://crm_webhook';
	//###################################################################

	if($query === array()){
		$resturlm = $restonly . '/' . $function; //Get API, no arguments
	} else {
		$resturlm = $restonly . '/' . $function . '?'. http_build_query($query); //Get API with arguments
	}

	return json_decode(file_get_contents($resturlm),true); //json to array

}

/*$QueryExample =  array( //Query to find Manager of department
	  					'FILTER' =>
								[
									//"UF_DEPARTMENT" => $myresponsable['result'][0]['UF_DEPARTMENT'],
									//"LEAD_ID" => null
								]
	    			);

var_dump(api('crm.contact.list',$QueryExample));*/

?>