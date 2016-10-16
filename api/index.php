<?php
require 'vendor/autoload.php';

// initate application
$app = new \Slim\Slim(array(
		'debug' 				=> true,
		'cookies.encrypt' 		=> true,
		'cookies.secret_key'	=> 'shebeesrobustapi',
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
	include_once 'config/db.php';
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


// run the application
$app->run();
?>
