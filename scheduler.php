<?php
include 'dbc.php';
include 'getWeather.php';

// presets
date_default_timezone_set('America/Atlanta');
$deviceCode = 234234;
$zipCode = "30126";
$timePerZone = 10;
$TimeToWater = "06:30:00";
$NumberOfZones = 5;
$waitDays = 5;
$user = "triley10";


// clear future predictions
$sqlDelWeather = "DELETE  FROM `WeatherData` WHERE `zipcode` = '$zipCode' and QueryType = 'P'";
mysql_query($sqlDelWeather,$link) or die(mysql_error());

// query database
$weather = getWeather($zipCode);
//var_dump($weather->data);
/*
foreach($weather->data->current_condition as $curobj) {
	$Curcloudcover = $curobj->cloudcover;
	$CurHumidity = $curobj->humidity;
	$CurPressure = $curobj->pressure;
	$CurTempF = $curobj->temp_F;
	$CurWeatherCode = $curobj->weatherCode;
}
$sqlCurWeather = "INSERT INTO `WeatherData` (`id`, `Date_Time_Stamp`, `zipcode`, `curcloudcover`, `curhumidity`, `curpressure`, `curtemp`, `curweathercode`, `precipitationmm`, `date`, `tempmaxF`, `tempminF`, `weathercode`) VALUES (NULL, CURRENT_TIMESTAMP, $zipCode, $Curcloudcover, $CurHumidity, $CurPressure, $CurTempF, $CurWeatherCode, NULL, CURDATE(), NULL, NULL, NULL)";
mysql_query($sqlCurWeather,$link) or die(mysql_error());
*/

foreach($weather->data->weather as $obj) {
	$Date = $obj->date;
	$precipitation = $obj->precipMM;
	$TempMaxF = $obj->tempMaxF;
	$TempMinF = $obj->tempMinF;
	$WCode = $obj->weatherCode;
	$sqlWeather = "INSERT INTO `WeatherData` (`id`, `Date_Time_Stamp`, `zipcode`,`QueryType`, `curcloudcover`, `curhumidity`, `curpressure`, `curtemp`, `curweathercode`, `precipitationmm`, `precipitationIN`, `date`, `tempmaxF`, `tempminF`, `weathercode`) VALUES (NULL, CURRENT_TIMESTAMP, $zipCode,'P', NULL, NULL, NULL, NULL, NULL, $precipitation,round($precipitation/25.4,2), '$Date', $TempMaxF, $TempMinF, $WCode)";
	mysql_query($sqlWeather,$link) or die(mysql_error());
}


// inserting past data if not currently set. up to 7 days in the past
$curdate = date('Y/m/d', time());
//echo $curdate;
for ($i=1; $i<=7; $i++) {
	$curdate = date('Y/m/d', strtotime($curdate.' - 1 DAY'));
	$day = date('Ymd',strtotime($curdate));
	$date = date('Y-m-d',strtotime($curdate));
	$location = 'Mableton';
	$state = 'Ga';
	
	// check if there is already a record
	$sqlWeatherDataCheck = "Select 1 as RESPONSE From  `WeatherData` where date = '$date' and zipcode = $zipCode and QueryType = 'H' limit 1";
	$SQLResult = mysql_query($sqlWeatherDataCheck,$link) or die(mysql_error());
	$row = mysql_fetch_array($SQLResult);
	$CheckResponse = $row['RESPONSE'];
	
	if(is_null($CheckResponse)) {
		$weather = getPastWeather($location,$state,$day);
		foreach($weather->history->dailysummary as $curobj){
			$rainIN = $curobj->precipi;
			$rainMM = $curobj->precipm; // returning T for some reason
			$rainIN = $rainMM/25.4;
			var_dump($rainMM);
			if (!is_numeric($rainMM)) {
				$rainMM = "NULL";
				$rainIN = "NULL";
			}
			
			$sqlWeather = "INSERT INTO `WeatherData` (`id`, `Date_Time_Stamp`, `zipcode`,`QueryType`, `curcloudcover`, `curhumidity`, `curpressure`, `curtemp`, `curweathercode`, `precipitationmm`,`precipitationIN`, `date`, `tempmaxF`, `tempminF`, `weathercode`) VALUES (NULL, CURRENT_TIMESTAMP, $zipCode,'H', NULL, NULL, NULL, NULL, NULL, $rainMM, $rainIN, '$date', NULL, NULL, NULL)";
			
			mysql_query($sqlWeather,$link) or die(mysql_error());
			//echo $sqlWeather;
			//echo "</br>";
		}
	}
}

