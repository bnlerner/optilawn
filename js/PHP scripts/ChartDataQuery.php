<?php
include 'dbc.php';
include 'userdetails.php';


$chart_type = "Watering_Days";//$_GET['chartType'];
$queryuser = "bnlerner";//$_SESSION['user_name'];

switch ($chart_type)
{
case "moisture-pH-perDay": 
	$sql_query = "SELECT 
	DATE(TIME) as DATE, 
	round(avg(ph_value),2) as PH, 
	round(avg(soil_moisture_value),2) as Moisture
	FROM optilawn_main.sensordata 
	WHERE TIME >= CURDATE() - INTERVAL 7 DAY
	AND device_code in (
		select serial FROM optilawn_main.users where user_name = '$queryuser')
		group by DATE(optilawn_main.sensordata.TIME)";
		
		$results = mysql_query($sql_query,$link) or die(mysql_error());

		$data = array();
		while($row = mysql_fetch_array($results)) {
			$data[] = array("DATE" => $row['DATE'], "pH" => $row['PH'], "Moisture" => $row['Moisture']);
		}
	
	break;
	
	case "Watering_Days":
	$sql_query = "SELECT day, StartTime, BaseTimeToWaterPerZone, zone1pct, zone2pct, zone3pct, zone4pct, zone5pct, zone6pct, zone7pct, zone8pct
	FROM optilawn_main.WateringSchedule 
	WHERE day > now() and deviceID in (
		select serial FROM optilawn_main.users where user_name = '$queryuser')
	order by day asc";
		
	$results = mysql_query($sql_query,$link) or die(mysql_error());

	$data = array();
	while($row = mysql_fetch_array($results)) {
			$data[] = array("Day" => $row['day'], "Start Time" => $row['StartTime'], "Base Time to Water" => $row['BaseTimeToWaterPerZone'],
							"Zone1" => $row['zone1pct'], "Zone2" => $row['zone2pct'], "Zone3" => $row['zone3pct'], "Zone4" => $row['zone4pct'],
							"Zone5" => $row['zone5pct'], "Zone6" => $row['zone6pct'], "Zone7" => $row['zone7pct'], "Zone8" => $row['zone8pct']);
		}
	
	break;

default:
	$sql_query = "Select 'Broken'";
	echo "There is an issue with the request";
}

echo json_encode($data);

//echo <br/>;

// from google

//$string = file_get_contents("sampleData.json");
//echo $string;
?>