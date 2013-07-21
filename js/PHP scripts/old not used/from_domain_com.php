 <?php 
$link = mysql_connect('optilawncom.domaincommysql.com', 'bnlerner', '*password*'); 
if (!$link) { 
    die('Could not connect: ' . mysql_error()); 
} 
echo 'Connected successfully'; 
mysql_select_db(optilawn_main); 
?> 
