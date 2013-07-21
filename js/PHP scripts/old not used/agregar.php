<?php
   include("conec.php");
   $link=Conectarse();
$Sql="insert into tablacurso (nombre,direccion,telefono,email,imagen)  values ('".$_POST["nombre"]."','".$_POST["direccion"]."', '".$_POST["telefono"]."', '".$_POST["email"]."', '".$_POST["imagen"]."')";     
   mysql_query($Sql,$link);
   header("Location: insertareg.php");
?>