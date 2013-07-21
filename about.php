<?php 
include 'dbc.php';

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

<h2>About Us</h2>

<h4>Background:</h4>
<p>
    In the spring semester at Georgia Tech, five engineers took a 
	management course regarding to Collaborative Product Development. 
	As part of the class project, we had to develop a new concept idea 
	and initiate prototyping. We followed the product development schematic 
	process that lead us with the concept of a smarter way to irrigate the lawn. 
	We initiated product concept formulation and testing with potential customers 
	and after winning the Georgia Chapter Product Development and Management Association 
	contest later that semester, we decided to continue on even after class ended. And now, 
	here we are trying to develop an idea we came up with for a class project. 
	</p>
	<br>
	<p>
	Meet our team of graduate student of the Georgia Institute of Technology:
	</p>
	<br>

<div class="floatl mypic">
<h4>Brian Lerner - Chief Engineer</h4> 
<h3>MS Mechanical Engineer</h3> 
<img border="0" src="images/tm_brian_lerner.jpg" height="209px" width="154px"> 
<br>
<p class="mar10">
</p>
</div>

<div class="floatl mypic">
<h4>Larry Freil - Chief of Technology</h4> 
<h3>Ph.D. Human Centered Computing</h3>
<img border="0" src="images/tm_larry_freil.png" height="209px" width="154px">
<br>
<p class="mar10">
</p>
</div>

<div class="floatl mypic">
<h4>Gretchen Otano - Chief of Finance</h4> 
<h3>MS Chemical and Biomolecular Engineer</h3>
<img border="0" src="images/tm_gretchen_otano.png" height="209px" width="154px">
<br>
<p class="mar10">
</p>
</div>

<div class="floatl mypic">
<h4>Sneha Bishnoi - Chief of Marketing</h4>
<h3>MS Operations Research</h3> 
<img border="0" src="images/tm_sneha_bishnoi.jpg" height="209px" width="154px">
<br>
<p class="mar10">
</p>
</div>
<div class="clear"></div>
<br>


<p>
Maribel Baker started with us in the product development process. 
She step out of the team after her graduation. The team thanks her 
for the contribution she gave during the beginning months. 
</p>
<br>
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
