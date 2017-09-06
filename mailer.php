<?php

#phpinfo();
#var_dump(ini_get_all());
#ini_set('error_reporting', E_ALL);

isset($_GET['key']) ? $key = $_GET['key'] : $key = '';
isset($_GET['subject']) ? $subject = urldecode($_GET['subject']) : $subject = '';
isset($_GET['body']) ? $body = urldecode($_GET['body']) : $body = '';

if ($key == 'YOUR_USER_KEY') { // set key (letters/numbers only) according to the key you set in radar.php
	$header  = 'MIME-Version: 1.0' . PHP_EOL;
	$header .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
	$header .= 'From: ADS-B RADAR' . PHP_EOL;
	$header .= 'Reply-To: YOUR_EMAIL@EMAIL.COM' . PHP_EOL; // set your email
	$header .= 'X-Mailer: PHP ". phpversion();
	mail('YOUR_EMAIL@EMAIL.COM', $subject, $body, $header); // set your email
}

?>
