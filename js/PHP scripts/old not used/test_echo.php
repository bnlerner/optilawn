<?php

include 'dbc.php';

$soilData = (is_null($_GET['soilMoisture']) ? 'null' : $_GET['soilMoisture']);
$deviceCode = (is_null($_GET['deviceCode']) ? 'null' : $_GET['deviceCode']);
$pH_value = (is_null($_GET['pH']) ? 'null' : $_GET['pH']);

$sql_insert = "INSERT into `test_data`
 			(`TIME`,`pH_value`,`soil_moisture_value`,`device_code`)
		    VALUES
		    (now(),$pH_value,$soilData,$deviceCode)";	
mysql_query($sql_insert,$link) or die(mysql_error());

?>