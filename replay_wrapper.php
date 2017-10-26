<?php

// set hex and date you want to replay and set $i to timelapse-factor e.g. 10 is 10-times faster than reality
$user_hex = 'YOUR_HEX';    $date_to_watch = 'YYYY-MM-DD%';    $speed_factor = 10;

$sql = "select * from aircrafts where hex='$user_hex' and message_date like '$date_to_watch' and squawk!='' and flight!='' and lat!='' and lon!='' and nucp!='' and seen_pos!='' and altitude!='' and vert_rate!='' and track!='' and speed!='' and category!='' and messages!='' and seen!='' and rssi!='' order by id asc";
$db = new PDO('mysql:host=127.0.0.1;dbname=adsb', 'root', 'YOUR_PASSWORD');
$stmt = $db->prepare($sql);
$stmt->execute();
$select = $stmt->fetchAll();

$i = 0;

foreach  ($select as $result) {

$aircraft_json = '{ "now" : ' . $result['now'] . ',' . PHP_EOL;
$aircraft_json .= '  "messages" : 60296972,' . PHP_EOL;
$aircraft_json .= '  "aircraft" : [' . PHP_EOL;
$aircraft_json .= '    {"hex":"' . $result['hex'] . '","squawk":"' . $result['squawk'] . '","flight":"' . $result['flight'] . '","lat":' . $result['lat'] . ',"lon":' . $result['lon'] . ',"nucp":' . $result['nucp'] . ',"seen_pos":' . $result['seen_pos'] . ',"altitude":' . $result['altitude'] . ',"vert_rate":' . $result['vert_rate'] . ',"track":' . $result['track'] . ',"speed":' . $result['speed'] . ',"category":"' . $result['category'] . '","mlat":"","tisb":"","messages":' . $result['messages'] . ',"seen":' . $result['seen'] . ',"rssi":' . $result['rssi'] . '}' . PHP_EOL;
$aircraft_json .= '  ]' . PHP_EOL;
$aircraft_json .= '}' . PHP_EOL;
$i++;

if ($i == $speed_factor) { file_put_contents('/YOUR_PATH/data/aircraft.json', $aircraft_json, LOCK_EX); $i = 0; sleep(1); }

}

?>
