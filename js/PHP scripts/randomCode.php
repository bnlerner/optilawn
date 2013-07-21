<?php
$what_the_arduino_reads = '1'.base_convert(rand(10000,9999999), 10, 36);

echo '<'.$what_the_arduino_reads.'>';

$sql_query = "select * from optilawn_main.test_data limit 0, 30";



$results = mysql_query($sql_query,$link) or die(mysql_error());

while($row = mysqli_fetch_assoc($results)) {
	echo $row['TIME'];
	echo $row['pH_value'];
	echo $row['soil_moisture_value'];
	echo $row['device_code'];
}
?>