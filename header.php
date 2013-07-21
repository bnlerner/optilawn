<?php 
include 'dbc.php';


$err = array();
foreach($_GET as $key => $value) {
	$get[$key] = filter($value); //get variables are filtered.
}
foreach($_POST as $key => $value) {
	$post[$key] = filter($value); //post variables are filtered.
}

if (@$_POST['doLogin']=='Login')
{

	foreach($_POST as $key => $value) {
		$data[$key] = filter($value); // post variables are filtered
	}

	$user_email = $data['usr_email'];
	$pass = $data['pwd'];

	if (strpos($user_email,'@') === false) {
	    $user_cond = "user_name='$user_email'";
	} else {
	      $user_cond = "user_email='$user_email'";
	}
	
	$result = mysql_query("SELECT `id`,`pwd`,`user_name`,`approved`,`user_level` FROM users WHERE 
           $user_cond AND `banned` = '0'") or die ("Couldn't make connection."); 
	$num = mysql_num_rows($result);
 
    if ( $num > 0 ) { 
		list($id,$pwd,$full_name,$approved,$user_level) = mysql_fetch_row($result);
	
		if(!$approved) {
			$err[] = "Account not activated. Please check your email for activation code";
		}
		
		if ($pwd === PwdHash($pass,substr($pwd,0,9))){
			if(empty($err)){
		       session_start();
			   session_regenerate_id (true); //prevent against session fixation attacks.

			   // this sets variables in the session 
				$_SESSION['user_id']= $id;  
				$_SESSION['user_name'] = $full_name;
				$_SESSION['user_level'] = $user_level;
				$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
			
				//update the timestamp and key for cookie
				$stamp = time();
				$ckey = GenKey();
				mysql_query("update users set `ctime`='$stamp', `ckey` = '$ckey' where id='$id'") or die(mysql_error());
				
				//set a cookie 
				
			   if(isset($_POST['remember'])){
						  setcookie("user_id", $_SESSION['user_id'], time()+60*60*24*COOKIE_TIME_OUT, "/");
						  setcookie("user_key", sha1($ckey), time()+60*60*24*COOKIE_TIME_OUT, "/");
						  setcookie("user_name",$_SESSION['user_name'], time()+60*60*24*COOKIE_TIME_OUT, "/");
						  $err[] = "remembered";
						   }
				header("Location: status.php");
		 	}
		}
		else{
			//$msg = urlencode("Invalid Login. Please try again with correct user email and password. ");
			$err[] = "Invalid Login. Please try again with correct user email and password.";
			//header("Location: failed.php?msg=$msg");
		}
	} 
	else {
		$err[] = "Error - Invalid login. No such user exists";
	}		
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="designer" content="Vijay George Richards" />
<title>Opti-Lawn.com  - Home</title>
<link rel="shortcut icon" href="images/favicon.ico">
<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
<link rel="stylesheet" href="validationEngine.jquery.css" type="text/css">
<script type="text/javascript" src="js/jquery.js"></script>
<script src="js/jquery.validationEngine-en.js" type="text/javascript"></script>
<script src="js/jquery.validationEngine.js" type="text/javascript"></script>
</head>

<script src="js/header.js" type="text/javascript"></script>

</head>

	<div id="header">
		<div id="logo" class="logo floatl">
			<a href="index.php"><img src="images/logo.gif" height="77px" width="189px" alt="" title="" border="0" /></a>
		</div>
		<div class="loginbox floatr">
			   <?php 
			   include 'userdetails.php';
			   user_details();
			   ?>
		</div>
		<?php
		include 'menu.php';
		?>
	</div>
	
	<?php
	if(!empty($err)){
		echo "<div class=\"error\">";
		foreach ($err as $e) {
			echo "$e<br>";
		}
		echo "</div>";
	}
	$msg = $_GET["msg"];
	if(!empty($msg)){
		echo "<div class=\"success\">";
			echo $msg;
		echo "</div>";
	}
	?>
	</div>

<div id="popup_login" class="popup_login">
	<form action="index.php" method="post" name="logForm" id="logForm" >
	<p align="center">Login ID or Email address:<br>
	<input name="usr_email" type="text" class="validate[required] text-input" id="usr_email" size="35">
	<br><br>Password:<br>
	<input name="pwd" type="password" class="validate[required] text-input" id="pwd" size="35">
	<br><br>

	<input name="remember" type="checkbox" id="remember" class="remember"> Remember me<br><br>

	<DIV class="buttons">
		<input class="regular button login" name="doLogin" type="submit" id="doLogin" value="Login">
	</div>
	</form>
	<br>
	New to Opti-Lawn.com <a href="register.php">Register</a> | <a href="forgot.php">Forgot Password</a></p>
</div>