<?php
include 'dbc.php';
include 'getWeather.php';

// get weather
/*
$weather = getWeather('30126');

$string = '{"foo": "bar", "cool": "attr"}';
$result = json_decode($string);
 
// Result: object(stdClass)#1 (2) { ["foo"]=> string(3) "bar" ["cool"]=> string(4) "attr" }
var_dump($result);
 
// Prints "bar"
echo $result->foo;
 
// Prints "attr"
echo $result->cool;

var_dump($weather);

echo  "<br>";
echo  "<br>";
echo  "<br>";
echo "json data";
echo  $weather->weather ;
*/

$day = '20130722';
$location = 'Mableton';
$state = 'Ga';
$weather = getPastWeather($location,$state,$day);
foreach($weather->history->dailysummary as $curobj){//->history->observations->dailysummary as $curobj) {
	$rainIN = $curobj->precipi;
	$rainMM = $curobj->precipm;
}
echo $rainIN;
echo $rainMM;





/*
$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($json, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
    if(is_array($val)) {
        echo "$key:\n";
    } else {
        echo "$key => $val\n";
    }
}

*/

?>