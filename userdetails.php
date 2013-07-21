<?php
error_reporting(0);
session_start();
 
function user_details() {
$mystate = '1';

if (isset($_SESSION['HTTP_USER_AGENT']))
{
    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
    {
        $mystate = '0';   
    }
}

if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_name'])) 
{
	if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_key'])){
	/* we double check cookie expiry time against stored in database */
	
	$cookie_user_id  = filter($_COOKIE['user_id']);
	$rs_ctime = mysql_query("select `ckey`,`ctime` from `users` where `id` ='$cookie_user_id'") or die(mysql_error());
	list($ckey,$ctime) = mysql_fetch_row($rs_ctime);
	// coookie expiry
	if( (time() - $ctime) > 60*60*24*COOKIE_TIME_OUT) {

		$mystate = '0';
		}
	/* Security check with untrusted cookies - dont trust value stored in cookie. 		
	/* We also do authentication check of the `ckey` stored in cookie matches that stored in database during login*/

	 if( !empty($ckey) && is_numeric($_COOKIE['user_id']) && isUserID($_COOKIE['user_name']) && $_COOKIE['user_key'] == sha1($ckey)  ) {
	 	  //session_regenerate_id();
	
		  $_SESSION['user_id'] = $_COOKIE['user_id'];
		  $_SESSION['user_name'] = $_COOKIE['user_name'];
		/* query user level from database instead of storing in cookies */	
		  list($user_level) = mysql_fetch_row(mysql_query("select user_level from users where id='$_SESSION[user_id]'"));

		  $_SESSION['user_level'] = $user_level;
		  $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
		  
	   } else {
	   $mystate = '0';
	   }

  	} else {
	$mystate = '0';
	}
}
if ($mystate == '0'){
/* User Not Logged in */

	?>
	<div id = "user">
	<div id = "user-inner">
	Don't have an account? <a href="register.php">Join Now!</a> | <a href="#?w=450" rel="popup_login" class="poplight">Login</a>
	</div>
	</div>
  <?php 
}
else{
/* User Logged in */

	?>
	<div id = "user">
	<div id = "user-inner">
	  Logged in as <?php echo $_SESSION['user_name']." |";
	  global $user_id;
	  $user_id = $_SESSION['user_id'];?> 
	<a href="logout.php">Logout</a>
	</div>
  </div>
  <?php
  
echo "<br>";
echo "<a href=\"status.php\" class=\"logged\">My Profile</a>";
echo " | <a href=\"mysettings.php\" class=\"logged\">My Settings</a>";
if(checkAdmin()) {
echo " | <a href=\"admin.php\" class=\"logged\">Admin Panel</a>";
echo " | <a href=\"mailing.php\" class=\"logged\">Mailing list</a>";
}

}


}
?>