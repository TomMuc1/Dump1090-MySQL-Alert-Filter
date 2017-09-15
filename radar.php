<?php

#phpinfo();
#var_dump(ini_get_all());
#ini_set('error_reporting', E_ALL);

// below a sample create statement for database table
// CREATE TABLE aircrafts (id INT NOT NULL AUTO_INCREMENT, date VARCHAR(100), now VARCHAR(100), hex VARCHAR(100), flight VARCHAR(100), altitude VARCHAR(100), lat VARCHAR(100), lon VARCHAR(100), track VARCHAR(100), speed VARCHAR(100), vert_rate VARCHAR(100), seen_pos VARCHAR(100), seen VARCHAR(100), rssi VARCHAR(100), messages VARCHAR(100), category VARCHAR(100), squawk VARCHAR(100), nucp VARCHAR(100), mlat VARCHAR(100), tisb VARCHAR(100), PRIMARY KEY (id))

// set the rectangle and altitude to store aircraft-data in database
$user_set_array['max_lat'] = 50.000000;    $user_set_array['min_lat'] = 46.000000;    $user_set_array['max_alt'] = 10000;
$user_set_array['max_lon'] = 14.000000;    $user_set_array['min_lon'] = 10.000000;

// set the rectangle and altitude to send alert message
$user_set_array['alert_max_lat'] = 49.000000;    $user_set_array['alert_min_lat'] = 47.000000;    $user_set_array['alert_max_alt'] = 5000;
$user_set_array['alert_max_lon'] = 13.000000;    $user_set_array['alert_min_lon'] = 11.000000;

// set lookup-interval default is 1 (must be integer between 1 - 900) this is the frequency the script runs and writes to database or looks for alerts
$user_set_array['sleep'] = 1;

// set to true and your google email-address if for alert-messages you want to use gmail instead own mailer.php file
$user_set_array['gmail'] = false; $user_set_array['email_address'] = 'YOUR_EMAIL@gmail.com';

// set parameters for database connection
$user_set_array['db_name'] = 'adsb'; $user_set_array['db_host'] = '127.0.0.1'; $user_set_array['db_user'] = 'USERNAME'; $user_set_array['db_pass'] = 'PASSWORD';

// set path to aircraft.json file
$user_set_array['url_json'] = 'http://127.0.0.1/dump1090/data/aircraft.json';

// set path to your mailer.php file
$user_set_array['url_mailer'] = 'http://YOUR_WEBSPACE.COM/mailer.php';

// set a key (letters/numbers only) according to mailer.php
$user_set_array['secret_email_key'] = 'YOUR_USER_KEY';

// set the absolute limit of alert-messages (default is 1000) this script is allowed to send over its whole runtime
$user_set_array['mailer_limit'] = 1000;

// set aircraft suspend time (default is 900) - change only if needed - time in seconds an aicraft is suspended from alert-messages after sending an alert-message for this aircraft
$user_set_array['aircraft_suspend_time'] = 900;

// set this to true if you want only alerts and/or database writes from those aircrafts matching your hex_code_array.txt or flight_code_array.txt files
$user_set_array['filter_mode_alert'] = false;    $user_set_array['filter_mode_database'] = false;

// set path to your hex_code_array.txt and flight_code_array.txt files
$user_set_array['hex_file_path'] = '/home/pi/hex_code_array.txt';
$user_set_array['flight_file_path'] = '/home/pi/flight_code_array.txt';

// set your timezone see http://php.net/manual/en/timezones.php
$user_set_array['time_zone'] = 'America/Chicago';



function func_wildcard_search($needle, $haystack) {
	$match = false;
	foreach ($haystack as $pattern) {
		if (preg_match('/^' . trim($pattern) . '$/', strtoupper($needle))) $match = true;
	}
	return $match;
}

$i = 0;
$alert_message = '';
$sent_alert_messages = 0;
$alert_trigger_array = array();
$start_time = time();
date_default_timezone_set($user_set_array['time_zone']);

