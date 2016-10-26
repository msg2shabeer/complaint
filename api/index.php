<?php
require 'vendor/autoload.php';

// initate application
$app = new \Slim\Slim(array(
		'debug' 				=> true,
		'cookies.encrypt' 		=> true,
		'cookies.secret_key'	=> 'complaintencriptionkey',
		'cookies.cipher' 		=> MCRYPT_RIJNDAEL_256,
		'cookies.cipher_mode' 	=> MCRYPT_MODE_CBC,
		'http.version' 			=> '1.1',
));

// test rout
$app->get('/',function(){
	echo "Welcome to Complaint Management API system";
});

// get all users
$app->get('/users/',function() use($app){
	// include_once 'config/db.php';
	include_once 'config/common_functions.php';
	$users = array();
	foreach ($complaint->users() as $user) {
		$users[] = array(
			'id'			=> $user['id'],
			'user_id'		=> $user['user_id'],
			'name'			=> $user['name'],
			'user_type_id'	=> $user['user_type_id']
		);
	}
	$app->response()->header("Content-Type","application/json");
	echo json_encode($users);
});

// get single user
$app->get('/user/:id',function($uid) use($app){
	include_once 'config/db.php';
	$user = array();
	foreach ($complaint->users("id = ?",$uid) as $suser) {
		$user[] = array(
			'id'			=> $suser['id'],
			'user_id'		=> $suser['user_id'],
			'name'			=> $suser['name'],
			'user_type_id'	=> $user['user_type_id']
		);
		$app->response()->header("Content-Type","application/json");
		echo json_encode($user);
	}
});

// put a single user
$app->post('/addUser/',function() use($app){
	// include_once 'config/db.php';
	include_once 'config/common_functions.php';
	$usr_name 				= 	sanitize($app->request->post('usr_name'));
	$usr_userId 			= 	sanitize($app->request->post('usr_userId'));
	$usr_password 			=	sanitize($app->request->post('usr_password'));
	$insert 				= 	$complaint->users()->insert(
		array(
			"name" 			=> $usr_name, 
			"user_id" 		=> $usr_userId,
			"password"		=> md5($usr_password)
			)
		);
	$result 				=	array('id' => $insert['id']);
	$app->response()->header("Content-Type","application/json");
	echo json_encode($result);
});

// get all complaints
$app->get('/complaints/',function() use($app){
	// include_once 'config/db.php';
	include_once 'config/common_functions.php';
	$complaints = array();
	foreach ($complaint->complaints() as $complaint) {
		// get complaint priority
		$c_priority = get_complaint_priority($complaint['no_calls'],$complaint['date_time']);
		$complaints[] = array(
			'id'				=> $complaint['id'],
			'customer_id'		=> $complaint['customer_id'],
			'customer_address'	=> $complaint['customer_address'],
			'customer_phone'	=> $complaint['customer_phone'],
			'complaint_phone'	=> $complaint['complaint_phone'],
			'no_calls'			=> $complaint['no_calls'],
			'status_id'			=> $complaint['status_id'],
			'date_time' 		=> $complaint['date_time'],
			'priority' 			=> $c_priority
		);
	}
	$app->response()->header("Content-Type","application/json");
	echo json_encode($complaints);
});

// get a complaint by its id

// get complaint by customer id

// put a complaint
$app->post('/addComplaint/',function() use($app){
	// include_once 'config/db.php';
	include_once 'config/common_functions.php';
	$cmpt_customer_id		= 	sanitize($app->request->post('cmpt_customer_id'));
	$cmpt_customer_address	= 	sanitize($app->request->post('cmpt_customer_address'));
	$cmpt_customer_phone	=	sanitize($app->request->post('cmpt_customer_phone'));
	$cmpt_complaint_phone	=	sanitize($app->request->post('cmpt_complaint_phone'));
	// check is there any open complaint against customer
	$complaint_id = any_open_complaint($cmpt_customer_id); // return complaint id if exist else false
	if (!$complaint_id) {
		// create complaint
		$insert 			= 	$complaint->complaints()->insert(
			array(
				"customer_id" 		=> $cmpt_customer_id, 
				"customer_address" 	=> $cmpt_customer_address,
				"customer_phone"	=> $cmpt_customer_phone,
				"complaint_phone"	=> $cmpt_complaint_phone,
				"no_calls" 			=>	1,
				"status_id"			=>	1
				)
			);
		$result 				=	array('id' => $insert['id'],'job' => 'create');
	}
	else{
		// update complaint's no of calls 
		$result 			=	increment_no_calls($complaint_id);
	}
	$app->response()->header("Content-Type","application/json");
	echo json_encode($result);
});

// run the application
$app->run();
?>
