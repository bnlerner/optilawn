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

<script type="text/javascript">
$(window).resize(function() {
	$('#mainbody').css("height", $('#left_content').height()+50);
	$('#left_content').css("width", ($(window).width()-600)/2);
	$('#fright_content').css("width", ($(window).width()-600)/2);
    });

// ------------------
(function ($) {
	$.fn.vAlign = function(container) {
	return this.each(function(i){
	if(container == null) {
	container = 'div';
	}
	$(this).html("<" + container + ">" + $(this).html() + "</" + container + ">");
	var el = $(this).children(container + ":first");
	var elh = $(el).height(); //new element height
	var ph = $(this).height(); //parent height
	var nh = (ph - elh) / 2; //new height to apply
	$(el).css('margin-top', nh);
	
	});
	};
})(jQuery);
// ------------------

$(document).ready(function() {
	$("#logForm").validationEngine('attach',{scroll: false});
	$("#regForm").validationEngine('attach',{scroll: false});
	$('#mainbody').css("height", $('#left_content').height()+50);
	$('#left_content').css("width", ($(window).width()-600)/2);
	$('#fright_content').css("width", ($(window).width()-600)/2);

// ------------------LOGIN POPUP------------------------
$('a.poplight').click(function() {
    var popID = $(this).attr('rel');
    var popURL = $(this).attr('href');
    var query= popURL.split('?');
    var dim= query[1].split('&');
    var popWidth = dim[0].split('=')[1];

    $('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#" class="close"><img src="images/close_pop.png" class="btn_close" title="Close Popup" alt="Close" /></a>');

    var popMargTop = ($('#' + popID).height() + 80) / 2;
    var popMargLeft = ($('#' + popID).width() + 80) / 2;

    $('#' + popID).css({
        'margin-top' : -popMargTop,
        'margin-left' : -popMargLeft
    });

    $('body').append('<div id="fade"></div>');
	
    if($.browser.msie && $.browser.version=="6.0") {
    	$('img.btn_close').css({
        	'float': 'right',
    		'margin': '-20px -20px 0 0'});
    	$('#fade').css("height", $(window).height()*2);
    }
    else{
        $('img.btn_close').css({
        	'float': 'right',
    		'margin': '-55px -55px 0 0'});
    	$('#fade').css("height", '100%');
    };
    $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
    return false;
});
$('a.close').live('click', function() {
    $('#fade , .popup_login').fadeOut(function() {
    	$('#logForm').validationEngine('hideAll');
        $('#fade, a.close').remove();
    });
    return false;
});
// -----------------------------------------------------

});
</script>

</head>
<body>
<body>
<center>
<div id="main_container">
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
	
<div class="mainbody" id="mainbody">

<div id="left_content"><div class="inner_copy"></div>
			<h2>Product Concept</h2>
			<p>
			Our Product will focus on providing a holistic solution to lawn care for the customer. It
			will integrate a physical system with an online interface that will control the watering of the
			users lawn based on parameters such as soil moisture, soil PH level, weather predictions, soil
			type, grass type, city restrictions and personal preferences. The system will feature a box that
			will be internet connected along with a soil moisture sensor that will measure soil moisture at
			depth within the users lawn. The back end will be a cloud based solution which uses analytics to
			predict best times to water. Our value proposition is to improve the users lawn appearance while
			saving water, money and time. The user will receive updates on other things to keep their lawn
			healthy such as when to fertilize, when and how often to spray for bugs and if there are any
			projected problems with their sprinkler system such as loss of pressure.
			</p>
</div>
		

<div id="fright_content">

			<h2>Facts regarding lawn care:</h2>
			<p>
			<li>An American family of four can use 400 gallons of water per day, and about 30 percent of
			that is devoted to outdoor uses. More than half of that outdoor water is used for watering
			lawns and gardens</li>
			<li>Nationwide, landscape irrigation is estimated to account for almost one-third of all
			residential water use, totaling more than 7 billion gallons per day.</li>
			<li>Some experts estimate that up to 50 percent of commercial and residential irrigation
			water use goes to waste due to evaporation, wind, improper system design, or
			overwatering.</li>
			<li>Soil moisture sensors determine the amount of water in the ground available to plants.
			These sensors, when professionally installed and properly maintained, can potentially
			save a household more than 11,000 gallons of water used for irrigation annually.</li>
			</p>

</div>

	
		
<div id="right_content">
			<h2>Current Product</h2>
			<img border="0" src="images/currentproduct.png" height=400 width=500>
		
	<div class="footer-signup">
	<form action="emaillist.php" method="post" name="regForm" id="regForm" >
      <input id="email" type="email" name="email" class="custom[email]" placeholder="newsletter signup">
      <div class="secure-statement">Private, secure,&nbsp;spam-free.</div>
      <input class="button" name="doRegister" type="submit" id="doRegister" value="Register">
    </form>
      <br>
      <br>
      <div class="secure-statement">Follow us:</div>
    </div>
    <div class="social">
      <a href="https://www.facebook.com/pages/Opti-Lawn/647258301954736" target="_blank">
        <div role='img' title="Facebook" class="sprite facebook"></div>
      </a>
      <a href="https://twitter.com/optilawn" target="_blank">
        <div role='img' title="Twitter" class="sprite twitter"></div>
      </a>
      <a href="https://www.pinterest.com/optilawn/" target="_blank">
        <div role='img' title="Pinterest" class="sprite pinterest"></div>
      </a>
      <a href="https://plus.google.com/115444579751887570142" target="_blank" rel="publisher">
        <div role='img' title="g+" class="sprite google-plus"></div>
      </a>
    </div>
    
</div>





</div>




<div id="footer">
	<div class="fleft"></div>
	<div class="fcenter">All rights reserved</div>
	<div class="fright"></div>
</div>

</div>
<center>
<!-- ------------------------------------------ -->
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

<!-- ------------------------------------------ -->
</center>
</body></html>
