<?php
function Conection(){
	$link=mysql_connect("optilawncom.domaincommysql.com","datamain","r5g>)[!|e@*.ElY");
   if (!$link) {
      die('Could not connect: ' . mysql_error());
   }
   echo 'Connected successfully'; 
	mysql_select_db(optilawn_main); 
   return $link;
}
?>