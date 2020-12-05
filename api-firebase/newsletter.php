<?php 
header('Access-Control-Allow-Origin: *');
include_once('../includes/custom-functions.php');
$fn = new custom_functions;
include_once('../includes/crud.php');
$db = new Database();
$db->connect();

if(isset($_POST) && isset($_POST['accesskey']) && isset($_POST['email']) && !empty($_POST['email'])){
    include_once('../includes/variables.php');
    $response = [];
    
    $access_key_received = isset($_POST['accesskey']) && !empty($_POST['accesskey'])? $db->escapeString($fn->xss_clean($_POST['accesskey'])) : '';
    if($access_key_received != $access_key){
        $response['error'] = true;
        $response['message'] = "Invalid access key passed.";
        echo json_encode($response);
        return false;
        exit(0);
    }
	
	$sql = "select count(*) as total from newsletter where email = '$_POST[email]'";
	$db->sql($sql);
	$exist = $db->getResult();
	
    if(isset($exist[0]['total']) && intval($exist[0]['total'])){
        $response['error'] = true;
        $response['message'] = "Thanks! You're Already Subscribed.";
        echo json_encode($response);
    }else{		
		$db->insert('newsletter', ['email' => $_POST['email']]);  // Table name, column names and respective values
		$res = $db->getResult();
		$response['error'] = false;
        $response['message'] = "Thanks! We'll not spam you.";
        echo json_encode($response);
    }
}else{
    $response['error'] = true;
    $response['message'] = "Please pass all the fields.";
    echo json_encode($response);
}
?>