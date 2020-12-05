<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');
include_once('../includes/variables.php');
include_once('verify-token.php');
include_once('../includes/custom-functions.php');
$fn = new custom_functions;

$db = new Database();
$db->connect(); 
if(!verify_token()){
	return false;
}

include_once('includes/custom-functions.php');

$function = new functions;

if(isset($_POST['accesskey'])) {

	$access_key_received = $db->escapeString($fn->xss_clean($_POST['accesskey']));

	$city_id = (isset($_POST['city_id']))?$db->escapeString($fn->xss_clean($_POST['city_id'])):"";

	if($access_key_received == $access_key){

		// create object of functions class
		$function = new functions;
		
		// create array variable to store data from database
		$data = array();
		
		if(isset($_POST['keyword'])){	
			$sql_query = "SELECT id, question, answer FROM faq where answer != '' and question LIKE ".$function->sanitize($_POST['keyword'])." ORDER BY id DESC";
		}else{
			$sql_query = "SELECT id, question, answer FROM faq where answer != '' ORDER BY id DESC";
		}

		$db->sql($sql_query);

		$res = $db->getResult();

		$total_records = $db->numRows($res);

		// check page parameter
		if(isset($_POST['page'])){

			$page = $_POST['page'];

		}else{

			$page = 1;

		}
		
		// number of data that will be display per page		
		$limit = 10;
		
		if(isset($_POST['limit'])){

			$limit = $_POST['limit'];

		}
		//lets calculate the LIMIT for SQL, and save it $from
		if ($page){
			$from 	= ($page * $limit) - $limit;
		}else{
			//if nothing was given in page request, lets load the first page
			$from = 0;	
		}	
		
		if(empty($keyword)){
			$sql_query = "SELECT id, question, answer FROM faq WHERE answer != '' ORDER BY id DESC LIMIT ".$from.",".$limit."";
		}else{
			$sql_query = "SELECT id, question, answer FROM faq WHERE answer != '' and question LIKE ".$keyword."  ORDER BY id DESC LIMIT ".$from.",".$limit."";
		}

		// Execute query
		$db->sql($sql_query);

		// store result 
		$res = $db->getResult();

		if(!empty($res)){

			$response['error'] = false;

			$response['data'] = $res;

			$response['total'] = $total_records;

		}else{

			$response['data'] = [];

			$response['total'] = 0;
			
			$response['error'] = false;

			$response['message'] = "No data found!";

		}

		$output = json_encode($response);

	}else{

		die('accesskey is incorrect.');

	}

} else {

	die('accesskey is required.');

}

//Output the output.
echo $output;

$db->disconnect(); 
?>