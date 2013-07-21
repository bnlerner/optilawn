<?php

include_once "header.php";
include_once 'dbc.php';

 //if($_SESSION['user_id'] != NULL){
//	echo $_SESSION['user_id'];
 //} else {
 //echo "<div> TEST </div>";
 //}
?>
<script src="js/status.js" type="text/javascript" ></script>
<link rel="stylesheet" type="text/css" href="css/status.css">
<div id="main_container">
	<div id="controlPanel">
	<p> Your sprinklers are currently: 
	<?php
	$deviceQuery = "SELECT `DeviceID` FROM userDevices WHERE UserID = '".$_SESSION['user_name']."'";
	//echo $deviceQuery;
	
	$results = mysql_query($deviceQuery,$link) or die(mysql_error());
	$deviceCode;
	if(mysql_num_rows($results) > 0){
	$row = mysql_fetch_assoc($results);
		//echo($row["DeviceID"]);
		$deviceCode = $row["DeviceID"];
		$_SESSION['DeviceID'] = $deviceCode;
	}
	
//	$deviceCode = (is_null($_GET['deviceCode']) ? 'null' : $_GET['deviceCode']);
	$query = "SELECT * FROM optilawn_main.ZoneWateringSchedule WHERE DeviceID = '".$deviceCode."' AND StartTime < NOW() AND EndTime > NOW()";
	$wateringResults = mysql_query($query, $link) or die(mysql_error());
	//echo $query; //this is here only for testing purposes.
	
	if(mysql_num_rows($wateringResults) > 0){
		$row = mysql_fetch_assoc($wateringResults);
		//var_dump($row);
		echo "ON in Zone ". $row["ZoneNumber"];
		echo '<div id="buttonDiv"><button type="button" id="stopWatering"> Turn Off Sprinklers Now </button></div>';
	}
	else{
		echo "off";
		echo '	<div id="buttonDiv"><button type="button" id="startWatering">Turn On Sprinklers Now</button></div>';
	}
	
	
	//echo ($_SESSION['user_name']);
		//<div id="buttonDiv">
	//<button type="button" id="startWatering">Turn On Sprinklers Now</button>
	//</div>
	?></div></p>
	
	<?php
	$query = "SELECT * FROM optilawn_main.ZoneWateringSchedule WHERE DeviceID = '".$deviceCode."' AND EndTime > NOW() AND EndTime < DATE_ADD(NOW(), INTERVAL 1 WEEK)";
	//echo $query;
	$wateringResults = mysql_query($query, $link) or die(mysql_error());
	if(mysql_num_rows($wateringResults)>0){
		echo "Watering Times for the next week: <br>";
	}
	while($row = mysql_fetch_assoc($wateringResults)){
	//var_dump($row);
		echo "Zone: ". $row["ZoneNumber"]. " at ". $row["StartTime"];
		echo "<br>";
		//ho "list";
	}
	
	?>
	
	
	</div>
</div>

