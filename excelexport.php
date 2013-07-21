<?php

include 'dbc.php';
page_protect();

if(!checkAdmin()) {
header("Location: index.php");
exit();
}


$filename ="excelreport.xls";

$contents = "Date Added \t Email \t Active \t \n";

$rs_settings = mysql_query("select * from newsletter");
while ($row_settings = mysql_fetch_array($rs_settings)) {

$contents = $contents.$row_settings['date']."\t".$row_settings['email']."\t".$row_settings['active']."\n";

}




//$contents = "testdata1 \t testdata2 \t testdata3 \t \n testdata4 \t testdata5 \t testdata6";

header('Content-type: application/ms-excel');
header('Content-Disposition: attachment; filename='.$filename);
echo $contents;
 ?>