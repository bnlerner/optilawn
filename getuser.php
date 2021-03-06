<?php 
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
header("Location: index.php");
exit();
}

foreach($_GET as $key => $value) {
	$data[$key] = filter($value);
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
<h2>User Details</h2>

<?php
$user_id = $_GET["user"];

$rs_settings = mysql_query("select * from users where id='$user_id'");

while ($row_settings = mysql_fetch_array($rs_settings)) {
?>

<table>
<tr>
<td width="200px">Name</td><td><?php echo $row_settings['full_name']; ?></td></tr>
<tr><td>Email</td><td><?php echo $row_settings['user_email']; ?></td></tr>
<tr><td>Street</td><td><?php echo $row_settings['address']; ?></td></tr>
<tr><td>City</td><td><?php echo $row_settings['city']; ?></td></tr>
<tr><td>State</td><td><?php echo $row_settings['state']; ?></td></tr>
<tr><td>ZIP</td><td><?php echo $row_settings['zip']; ?></td></tr>
<tr><td>Phone</td><td><?php echo $row_settings['tel']; ?></td></tr>
<tr><td colspan=2></td></tr>
<tr><td>Hardware serial number</td><td><?php echo $row_settings['serial']; ?></td></tr>
<tr><td colspan=2></td></tr>
<tr><td>Any issues with bugs or weeds?</td><td><?php echo $row_settings['issues']; ?></td></tr>
<tr><td>Soil type</td><td><?php echo $row_settings['soil']; ?></td></tr>
<tr><td>Grass type</td><td><?php echo $row_settings['grass']; ?></td></tr>
<tr><td>Current condition</td><td><?php echo $row_settings['condition']; ?></td></tr>
<tr><td>City restrictions on watering</td><td><?php echo $row_settings['waterweek']; ?><br><?php echo $row_settings['waterdays']; ?></td></tr>
<tr><td>sprinkler system layout with zones</td><td><?php echo $row_settings['sprinkler']; ?><br><br>
	<a href="user_images/<?php echo $row_settings['file_name']; ?>" target="_blank"><img src="user_images/<?php echo $row_settings['file_name']; ?>" height="75px" width="100px"></a>
	</td></tr>
<tr><td>Soil moisture sensor placed in</td><td><?php echo $row_settings['zone']; ?></td></tr>
<tr><td>Zone shaded</td><td><?php echo $row_settings['shaded']; ?></td></tr>

</table>

<?php            
}


?>

<br>


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
