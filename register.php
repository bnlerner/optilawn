<?php 
include 'dbc.php';

$err = array();
$file_rename = GenPwd().date("YmdHis");
					 
if(@$_POST['doRegister'] == 'Register') 
{ 
/******************* Filtering/Sanitizing Input *****************************
This code filters harmful script code and escapes data of all POST data
from the user submitted form.
*****************************************************************/
foreach($_POST as $key => $value) {
	$data[$key] = filter($value);
}
/************************ SERVER SIDE VALIDATION **************************************/
/********** This validation is useful if javascript is disabled in the browswer ***/

if(empty($data['full_name']) || strlen($data['full_name']) < 4)
{
$err[] = "ERROR - Invalid name. Please enter atleast 3 or more characters for your name";
//header("Location: register.php?msg=$err");
//exit();
}

// Validate User Name
if (!isUserID($data['user_name'])) {
$err[] = "ERROR - Invalid user name. It can contain alphabet, number and underscore.";
//header("Location: register.php?msg=$err");
//exit();
}

// Validate Email
if(!isEmail($data['usr_email'])) {
$err[] = "ERROR - Invalid email address.";
//header("Location: register.php?msg=$err");
//exit();
}
// Check User Passwords
if (!checkPwd($data['pwd'],$data['pwd2'])) {
$err[] = "ERROR - Invalid Password or mismatch. Enter 5 chars or more";
//header("Location: register.php?msg=$err");
//exit();
}
	  
$user_ip = $_SERVER['REMOTE_ADDR'];

// stores sha1 of password
$sha1pass = PwdHash($data['pwd']);

// Automatically collects the hostname or domain  like example.com) 
$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

// Generates activation code simple 4 digit number
$activ_code = rand(1000,9999);

$usr_email = $data['usr_email'];
$user_name = $data['user_name'];

/************ USER EMAIL CHECK ************************************
This code does a second check on the server side if the email already exists. It 
queries the database and if it has any existing email it throws user email already exists
*******************************************************************/

$rs_duplicate = mysql_query("select count(*) as total from users where user_email='$usr_email' OR user_name='$user_name'") or die(mysql_error());
list($total) = mysql_fetch_row($rs_duplicate);

if ($total > 0)
{
$err[] = "ERROR - The username/email already exists. Please try again with different username and email.";
//header("Location: register.php?msg=$err");
//exit();
}
/***************************************************************************/

/***********************/

		$allowedExts = array("gif", "jpeg", "jpg", "png");
		$extension = end(explode(".", $_FILES["file"]["name"]));
		if ((($_FILES["file"]["type"] == "image/gif")
		|| ($_FILES["file"]["type"] == "image/jpeg")
		|| ($_FILES["file"]["type"] == "image/jpg")
		|| ($_FILES["file"]["type"] == "image/png"))
		&& in_array($extension, $allowedExts))
		  {
		  if ($_FILES["file"]["error"] > 0)
		    {
		    //$err[]= "Return Code: " . $_FILES["file"]["error"] . "<br>";
		    }
		  else
		    {
		    //echo "Upload: " . $_FILES["file"]["name"] . "<br>";
		    //echo "Type: " . $_FILES["file"]["type"] . "<br>";
		    //echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
		    //echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
		
		    if (file_exists("user_images/" . $_FILES["file"]["name"]))
		      {
		      $err[]= $_FILES["file"]["name"] . " already exists. ";
		      }
		    else
		      {
		      move_uploaded_file($_FILES["file"]["tmp_name"],
		      "user_images/" . $file_rename.".".$extension);
		      $file_name =$file_rename.".".$extension;
		      
		      }
		    }
		  }
		else
		  {
		  //echo "Invalid file";
		  }

/***********************/
		  

if(empty($err)) {

if(isset($_POST['add_email']) && 
   $_POST['add_email'] == 'Yes') {
		$rs_duplicate = mysql_query("select count(*) as total from `newsletter` where email='$usr_email'") or die();
		list($total) = mysql_fetch_row($rs_duplicate);
		
		if ($total > 0)
		{

		}
		else {
		$sql_insert = "INSERT into `newsletter`
		  			(`email`,`date`,`active`
					)
				    VALUES
				    ('$usr_email',now(),'1')
					";	
		mysql_query($sql_insert,$link) or die();
		}
   }

$water_week='';

foreach($_POST['checkbox'] as $checkbox){
    $water_week =  $water_week . ', '.$checkbox;
}

$sql_insert = "INSERT into `users`
  			(`full_name`,`user_email`,`pwd`,`address`,`tel`,`state`, `zip`,`city`,
  			`date`,`serial`, `users_ip`,`activation_code`,`issues`,`soil`, `user_name`,`grass`,
  			`condition`, `waterweek`, `waterdays`, `sprinkler`, `zone`, `shaded`, `file_name`
			)
		    VALUES
		    ('$data[full_name]','$usr_email','$sha1pass','$data[address]','$data[tel]','$data[state]','$data[zip]','$data[city]'
			,now(),'$data[serial]','$user_ip','$activ_code','$data[issues]','$data[soil]','$user_name','$data[grass]',
			'$data[condition]', '$water_week', '$data[waterdays]', '$data[sprinkler]', '$data[zone]', '$data[shaded]', '$file_name'
			)
			";
			
mysql_query($sql_insert,$link) or die("Insertion Failed:" . mysql_error());
$user_id = mysql_insert_id($link);  
$md5_id = md5($user_id);
mysql_query("update users set md5_id='$md5_id' where id='$user_id'");
//	echo "<h3>Thank You</h3> We received your submission.";

if($user_registration)  {
$a_link = "
*****ACTIVATION LINK*****\n
http://$host$path/activate.php?user=$md5_id&activ_code=$activ_code
"; 
} else {
$a_link = 
"Your account is *PENDING APPROVAL* and will be soon activated the administrator.
";
}

$message = 
"Hello \n
Thank you for registering with us. Here are your login details...\n

User ID: $user_name
Email: $usr_email \n 
Password: $data[pwd] \n

$a_link

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";

	mail($usr_email, "Login Details", $message,
    "From: \"Member Registration\" <auto-reply@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion());

  header("Location: thankyou.php");  
  exit();
	 
	 } 
 }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Designer" content="Vijay George Richards" />
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
	$("#regForm").validationEngine('attach',{scroll: false});
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
    $('#fade , .popup_login, .grass_select').fadeOut(function() {
    	$('#logForm').validationEngine('hideAll');
        $('#fade, a.close').remove();
    });
    return false;
});
// -----------------------------------------------------