/*
// past weather
$day = '20130722';
$date = '2013-07-23';
$location = 'Mableton';
$state = 'Ga';
$weather = getPastWeather($location,$state,$day);
foreach($weather->history->dailysummary as $curobj){
	$rainIN = $curobj->precipi;
	$rainMM = $curobj->precipm;
	$sqlWeather = "INSERT INTO `WeatherData` (`id`, `Date_Time_Stamp`, `zipcode`, `curcloudcover`, `curhumidity`, `curpressure`, `curtemp`, `curweathercode`, `precipitationmm`, `date`, `tempmaxF`, `tempminF`, `weathercode`) VALUES (NULL, CURRENT_TIMESTAMP, $zipCode, NULL, NULL, NULL, NULL, NULL, $rainMM, $date, NULL, NULL, NULL)";
	mysql_query($sqlWeather,$link) or die(mysql_error());
}
*/

// find future rainfall
$SQLRainfall ="select sum(`precipitationmm`) as RAINFALL from `WeatherData`  where `precipitationmm` IS NOT NULL and `zipcode` = $zipCode and `date` >= curdate() and QueryType = 'P'";
$SQLResult = mysql_query($SQLRainfall,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$futRainfall = $row['RAINFALL']; // in mm
$futRainfall = $futRainfall/25.4; // in in

// find past rainfall
$pastdayInterval = 7; // find 1 week prior
$SQLPastRainfall = "select sum(`precipitationmm`) as RAINFALL from `WeatherData`  where `precipitationmm` IS NOT NULL and QueryType = 'H' and `zipcode` = $zipCode and `date` between (curdate() - interval $pastdayInterval day) and curdate()";
$SQLResult = mysql_query($SQLPastRainfall,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$PastRainfall = $row['RAINFALL']; // in mm
$PastRainfall = $PastRainfall/25.4; // in in


/* --------------FIGURE OUT WHAT TO DO WITH THIS ------------------------

// obtain soil type, calculate inches penetrated
$SQLSoilprate = "select (`Water_infiltration_rate_MAX_IN_HR`+`Water_infiltration_rate_MIN_IN_HR`)/2 as AVGRate from `Soil_Penetration_Rate` where `Soil_Type` = (select soil from users where user_name = '$user')";
$SQLResult = mysql_query($SQLSoilprate,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$waterRemain=$row['AVGRate'];

//water needed in inches
$sqlGrassneed= "select ‘WaterNeedPerWeek’ as WaterReqd from ‘Grass’ where ‘grass Type’ = (select grass as GrassType from users where users= '$user')";
$SQLResult = mysql_query($sqlGrassneed,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$waterNeed=$row['WaterReqd'];
$NetWater= $waterNeed-$waterRemain;


*/

// need to add duration finding inches watered
$SQLWateredInches = "select sum(abs(TIMESTAMPDIFF(SECOND,`EndTime`,`StartTime`)))/3600 as WATERHOURS from `ZoneWateringSchedule`  where `DeviceID` = $deviceCode and EndTime between (now() - interval $pastdayInterval DAY) and now()";
$SQLResult = mysql_query($SQLWateredInches,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$PastWater = $row['WATERHOURS']; // in hours watered
// gph average assuming 20 psi pipe pressure 1/2 " diameter tube
$avgGPH =  33.345*60; //gph %older was 33.345
$avgLawnSize =  3000*144; // american average 4900*144; //sq inches
$inchesWatered = ($avgGPH*231*$PastWater)/($avgLawnSize); //watered in inches 

$TotalCurrentGrassWater = $PastRainfall + $inchesWatered;

// find recommended water for grass 

$SQLRecWaterInPerWeek = "select Summer_min_Water_in_month AS WATER from Grass where grassType = (select grass from users where serial = $deviceCode)";
$SQLResult = mysql_query($SQLRecWaterInPerWeek,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$WatPerMonth = $row['WATER']; // in inches per month
$WatPerWeek = $WatPerMonth/4; // per week


// any future watering dates
$SQLFutureSchedule = "select CAST(StartTime AS DATE) as SCHEDULEWATER from `ZoneWateringSchedule`  where `DeviceID` = $deviceCode and StartTime > now() order by StartTime Desc LIMIT 1,1";
$SQLResult = mysql_query($SQLFutureSchedule,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$ScheduleWatering = $row['SCHEDULEWATER'];

// find last time watered
$SQLLastWater ="select CAST(EndTime AS DATE) as LASTDAY from `ZoneWateringSchedule`  where `DeviceID` = $deviceCode and EndTime < now() order by EndTime Desc LIMIT 1,1";
$SQLResult = mysql_query($SQLLastWater,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$LastDayWatered = $row['LASTDAY'];
$XDaysAgo = date( "Y-m-d" ,strtotime(date("Y-m-d ", time()) . " - " . $waitDays . " Day"));

if($TotalCurrentGrassWater <= $WatPerWeek*0.5 && empty($ScheduleWatering)) {
	echo "Lawn water at: ". $TotalCurrentGrassWater. " We will water because recommended inches is ".$WatPerWeek." inches";
	echo "</br>";
	if($WatPerWeek*0.5 > $futRainfall ) {
		echo "Watering lawn future rainfall is ".$futRainfall." which is less than half ".$WatPerWeek." inches";
		echo "</br>";
		
		if($LastDayWatered <= $XDaysAgo) {
			if(date("H:i:s")>$TimeToWater) {
				$WaterTomorrow = date( "Y-m-d H:i:s" ,strtotime(date("Y-m-d ".$TimeToWater, time()) . " + " . 1 . " Day"));
				echo "Watering Tomorrow  " . $WaterTomorrow;
				echo "</br>";
				//WaterLawn
				$currentTime = $WaterTomorrow;
				for ($i = 1; $i <= $NumberOfZones; $i++) {
				$futureTime =  date( "Y-m-d H:i:s" ,strtotime($currentTime . " + " . 60*$timePerZone . " second"));
				$sql_waterQuery = "insert into optilawn_main.ZoneWateringSchedule (DeviceID,ZoneNumber,StartTime,EndTime)	values('234234','$i','$currentTime' 					,'$futureTime');";
		  	  	mysql_query($sql_waterQuery,$link) or die(mysql_error($link));
				$currentTime = $futureTime;
			}
			} else {
				echo "Watering Today  " . date("Y-m-d ".$TimeToWater, time());
				echo "</br>"; 
				//WaterLawn
				$currentTime = date("Y-m-d ".$TimeToWater, time());
				for ($i = 1; $i <= $NumberOfZones; $i++) {
					$futureTime =  date( "Y-m-d H:i:s" ,strtotime($currentTime . " + " . 60*$timePerZone . " second"));
					$sql_waterQuery = "insert into optilawn_main.ZoneWateringSchedule (DeviceID,ZoneNumber,StartTime,EndTime)	values('234234','$i','$currentTime' 					,'$futureTime');";
		  	  	  mysql_query($sql_waterQuery,$link) or die(mysql_error($link));
					$currentTime = $futureTime;
				}
			}
		} else {
			$futureDate = date( "Y-m-d ".$TimeToWater ,strtotime($LastDayWatered . " + " . $waitDays . " Day"));
			echo "Watering in the future to respect ".$waitDays." wait limit. Watering on ".$futureDate;
			$currentTime = $futureDate;
			for ($i = 1; $i <= $NumberOfZones; $i++) {
				$futureTime =  date( "Y-m-d H:i:s" ,strtotime($currentTime . " + " . 60*$timePerZone . " second"));
				$sql_waterQuery = "insert into optilawn_main.ZoneWateringSchedule (DeviceID,ZoneNumber,StartTime,EndTime)	values('234234','$i','$currentTime','$futureTime');";
	  	  		mysql_query($sql_waterQuery,$link) or die(mysql_error($link));
				$currentTime = $futureTime;
		}
		}
	} else {
		//ClearSchedule
		$sqlDelSchedule = "DELETE FROM `ZoneWateringSchedule`  where EndTime > now() and DeviceID = $deviceCode";
		mysql_query($sqlDelSchedule,$link) or die(mysql_error());
		echo "Lawn is not saturated at: ". $TotalCurrentGrassWater ." but rainfall expected in next 5 days. Clearing Schedule and waiting. Future Rainfall: ".$futRainfall . " inches. ";
		echo "</br>";
	}
} else {
	echo "Lawn water at: ". $TotalCurrentGrassWater. " inches which is good for recommended ". $WatPerWeek ." inches per week. Or watering scheduled for this time: ".$ScheduleWatering.". No scheduling is done.";
	echo "</br>";
}


/*
// find last time watered
$SQLLastWater ="select CAST(EndTime AS DATE) as LASTDAY from `ZoneWateringSchedule`  where `DeviceID` = $deviceCode and EndTime < now() order by EndTime Desc LIMIT 1,1";
$SQLResult = mysql_query($SQLLastWater,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$LastDayWatered = $row['LASTDAY'];


// any future watering dates
$SQLFutureSchedule = "select CAST(StartTime AS DATE) as SCHEDULEWATER from `ZoneWateringSchedule`  where `DeviceID` = $deviceCode and StartTime > now() order by StartTime Desc LIMIT 1,1";
$SQLResult = mysql_query($SQLFutureSchedule,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$ScheduleWatering = $row['SCHEDULEWATER'];

$waitDays = 5;
$mmRainWater = 5;
$XDaysAgo = date( "Y-m-d" ,strtotime(date("Y-m-d ", time()) . " - " . $waitDays . " Day"));
// 1 in is 25.4 mm


if($LastDayWatered < $XDaysAgo && empty($ScheduleWatering)) {
	echo "Watered: ". $LastDayWatered. " We will water because its been ".$waitDays." days";
	echo "</br>";
	if($Rainfall <= $mmRainWater ) {
		echo "Watering lawn future rainfall is ".$Rainfall." which is less than ".$mmRainWater." mm";
		echo "</br>";
		
		if(date("H:i:s")>$TimeToWater) {
			$WaterTomorrow = date( "Y-m-d H:i:s" ,strtotime(date("Y-m-d ".$TimeToWater, time()) . " + " . 1 . " Day"));
			echo "Watering Tomorrow  " . $WaterTomorrow;
			echo "</br>";
			//WaterLawn
			$currentTime = $WaterTomorrow;
			for ($i = 1; $i <= $NumberOfZones; $i++) {
				$futureTime =  date( "Y-m-d H:i:s" ,strtotime($currentTime . " + " . 60*$timePerZone . " second"));
				$sql_waterQuery = "insert into optilawn_main.ZoneWateringSchedule (DeviceID,ZoneNumber,StartTime,EndTime)	values('234234','$i','$currentTime' 					,'$futureTime');";
		  	  	mysql_query($sql_waterQuery,$link) or die(mysql_error($link));
				$currentTime = $futureTime;
			}
		} else {
			echo "Watering Today  " . date("Y-m-d ".$TimeToWater, time());
			echo "</br>"; 
			//WaterLawn
			$currentTime = date("Y-m-d ".$TimeToWater, time());
			for ($i = 1; $i <= $NumberOfZones; $i++) {
				$futureTime =  date( "Y-m-d H:i:s" ,strtotime($currentTime . " + " . 60*$timePerZone . " second"));
				$sql_waterQuery = "insert into optilawn_main.ZoneWateringSchedule (DeviceID,ZoneNumber,StartTime,EndTime)	values('234234','$i','$currentTime' 					,'$futureTime');";
		  	  	mysql_query($sql_waterQuery,$link) or die(mysql_error($link));
				$currentTime = $futureTime;
			}
		}
	} else {
		//ClearSchedule
		$sqlDelSchedule = "DELETE FROM `ZoneWateringSchedule`  where EndTime > now() and DeviceID = $deviceCode";
		mysql_query($sqlDelSchedule,$link) or die(mysql_error());
		echo "Watered more than 4 days ago and rainfall expected in next 5 days. Clearing Schedule and waiting. Rainfall: ".$Rainfall . "  Set water limit: " . $mmRainWater;
		echo "</br>";
	}
} else {
	echo "Watered in the last ".$waitDays." days or watering scheduled for ".$ScheduleWatering.". No scheduling is done.";
	echo "</br>";
}

*/
?>