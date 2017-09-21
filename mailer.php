<?php

#phpinfo();
#var_dump(ini_get_all());
#ini_set('error_reporting', E_ALL);

// set key (letters/numbers only) according to the key you set in radar.php
$user_key = 'YOUR_USER_KEY';

// set your email address if you want to use email alert
$user_email = 'YOUR_EMAIL@EMAIL.COM';

// set your pushover token and key if you want to use pushover alert
$pushover_api_token = 'YOUR_API_TOKEN'; $pushover_user_key = 'YOUR_USER_KEY';

isset($_GET['key']) ? $key = $_GET['key'] : $key = '';
isset($_GET['mode']) ? $mode = $_GET['mode'] : $mode = '';
isset($_GET['subject']) ? $subject = urldecode($_GET['subject']) : $subject = '';
isset($_GET['body']) ? $body = urldecode($_GET['body']) : $body = '';

if ($key == $user_key && $mode == 'webmail') {
	$header  = 'MIME-Version: 1.0' . PHP_EOL;
	$header .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
	$header .= 'From: ADS-B RADAR' . PHP_EOL;
	$header .= 'Reply-To: ' . $user_email . PHP_EOL;
	$header .= 'X-Mailer: PHP '. phpversion();
	mail($user_email, $subject, $body, $header);
} elseif ($key == $user_key && $mode == 'pushover') {
	curl_setopt_array($ch = curl_init(), array(
		CURLOPT_URL => 'https://api.pushover.net/1/messages.json',
		CURLOPT_POSTFIELDS => array('token' => $pushover_api_token, 'user' => $pushover_user_key, 'message' => $body, 'html' => '1'),
		CURLOPT_SAFE_UPLOAD => true,
		CURLOPT_RETURNTRANSFER => true,
	));
	curl_exec($ch);
	curl_close($ch);
}

?>