while (true) {

	$x = 0;
	$sql = '';
	$start_loop_microtime = microtime(true);
	$json_data_array = json_decode(file_get_contents($user_set_array['url_json']),true);
	if ($user_set_array['filter_mode_alert'] || $user_set_array['filter_mode_database']) {
		$hex_code_array = explode(',', str_replace('%', '.', strtoupper(file_get_contents($user_set_array['hex_file_path']))));
		$flight_code_array = explode(',', str_replace('%', '.', strtoupper(file_get_contents($user_set_array['flight_file_path']))));
	}

	// loop through aircraft.json file
	foreach ($json_data_array['aircraft'] as $row) {
		isset($json_data_array['now']) ? $ac_now = $json_data_array['now'] : $ac_now = '';
		isset($row['hex']) ? $ac_hex = $row['hex'] : $ac_hex = '';
		isset($row['flight']) ? $ac_flight = trim($row['flight']) : $ac_flight = '';
		isset($row['altitude']) ? $ac_altitude = $row['altitude'] : $ac_altitude = '';
		isset($row['lat']) ? $ac_lat = $row['lat'] : $ac_lat = '';
		isset($row['lon']) ? $ac_lon = $row['lon'] : $ac_lon = '';
		isset($row['track']) ? $ac_track = $row['track'] : $ac_track = '';
		isset($row['speed']) ? $ac_speed = $row['speed'] : $ac_speed = '';
		isset($row['vert_rate']) ? $ac_vert_rate = $row['vert_rate'] : $ac_vert_rate = '';
		isset($row['seen_pos']) ? $ac_seen_pos = $row['seen_pos'] : $ac_seen_pos = '';
		isset($row['seen']) ? $ac_seen = $row['seen'] : $ac_seen = '';
		isset($row['rssi']) ? $ac_rssi = $row['rssi'] : $ac_rssi = '';
		isset($row['messages']) ? $ac_messages = $row['messages'] : $ac_messages = '';
		isset($row['category']) ? $ac_category = $row['category'] : $ac_category = '';
		isset($row['squawk']) ? $ac_squawk = $row['squawk'] : $ac_squawk = '';
		isset($row['nucp']) ? $ac_nucp = $row['nucp'] : $ac_nucp = '';
		isset($row['mlat']) ? $ac_mlat = implode(' ', $row['mlat']) : $ac_mlat = '';
		isset($row['tisb']) ? $ac_tisb = implode(' ', $row['tisb']) : $ac_tisb = '';

		// generate sql insert statement per aircraft in range of user set altitude/latitude/longitude and optionally according only to hex or flight numbers in hex_code_array.txt and flight_code_array.txt
		#var_dump($hex_code_array); var_dump($flight_code_array); // show arrays for debug
		if ($user_set_array['filter_mode_database']) {
			if (($ac_altitude != '' && $ac_altitude < $user_set_array['max_alt'] && $ac_lat < $user_set_array['max_lat'] && $ac_lat > $user_set_array['min_lat'] && $ac_lon < $user_set_array['max_lon'] && $ac_lon > $user_set_array['min_lon']) && (func_wildcard_search($ac_hex, $hex_code_array) || ($ac_flight != '' && func_wildcard_search($ac_flight, $flight_code_array)))) {
				$sql .= "INSERT INTO aircrafts VALUES (NULL, '" . date("Y-m-d G:i:s l", $ac_now) . "', '$ac_now', '$ac_hex', '$ac_flight', ";
				$sql .= "'$ac_altitude', '$ac_lat', '$ac_lon', '$ac_track', '$ac_speed', '$ac_vert_rate', '$ac_seen_pos', '$ac_seen', ";
				$sql .= "'$ac_rssi', '$ac_messages', '$ac_category', '$ac_squawk', '$ac_nucp', '$ac_mlat', '$ac_tisb');";
				$sql .= PHP_EOL;
				$x++;
			}
		} else {
			if ($ac_altitude != '' && $ac_altitude < $user_set_array['max_alt'] && $ac_lat < $user_set_array['max_lat'] && $ac_lat > $user_set_array['min_lat'] && $ac_lon < $user_set_array['max_lon'] && $ac_lon > $user_set_array['min_lon']) {
				$sql .= "INSERT INTO aircrafts VALUES (NULL, '" . date("Y-m-d G:i:s l", $ac_now) . "', '$ac_now', '$ac_hex', '$ac_flight', ";
				$sql .= "'$ac_altitude', '$ac_lat', '$ac_lon', '$ac_track', '$ac_speed', '$ac_vert_rate', '$ac_seen_pos', '$ac_seen', ";
				$sql .= "'$ac_rssi', '$ac_messages', '$ac_category', '$ac_squawk', '$ac_nucp', '$ac_mlat', '$ac_tisb');";
				$sql .= PHP_EOL;
				$x++;
			}
		}

		// set and modify alert-trigger-array and build alert-message optionally according only to hex or flight numbers in hex_code_array.txt and flight_code_array.txt
		if ($user_set_array['filter_mode_alert']) {
			if (($ac_altitude != '' && $ac_altitude < $user_set_array['alert_max_alt'] && $ac_lat < $user_set_array['alert_max_lat'] && $ac_lat > $user_set_array['alert_min_lat'] && $ac_lon < $user_set_array['alert_max_lon'] && $ac_lon > $user_set_array['alert_min_lon']) && (func_wildcard_search($ac_hex, $hex_code_array) || ($ac_flight != '' && func_wildcard_search($ac_flight, $flight_code_array)))) {
				if (!array_key_exists($ac_hex, $alert_trigger_array)) {
					$alert_message_subject = urlencode('### STRAFER-ALERT ### ' . $ac_flight  . ' ' . $ac_hex . ' : ' . $ac_lat . ' ' . $ac_lon . ' : ' . $ac_altitude . 'ft @ ' . date('Y-m-d G:i:s l', $ac_now));
					$alert_message_body = urlencode($ac_flight  . ' ' . $ac_hex . ' : <a href="http://www.google.com/maps/place/' . $ac_lat . ',' . $ac_lon . '/@' . $ac_lat . ',' . $ac_lon . ',12z">' . $ac_lat . ' ' . $ac_lon . '</a> : ' . $ac_altitude . 'ft @ ' . date('Y-m-d G:i:s l', $ac_now));
					$alert_message = 'key=' . $user_set_array['secret_email_key'] . '&subject=' . $alert_message_subject . '&body=' . $alert_message_body;
					if ($ac_hex) {
						$alert_trigger_array[$ac_hex] = time();
						#var_dump($alert_trigger_array); // show array for debug
					}
				}
			}
		} else {
			if ($ac_altitude != '' && $ac_altitude < $user_set_array['alert_max_alt'] && $ac_lat < $user_set_array['alert_max_lat'] && $ac_lat > $user_set_array['alert_min_lat'] && $ac_lon < $user_set_array['alert_max_lon'] && $ac_lon > $user_set_array['alert_min_lon']) {
				if (!array_key_exists($ac_hex, $alert_trigger_array)) {
					$alert_message_subject = urlencode('### STRAFER-ALERT ### ' . $ac_flight  . ' ' . $ac_hex . ' : ' . $ac_lat . ' ' . $ac_lon . ' : ' . $ac_altitude . 'ft @ ' . date('Y-m-d G:i:s l', $ac_now));
					$alert_message_body = urlencode($ac_flight  . ' ' . $ac_hex . ' : <a href="http://www.google.com/maps/place/' . $ac_lat . ',' . $ac_lon . '/@' . $ac_lat . ',' . $ac_lon . ',12z">' . $ac_lat . ' ' . $ac_lon . '</a> : ' . $ac_altitude . 'ft @ ' . date('Y-m-d G:i:s l', $ac_now));
					$alert_message = 'key=' . $user_set_array['secret_email_key'] . '&subject=' . $alert_message_subject . '&body=' . $alert_message_body;
					if ($ac_hex) {
						$alert_trigger_array[$ac_hex] = time();
						#var_dump($alert_trigger_array); // show array for debug
					}
				}
			}
		}

		// delete aircraft after user set seconds from already-message-sent-trigger
		$outdated_entry = time() - $user_set_array['aircraft_suspend_time'];
		foreach ($alert_trigger_array as $key => $value) {
			if ($value < $outdated_entry) {
				unset($alert_trigger_array[$key]);
			}
		}

		// send alert-message, set absolute limit for maximum number of messages and reset alert-message
		if ($alert_message != '' && $sent_alert_messages < $user_set_array['mailer_limit']) {
			if ($user_set_array['gmail']) {
				$email = $user_set_array['email_address'];
				$header  = 'MIME-Version: 1.0' . PHP_EOL;
				$header .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
				$header .= 'From: ' . $user_set_array['email_address'] . PHP_EOL;
				$header .= 'Reply-To: ' . $user_set_array['email_address'] . PHP_EOL;
				$header .= 'X-Mailer: PHP ' . phpversion();
				mail($user_set_array['email_address'], urldecode($alert_message_subject), urldecode($alert_message_body), $header);
			} else {
				file_get_contents($user_set_array['url_mailer'] . '?' . $alert_message);
			}
			$sent_alert_messages++;
			$alert_message = '';
		}

	}

// write selected aircraft data to database
$db = new PDO('mysql:host=' . $user_set_array['db_host'] . ';dbname=' . $user_set_array['db_name'] . '', $user_set_array['db_user'], $user_set_array['db_pass']); $db_insert = '';
if ($sql) { $db->exec($sql); $db->lastInsertId() ? $db_insert = 'inserted' : $db_insert = 'db-error'; }
$db = null;

// generate terminal output and set sleep timer to get minimum a full second until next aircraft.json is ready to get fetched
$runtime = (time() - $start_time);
$runtime_formatted = sprintf('%d days %02d:%02d:%02d', $runtime/60/60/24,($runtime/60/60)%24,($runtime/60)%60,$runtime%60);
($runtime > 0) ? $loop_clock = number_format(round(($i / $runtime),12),12) : $loop_clock = number_format(1, 12);
$process_microtime = (round(1000000 * (microtime(true) - $start_loop_microtime)));
print('process-time(us): ' . sprintf('%07d', $process_microtime) . ' - ' . $loop_clock . ' loops/s avg - since ' . $runtime_formatted . ' - run(s) ' . $i . ' -> ' . sprintf('%03d', $x) . ' dataset(s) => ' . $db_insert . PHP_EOL);
sleep($user_set_array['sleep']);
$i++;

}

?>
