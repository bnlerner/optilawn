<?php

session_start();
session_regenerate_id (true);
include '../dbc.php';

$test[] = "place";
//echo json_encode($_SESSION['DeviceID']);
//$query = "nothing";
$query = sprintf("SELECT `zones` FROM `DeviceData` WHERE `DeviceID` = '%s'", $_SESSION['DeviceID']);
//echo $query;
$result = mysql_query($query, $link);
$zones = mysql_fetch_assoc($result);
//$zones = "test";

//echo $zones['zones'];
$duration = mysql_real_escape_string($_POST['duration']);

$duration = $duration/$zones['zones'];
$minutes = floor($duration);
$seconds = floor(($duration-$minutes)*60);
//echo $minutes.":".$seconds;

for($i = 0; $i < $zones['zones'];$i++){
	$startInterval = ($minutes*($i)).".".($seconds*($i));
   $duration = ($minutes*($i+1)).".".($seconds*($i+1));
	//echo $duration;
	//echo $startInterval."-".$duration;
   $query = sprintf("INSERT INTO  `ZoneWateringSchedule` (  `DeviceID` ,  `ZoneNumber` ,  `StartTime` ,  `EndTime` ,  `CreatedTime`, `ZoneWateringScheduleID` ) VALUES ( '%s',  '%s',  DATE_ADD(NOW(), INTERVAL %s MINUTE_SECOND), DATE_ADD(NOW(), INTERVAL %s MINUTE_SECOND), CURRENT_TIMESTAMP , NULL );", $_SESSION['DeviceID'], ($i+1) , $startInterval, $duration);
	$result = mysql_query($query,$link);
	//echo $query;
}
$query = sprintf("INSERT INTO  `ZoneWateringSchedule` (  `DeviceID` ,  `ZoneNumber` ,  `StartTime` ,  `EndTime` ,  `CreatedTime`, `ZoneWateringScheduleID` ) VALUES ( '%s',  '%s',  NOW(), DATE_ADD(NOW(), INTERVAL %s MINUTE), CURRENT_TIMESTAMP , NULL );", $_SESSION['DeviceID'], mysql_real_escape_string($_POST['zone']), mysql_real_escape_string($_POST['duration']));
//$query = "SELECT * FROM optilawn_main.ZoneWateringSchedule";
//echo $query;

//$result = mysql_query($query,$link);
//var_dump(mysql_fetch_assoc($result));
//echo "nonsense";
//echo $query;
//var_dump($_POST);
echo json_encode("Success");


?>