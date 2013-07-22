<?php

include_once 'dbc.php';

$deviceCode = (is_null($_GET['deviceCode']) ? 'null' : $_GET['deviceCode']);


//$timezone = date_default_timezone_get();
//echo "The current server timezone is: " . $timezone;

//$date =SELECT * FROM optilawn_main.ZoneWateringSchedule WHERE DeviceID = '234234' AND StartTime < NOW() AND EndTime > NOW() date('m/d/Y h:i:s a', time());
//echo $date;
$sql_query = "SELECT * FROM optilawn_main.ZoneWateringSchedule WHERE  StartTime < NOW() AND EndTime > NOW() and deviceID = $deviceCode";
//echo $sql_query;
$results = mysql_query($sql_query,$link) or die(mysql_error());

if(mysql_num_rows($results) > 0){
	$row = mysql_fetch_array($results);
	$zone = $row['ZoneNumber'];
	echo "{".$zone.",1}";
	$insert = "$zone,1";
	$sql_insertquery = "Insert into Device_Requests (DeviceID,response) Values($deviceCode,'$insert')";
	mysql_query($sql_insertquery,$link) or die(mysql_error());
	}
	else{
		echo "{0,0}"; /// to reset {0,9}
		$insert = "0,0";
		$sql_insertquery = "Insert into Device_Requests (DeviceID,response) Values($deviceCode,'$insert')";
		mysql_query($sql_insertquery,$link) or die(mysql_error());
	}

$final = array();
$num = 1;
while($row = mysql_fetch_array($results)) {
		
	$final[] = array(
		  "Day $num"=> array(
			  "Date" => $row['day'],
 			"Start Time" => $row['StartTime'], 
			"BaseTimeToWaterPerZone" => $row['BaseTimeToWaterPerZone'],
			"zone" => array(
				"z1pct" => $row['zone1pct'],
				"z2pct" => $row['zone1pct'],
				"z3pct" => $row['zone1pct'],
				"z4pct" => $row['zone1pct'],
				"z5pct" => $row['zone1pct'],
				"z6pct" => $row['zone1pct'],
				"z7pct" => $row['zone1pct'],
				"z8pct" => $row['zone1pct'],
				),
			"Device ID" => $row['deviceID'],
			)
		);
		$num++;
}

//echo json_encode($final);
//echo "{2,1}"; // {zone,on/off}








//echo "<br>"; // break in printed echo

/*
$name = "Brian";

$obj = array("Gender" => "male", 
			 "age" => "24"
		 	 //engaged: true,
		     //favorite_tv_shows: ['Lost', 'Dirty Jobs', 'Deadliest Catch', 'Man vs Wild'],
			 //family_members: [
			//	 {name: "Frank", age: 57, relation: "father"},
			//	 {name: "Tina", age: 26, relation: "sister"}
			 //]
		 );
		 
		 $objUser = array("name" => $name);
		 $obj['user'] = $objUser;
		 
		 
		 echo $objUser;
		 
$JsonObj = json_encode($obj);
echo $JsonObj;
//echo myObj.family_members[1].name;
//var propery = "age";
//echo myObj[propery];

//echo $deviceCode;


class User {
public $firstname = "";
public $lastname = "";
public $birthdate = "";
}
 
$user = new User();
$user->firstname = "foo";
$user->lastname = "bar";
 
// Returns: {"firstname":"foo","lastname":"bar"}
json_encode($user);
 
$user->birthdate = new DateTime();
 
/* Returns:
  {
  "firstname":"foo",
  "lastname":"bar",
  "birthdate": {
  "date":"2012-06-06 08:46:58",
  "timezone_type":3,
  "timezone":"Europe\/Berlin"
  }
  }
*//*
echo json_encode($user);






$string = '{"foo": "bar", "cool": "attr"}';
$result = json_decode($string, true);
 
// Result: array(2) { ["foo"]=> string(3) "bar" ["cool"]=> string(4) "attr" }
var_dump($result);
 
// Prints "bar"
echo $result['foo'];
 
// Prints "attr"
echo $result['cool'];
*/

?>
