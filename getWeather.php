<?php

/***
* This function calls the WorldWeatherOnline.com API, and returns an objects that contains the results. The api in this case takes in a string that
* can contain a US Zipcode, UK Postcode, Canada Postalcode, IP address, Latitude/Longitude (decimal degree) or city name. I would recommend using 
* either lat/long, or zip code ideally
*
***/

function getWeather($location){
	//echo "getting weather";
	$ch = curl_init();
	$weatherQuery = "http://api.worldweatheronline.com/free/v1/weather.ashx?q=".$location."&format=json&num_of_days=5&key=eu5bremrqbbr3cvbksaygpzk";
	
	
	curl_setopt($ch, CURLOPT_URL, $weatherQuery);
	//curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	//echo "got here";
	$result = curl_exec($ch);
	//echo  "<br>";
	//echo "did it work?";
	
	curl_close($ch);
	
	//echo $weatherQuery;
	//$weather = file_get_contents($weatherQuery);
	//echo $weather;
	$weather =  json_decode($result);
	//
	
	//echo $weather->weather;
	return $weather;
}

?>
