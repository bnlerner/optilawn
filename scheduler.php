<?php
include 'dbc.php';
include 'getWeather.php';

// presets
$deviceCode = 234234;
$zipCode = "30126";
$timePerZone = 10;
$TimeToWater = "06:30:00";
$NumberOfZones = 5;

// query database
$weather = getWeather($zipCode);

$sqlDelWeather = "DELETE  FROM `WeatherData` WHERE `date` >= curdate() AND `zipcode` = '$zipCode'";
mysql_query($sqlDelWeather,$link) or die(mysql_error());
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
	$sqlWeather = "INSERT INTO `WeatherData` (`id`, `Date_Time_Stamp`, `zipcode`, `curcloudcover`, `curhumidity`, `curpressure`, `curtemp`, `curweathercode`, `precipitationmm`, `date`, `tempmaxF`, `tempminF`, `weathercode`) VALUES (NULL, CURRENT_TIMESTAMP, $zipCode, NULL, NULL, NULL, NULL, NULL, $precipitation, '$Date', $TempMaxF, $TempMinF, $WCode)";
	mysql_query($sqlWeather,$link) or die(mysql_error());
}


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

// find future rainfall
$SQLRainfall ="select sum(`precipitationmm`) as RAINFALL from `WeatherData`  where `precipitationmm` IS NOT NULL and `zipcode` = $zipCode and `date` >= curdate()";
$SQLResult = mysql_query($SQLRainfall,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$Rainfall = $row['RAINFALL'];

// find past rainfall
$pastdayInterval = 4;
$SQLPastRainfall = "select sum(`precipitationmm`) as RAINFALL from `WeatherData`  where `precipitationmm` IS NOT NULL and `zipcode` = $zipCode and `date` between (curdate() - interval $pastdayInterval day) and curdate()";
$SQLResult = mysql_query($SQLRainfall,$link) or die(mysql_error());
$row = mysql_fetch_array($SQLResult);
$PastRainfall = $row['RAINFALL'];

// need to add duration
$SQLWateredInches = "select sum(abs(TIMESTAMPDIFF(SECOND,`EndTime`,`StartTime`)))/3600 as WATERHOURS from `ZoneWateringSchedule`  where `DeviceID` = 234234 and EndTime between (now() - interval 5 DAY) and now()";


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

$waitDays = 4;
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

?>