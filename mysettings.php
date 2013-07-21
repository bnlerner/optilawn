<?php 
include 'dbc.php';
page_protect();

$err = array();
$msg = array();

if(@$_POST['doUpdate'] == 'Update')
{
$rs_pwd = mysql_query("select pwd from users where id='$_SESSION[user_id]'");
list($old) = mysql_fetch_row($rs_pwd);
$old_salt = substr($old,0,9);

	if($old === PwdHash($_POST['pwd_old'],$old_salt))
	{
	$newsha1 = PwdHash($_POST['pwd_new']);
	mysql_query("update users set pwd='$newsha1' where id='$_SESSION[user_id]'");
	$msg[] = "Your new password is updated";
	} else
	{
	 $err[] = "Your old password is invalid";
	}
}

if(@$_POST['doSave'] == 'Save')
{
	foreach($_POST as $key => $value){
		$data[$key] = filter($value);
	}
	
	mysql_query("UPDATE users SET
				`full_name` = '$data[name]',
				`address` = '$data[address]',
				`tel` = '$data[tel]',
				`city` = '$data[city]',
				`country` = '$data[country]',
				`pincode` = '$data[pincode]'
				 WHERE id='$_SESSION[user_id]'
				") or die(mysql_error());
	
	$msg[] = "Profile Sucessfully saved";
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
	$("#pform").validationEngine('attach',{scroll: false});


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

<h2>My Settings</h2>
      <p> 
        <?php	
	if(!empty($err))  {
	   echo "<div class=\"msg\">";
	  foreach ($err as $e) {
	    echo "* Error - $e <br>";
	    }
	  echo "</div>";	
	   }
	   if(!empty($msg))  {
	    echo "<div class=\"success\">" . $msg[0] . "</div>";
	   }
	  ?>
      </p>

     <h3>Change Password</h3>
      <p>If you want to change your password, please input your old and new password 
        to make changes.</p>
      <form name="pform" id="pform" method="post" action="">
        <table width="80%" border="0" align="center" cellpadding="3" cellspacing="3" class="forms">
          <tr> 
            <td width="31%">Old Password</td>
            <td width="69%"><input name="pwd_old" type="password" id="pwd_old" class="validate[required]"></td>
          </tr>
          <tr> 
            <td>New Password</td>
            <td><input name="pwd_new" type="password" id="pwd_new" class="validate[required]"></td>
          </tr>
          <tr> 
            <td>Retype New Password</td>
            <td>
			<input name="pwd_new0" type="password" id="pwd_new0" class="validate[required,equals[pwd_new]]"></td>
          </tr>
        </table>
        <p align="center"> 
          <input name="doUpdate" type="submit" id="doUpdate" value="Update">
        </p>
        <p>&nbsp; </p>
      </form>




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

	<input name="remember" type="checkbox" id="remember" class="remember"> Remember me<br><br>

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
