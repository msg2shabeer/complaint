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

// run the application
$app->run();
?>
