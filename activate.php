<?php 
include 'dbc.php';

foreach($_GET as $key => $value) {
	$get[$key] = filter($value);
}

/******** EMAIL ACTIVATION LINK**********************/
if(isset($get['user']) && !empty($get['activ_code']) && !empty($get['user']) && is_numeric($get['activ_code']) ) {

$err = array();
$msg = array();

$user = mysql_real_escape_string($get['user']);
$activ = mysql_real_escape_string($get['activ_code']);

//check if activ code and user is valid
$rs_check = mysql_query("select id from users where md5_id='$user' and activation_code='$activ'") or die (mysql_error()); 
$num = mysql_num_rows($rs_check);
  // Match row found with more than 1 results  - the user is authenticated. 
    if ( $num <= 0 ) { 
	$err[] = "Sorry no such account exists or activation code invalid.";
	//header("Location: activate.php?msg=$msg");
	//exit();
	}

if(empty($err)) {
// set the approved field to 1 to activate the account
$rs_activ = mysql_query("update users set approved='1' WHERE 
						 md5_id='$user' AND activation_code = '$activ' ") or die(mysql_error());
$msg1 = "Thank you. Your account has been activated.";
header("Location: index.php?done=1&msg=$msg1");						 
//exit();
 }
}

/******************* ACTIVATION BY FORM**************************/
if (@$_POST['doActivate']=='Activate')
{
$err = array();
$msg = array();

$user_email = mysql_real_escape_string($_POST['user_email']);
$activ = mysql_real_escape_string($_POST['activ_code']);
//check if activ code and user is valid as precaution
$rs_check = mysql_query("select id from users where user_email='$user_email' and activation_code='$activ'") or die (mysql_error()); 
$num = mysql_num_rows($rs_check);
  // Match row found with more than 1 results  - the user is authenticated. 
    if ( $num <= 0 ) { 
	$err[] = "Sorry no such account exists or activation code invalid.";
	//header("Location: activate.php?msg=$msg");
	//exit();
	}
//set approved field to 1 to activate the user
if(empty($err)) {
	$rs_activ = mysql_query("update users set approved='1' WHERE 
						 user_email='$user_email' AND activation_code = '$activ' ") or die(mysql_error());
	$msg[] = "Thank you. Your account has been activated.";
 }
//header("Location: activate.php?msg=$msg");						 
//exit();
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
	?>
	

<div class="mainbody">


<h2>Account Activation</h2>

      <p> 
        <?php
	  /******************** ERROR MESSAGES*************************************************
	  This code is to show error messages 
	  **************************************************************************/
	if(!empty($err))  {
	   echo "<div class=\"msg\">";
	  foreach ($err as $e) {
	    echo "* $e <br>";
	    }
	  echo "</div>";	
	   }
	   if(!empty($msg))  {
	    echo "<div class=\"msg\">" . $msg[0] . "</div>";

	   }	
	  /******************************* END ********************************/	  
	  ?>
      </p>
      <p>Please enter your email and activation code sent to you to your email 
        address to activate your account. Once your account is activated you can 
        <a href="index.php">login here</a>.</p><br>
	 
      <form action="activate.php" method="post" name="actForm" id="actForm" >
        <table width="65%" border="0" cellpadding="4" cellspacing="4" class="loginform">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="36%">Your Email</td>
            <td width="64%"><input name="user_email" type="text" class="required email" id="txtboxn" size="25"></td>
          </tr>
          <tr> 
            <td>Activation code</td>
            <td><input name="activ_code" type="password" class="required" id="txtboxn" size="25"></td>
          </tr>
          <tr> 
            <td colspan="2"> <div align="center"> 
                <p> 
                  <input name="doActivate" type="submit" id="doLogin3" value="Activate">
                </p>
              </div></td>
          </tr>
        </table>
        <div align="center"></div>
        <p align="center">&nbsp; </p>
      </form>
	  
      <p>&nbsp;</p>



</div>




<div id="footer">
	<div class="fleft"></div>
	<div class="fcenter">All rights reserved</div>
	<div class="fright"></div>
</div>

</div>

<!-- ------------------------------------------ -->
<div id="popup_login" class="popup_login">
	<form action="index.php" method="post" name="logForm" id="logForm" >
	<p align="center">Login ID or Email address:<br>
	<input name="usr_email" type="text" class="validate[required] text-input" id="usr_email" size="35">
	<br><br>Password:<br>
	<input name="pwd" type="password" class="validate[required] text-input" id="pwd" size="35">
	<br><br>

	<input name="remember" type="checkbox" id="remember" class="remember" value="1" checked="checked" style="display:none;">

	<DIV class="buttons">
		<input class="regular button login" name="doLogin" type="submit" id="doLogin" value="Login">
	</div>
	</form>
	<br>
	New to Omni-Lawn.com <a href="register.php">Register</a> | <a href="forgot.php">Forgot Password</a></p>
</div>

<!-- ------------------------------------------ -->
</center>
</body></html>