$("#grass1").click(function () {
	$("#grass").val("Tall Fescue");
    $('.grass_select').fadeOut(function() {
        $('#fade, a.close').remove();
    });
});
$("#grass2").click(function () {
	$("#grass").val("St. Augustinegrass");
    $('.grass_select').fadeOut(function() {
        $('#fade, a.close').remove();
    });
});
$("#grass3").click(function () {
	$("#grass").val("Floratam");
    $('.grass_select').fadeOut(function() {
        $('#fade, a.close').remove();
    });
});
$("#grass4").click(function () {
	$("#grass").val("Bermuda");
    $('.grass_select').fadeOut(function() {
        $('#fade, a.close').remove();
    });
});
$("#grass5").click(function () {
	$("#grass").val("Carpetgrass");
    $('.grass_select').fadeOut(function() {
        $('#fade, a.close').remove();
    });
});
$("#grass7").click(function () {
	$("#grass").val("Zoysia");
    $('.grass_select').fadeOut(function() {
        $('#fade, a.close').remove();
    });
});
$("#grass8").click(function () {
	$("#grass").val("Kentucky Bluegrass");
    $('.grass_select').fadeOut(function() {
        $('#fade, a.close').remove();
    });
});
$("#grass9").click(function () {
	$("#grass").val("Bahia");
    $('.grass_select').fadeOut(function() {
        $('#fade, a.close').remove();
    });
});

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

