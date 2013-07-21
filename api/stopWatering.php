<?php
session_start();
session_regenerate_id (true);
include '../dbc.php';
//echo "test";
$query = "DELETE FROM optilawn_main.ZoneWateringSchedule WHERE DeviceID = '".$_SESSION['DeviceID']."' AND StartTime < NOW() AND EndTime > NOW()";
//echo $query;
mysql_query($query, $link) or die(mysql_error());
$query = "DELETE FROM optilawn_main.ZoneWateringSchedule WHERE DeviceID = '".$_SESSION['DeviceID']."' AND StartTime < (DATE_ADD(NOW(), INTERVAL 2 HOUR)) AND StartTime > NOW()";
//echo $query;
mysql_query($query, $link) or die(mysql_error());
json_encode("deleted");
?>
