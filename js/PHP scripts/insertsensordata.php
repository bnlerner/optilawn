<?php

include 'dbc.php';

$soilData = (is_null($_GET['soilMoisture']) ? 'null' : (float)$_GET['soilMoisture']);
$deviceCode = (is_null($_GET['deviceCode']) ? 'null' : $_GET['deviceCode']);
$pH_value = (is_null($_GET['pH']) ? 'null' : (float)$_GET['pH']);
$device_IP_value = (is_null($_GET['deviceIP']) ? 'null' : $_GET['deviceIP']);

$sql_insert = "INSERT into `sensordata`
 			(`TIME`,`pH_value`,`soil_moisture_value`,`device_code`)
		    VALUES
		    (now(),$pH_value,$soilData,$deviceCode)";	
mysql_query($sql_insert,$link) or die(mysql_error());

$sql_update = "update `users` set lastDeviceIP = '$device_IP_value' WHERE serial = '$deviceCode'";

mysql_query($sql_update,$link) or die(mysql_error());


?>