<div class="mainbody">

	<?php 
	 if (isset($_GET['done'])) { ?>
	  <h2>Thank you</h2> Your registration is now complete and you can <a href="index.php">login here</a>";
	 <?php exit();
	  }
	?></p>
      <h2>Free Registration / Signup</h2>
      <p>Please register a free account, before you can start posting your ads. 
        Registration is quick and free! Please note that fields marked <span class="required">*</span> 
        are required.</p>

	<?php
	if(!empty($err)){
		echo "<div class=\"error\">";
		foreach ($err as $e) {
			echo "$e<br>";
		}
		echo "</div>";
	}
	?>
 
	  <br>
	  
	  <form id="regForm" method="post" action="register.php" enctype="multipart/form-data">
	  <table width="95%" border="0" cellpadding="3" cellspacing="3">
	  	<tr> 
      	<td width="50%">Name<span class="required"><font color="#CC0000">*</font></span></td>
      	<td>
	  		<input name="full_name" type="text" id="full_name" size="40" class="validate[required]">
	  	</td></tr>
	  	<tr> 
            <td>Your Email<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="usr_email" type="text" id="usr_email" class="validate[required,custom[email]]"> 
              <input type="checkbox" name="add_email" value="Yes">Add to mailing list</td>
        </tr>
        <tr> 
            <td>Street Address<span class="required"><font color="#CC0000">*</font></span> </td>
            <td> 
              <input name="address" type="text" id="address" size="40" class="validate[required]"></td>
        </tr>
          <tr> 
            <td>City<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="city" type="text" id="city" class="validate[required]"></td>
          </tr>
          <tr> 
            <td>State<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="state" type="text" id="state" class="validate[required]"></td>
          </tr>
          <tr> 
            <td>Zip<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="zip" type="text" id="zip" class="validate[required]"></td>
          </tr>
          <tr>
            <td>Phone<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="tel" type="text" id="tel" class="validate[required]"></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr> 	
          <tr> 
            <td>Enter your hardware serial number<font color="#CC0000">*</font></td>
            <td><input name="serial" type="text" id="serial" class="validate[required]">
			</td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2"><strong>Can you please answer some questions about lawn?</strong></td>
          </tr>	  	
          <tr> 
            <td>Does your lawn have any issues with bugs or weeds?</td>
            <td><select name="issues" class="validate[required]" id="issues">
                <option value="" selected></option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                <option value="Not sure">Not sure</option>
               	</select>
              </td>
          </tr>
          <tr> 
            <td>Soil type</td>
            <td><select name="soil" class="validate[required]" id="soil">
                <option value="" selected></option>
                <option value="Clay soil">Clay soil</option>
                <option value="Silty soil">Silty soil</option>
                <option value="Sandy soil">Sandy soil</option>
                <option value="Loamy soil">Loamy soil</option>
                <option value="Not sure">Not sure</option>
               	</select>
              </td>
          </tr>
          <tr> 
            <td>Grass type</td>
            <td><select name="grass" class="validate[required]" id="grass">
                <option value="" selected></option>
                <option value="Tall Fescue">Tall Fescue</option>
                <option value="St. Augustinegrass">St. Augustinegrass</option>
                <option value="Floratam">Floratam</option>
                <option value="Bermuda">Bermuda</option>
                <option value="Carpetgrass">Carpetgrass</option>
                <option value="Zoysia">Zoysia</option>
                <option value="Kentucky Bluegrass">Kentucky Bluegrass</option>
                <option value="Bahia">Bahia</option>
                <option value="Not sure">Not sure</option>
               	</select>
               	<a href="#?w=850" rel="grass_select" class="poplight">Help me select</a>
              </td>
          </tr>
          <tr> 
            <td>Current condition</td>
            <td><select name="condition" class="validate[required]" id="condition">
                <option value="" selected></option>
                <option value="Pristine">Pristine</option>
                <option value="Great">Great</option>
                <option value="Good">Good</option>
                <option value="Some brown spots">Some brown spots</option>
               	</select>
              </td>
          </tr>
          <tr> 
            <td>City restrictions on watering</td>
            <td>
            Choose certain days to water:<br>
            <input type="checkbox" name="checkbox[]" value="Mon">Mon
            <input type="checkbox" name="checkbox[]" value="Tue">Tue
            <input type="checkbox" name="checkbox[]" value="Wed">Wed
            <input type="checkbox" name="checkbox[]" value="Thu">Thu
            <input type="checkbox" name="checkbox[]" value="Fri">Fri
            <input type="checkbox" name="checkbox[]" value="Sat">Sat
            <input type="checkbox" name="checkbox[]" value="Sun">Sun
            <br><br>
            Number of days per week to water:<br>
            <select name="waterdays" class="" id="waterdays">
                <option value="" selected></option>
                <option value="1 day">1 day</option>
                <option value="2 days">2 days</option>
                <option value="3 days">3 days</option>
                <option value="4 days">4 days</option>
                <option value="5 days">5 days</option>
                <option value="6 days">6 days</option>
                <option value="7 days">7 days</option>
            </select>
			</td>
          </tr>
          <tr> 
            <td>Enter sprinkler system layout with zones</td>
            <td><input name="sprinkler" type="text" id="sprinkler" class=""><br>
            <input type="file" name="file" id="file" >
			<input type="submit" name="upload" value="Upload">
			</td>
          </tr>
          <tr> 
            <td>What zone is the soil moisture sensor placed in?</td>
            <td><select name="zone" class="" id="zone">
                <option value="" selected></option>
                <option value="Zone 1">Zone 1</option>
                <option value="Zone 2">Zone 2</option>
                <option value="Zone 3">Zone 3</option>
                <option value="Zone 4">Zone 4</option>
                <option value="Zone 5">Zone 5</option>
                <option value="Zone 6">Zone 6</option>
                <option value="Zone 7">Zone 7</option>
				<option value="Zone 8">Zone 8</option>
               	</select>
              </td>
          </tr>
          <tr> 
            <td>Is this zone shaded.</td>
            <td>
	            <input type="radio" value="Yes" checked name="shaded"> Yes
	            <input type="radio" value="No" name="shaded"> No
            </td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2"><strong>Login Details</strong></td>
          </tr>
         <tr> 
            <td>Username<span class="required"><font color="#CC0000">*</font></span></td>
            <td><input name="user_name" type="text" id="user_name" class="validate[required]" minlength="5" > 
              <input name="btnAvailable" type="button" id="btnAvailable" 
              onclick='$("#checkid").html("<img border=\"0\" src=\"images/loading.gif\"> Please wait..."); $.get("checkuser.php",{ cmd: "check", user: $("#user_name").val() } ,function(data){  $("#checkid").html(data); });'
              value="Check Availability"> 
			  <span style="color:red; font: bold 12px verdana; " id="checkid" ></span> 
            </td>
          </tr>
          <tr>
            <td>Password<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="pwd" type="password" class="validate[required]" minlength="5" id="pwd"> 
              <span class="example">** 5 chars minimum..</span></td>
          </tr>
          <tr>
            <td>Retype Password<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="pwd2"  id="pwd2" class="validate[required,equals[pwd]]" type="password" minlength="5" equalto="#pwd"></td>
          </tr>
	  </table>
	  <br>
	  <input class="regular button login" name="doRegister" type="submit" id="doRegister" value="Register">
	  <br><br>
	  </form>

