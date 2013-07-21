<?php

include 'getWeather.php';


echo "test.php";

$forecast = getWeather("30350");

echo var_dump($forecast);

?>