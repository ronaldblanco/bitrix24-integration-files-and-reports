<?php
//Var only for local API cases with start parameter
$restonly = 'https://crm.api.com/rest/1/1234'; //Functional Webhook with SSL!!
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
	
	if($query === array()){
		$resturlm = $restonly . '/' . $function; //Get API, no arguments
	} else {
		$resturlm = $restonly . '/' . $function . '?'. http_build_query($query); //Get API with arguments
	}

	//echo $resturlm;
	//die;
	/*$ch =  curl_init($resturlm);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
$result = curl_exec($ch);*/
	
	$arrContextOptions=array(
      "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  

	//return json_decode(file_get_contents($resturlm),true); //json to array
	return json_decode(file_get_contents($resturlm, false, stream_context_create($arrContextOptions)),true);
	//return result;

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