</div>




<div id="footer">
	<div class="fleft"></div>
	<div class="fcenter">All rights reserved</div>
	<div class="fright"></div>
</div>

</div>


<!-- ------------------------------------------ -->

<div id="grass_select" class="grass_select">
	<div>
		<div class="floatl pad5">
			TallFescue<br>
			<img border="0" src="images/img_TallFescue.jpg" width="200" height="141"><br>
			<input type='button' value='Select' id='grass1'>
		</div>
		<div class="floatl pad5">
			St. Augustinegrass<br>
			<img border="0" src="images/img_Bermuda_StAug.jpg" width="200" height="141"><br>
			<input type='button' value='Select' id='grass2'>
		</div>
		<div class="floatl pad5">
			Floratam<br>
			<img border="0" src="images/img_StAug_Floratam.jpg" width="200" height="141"><br>
			<input type='button' value='Select' id='grass3'>
		</div>
		<div class="floatl pad5">
			Bermuda<br>
			<img border="0" src="images/img_Bermuda.jpg" width="200" height="141"><br>
			<input type='button' value='Select' id='grass4'>
		</div>
		<div class="floatl pad5">
			Carpetgrass<br>
			<img border="0" src="images/img_carpetgrass.jpg" width="200" height="141"><br>
			<input type='button' value='Select' id='grass5'>
		</div>
		<div class="floatl pad5">
			Zoysia<br>
			<img border="0" src="images/img_Zoysia_03.jpg" width="200" height="141"><br>
			<input type='button' value='Select' id='grass7'>
		</div>
		<div class="floatl pad5">
			Kentucky Bluegrass<br>
			<img border="0" src="images/img_KentuckyBluegrass.jpg" width="200" height="141"><br>
			<input type='button' value='Select' id='grass8'>
		</div>
		<div class="floatl pad5">
			Bahia<br>
			<img border="0" src="images/img_Bahia.jpg" width="200" height="141"><br>
			<input type='button' value='Select' id='grass9'>
		</div>
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
	New to Opti-Lawn.com <a href="register.php">Register</a> | <a href="forgot.php">Forgot Password</a></p>
</div>

<!-- ------------------------------------------ -->
</center>
</body></html>
