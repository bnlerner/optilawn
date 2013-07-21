<?php 
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
header("Location: index.php");
exit();
}

$page_limit = 20; 


$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$login_path = @ereg_replace('admin','',dirname($_SERVER['PHP_SELF']));
$path   = rtrim($login_path, '/\\');

// filter GET values
foreach($_GET as $key => $value) {
	$get[$key] = filter($value);
}

foreach($_POST as $key => $value) {
	$post[$key] = filter($value);
}

if(@$post['doBan'] == 'Ban') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysql_query("update users set banned='1' where id='$id' and `user_name` <> 'admin'");
	}
 }
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
 header("Location: $ret");
 exit();
}

if(@$_POST['doUnban'] == 'Unban') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysql_query("update users set banned='0' where id='$id'");
	}
 }
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
 header("Location: $ret");
 exit();
}

if(@$_POST['doDelete'] == 'Delete') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysql_query("delete from users where id='$id' and `user_name` <> 'admin'");
	}
 }
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
 header("Location: $ret");
 exit();
}

if(@$_POST['doApprove'] == 'Approve') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysql_query("update users set approved='1' where id='$id'");
		
	list($to_email) = mysql_fetch_row(mysql_query("select user_email from users where id='$uid'"));	
 
$message = 
"Hello,\n
Thank you for registering with us. Your account has been activated...\n

*****LOGIN LINK*****\n
http://$host$path/

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";

@mail($to_email, "User Activation", $message,
    "From: \"Member Registration\" <auto-reply@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion()); 
	 
	}
 }
 
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];	 
 header("Location: $ret");
 exit();
}

$rs_all = mysql_query("select count(*) as total_all from users") or die(mysql_error());
$rs_active = mysql_query("select count(*) as total_active from users where approved='1'") or die(mysql_error());
$rs_total_pending = mysql_query("select count(*) as tot from users where approved='0'");						   

list($total_pending) = mysql_fetch_row($rs_total_pending);
list($all) = mysql_fetch_row($rs_all);
list($active) = mysql_fetch_row($rs_active);


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
<link rel="stylesheet" href="validationEngine.jquery.css"
	type="text/css">
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

<head></head>
<body>
<body>
<center>
<div id="main_container">
<div id="header">
<div id="logo" class="logo floatl"><a href="index.php"><img
	src="images/logo.gif" height="77px" width="189px" alt="" title=""
	border="0" /></a></div>
<div class="loginbox floatr"><?php 
			   include 'userdetails.php';
			   user_details();
			   ?>
</div>
<?php
		include 'menu.php';
		?></div>

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
<p align="left">
<h2><font color="#59AC22">Administration Page</font></h2>
</p>

<table width="90%" border="0" cellpadding="5" cellspacing="0"
	class="myaccount">
	<tr>
		<td>Total users: <?php echo $all;?></td>
		<td>Active users: <?php echo $active; ?></td>
		<td>Pending users: <?php echo $total_pending; ?></td>
	</tr>
</table>
<br>
<table width="80%" border="0" align="center" cellpadding="10"
	cellspacing="0"
	style="background-color: #E4F8FA; padding: 2px 5px; border: 1px solid #CAE4FF;">
	<tr>
		<td>
		<form name="form1" method="get" action="admin.php">
		<p align="center">Search <input name="q" type="text" id="q"
			size="40"> <br>
		[Type email or user name]</p>
		<p align="center"><input type="radio" name="qoption"
			value="pending"> Pending users <input type="radio"
			name="qoption" value="recent"> Recently registered <input
			type="radio" name="qoption" value="banned"> Banned users <br>
		<br>
		[You can leave search blank to if you use above options]</p>
		<p align="center"><input name="doSearch" type="submit"
			id="doSearch2" value="Search"></p>
		</form>
		</td>
	</tr>
</table>
<br>

      <p>
        <?php if (@$get['doSearch'] == 'Search') {
	  $cond = '';
	  if(@$get['qoption'] == 'pending') {
	  $cond = "where `approved`='0' order by date desc";
	  }
	  if(@$get['qoption'] == 'recent') {
	  $cond = "order by date desc";
	  }
	  if(@$get['qoption'] == 'banned') {
	  $cond = "where `banned`='1' order by date desc";
	  }
	  
	  if(@$get['q'] == '') { 
	  $sql = "select * from users $cond"; 
	  } 
	  else { 
	  $sql = "select * from users where `user_email` = '$_REQUEST[q]' or `user_name`='$_REQUEST[q]' ";
	  }

	  
	  $rs_total = mysql_query($sql) or die(mysql_error());
	  $total = mysql_num_rows($rs_total);
	  
	  if (!isset($_GET['page']) )
		{ $start=0; } else
		{ $start = ($_GET['page'] - 1) * $page_limit; }
	  
	  $rs_results = mysql_query($sql . " limit $start,$page_limit") or die(mysql_error());
	  $total_pages = ceil($total/$page_limit);
	  
	  ?>
      <p>Approve -&gt; A notification email will be sent to user notifying activation.<br>
        Ban -&gt; No notification email will be sent to the user. 
      <p><strong>*Note: </strong>Once the user is banned, he/she will never be 
        able to register new account with same email address. 
      <p align="right"> 
        <?php 
	  
	  // outputting the pages
		if ($total > $page_limit)
		{
		echo "<div><strong>Pages:</strong> ";
		$i = 0;
		while ($i < $page_limit)
		{
		
		
		$page_no = $i+1;
		$qstr = ereg_replace("&page=[0-9]+","",$_SERVER['QUERY_STRING']);
		echo "<a href=\"admin.php?$qstr&page=$page_no\">$page_no</a> ";
		$i++;
		}
		echo "</div>";
		}  ?>
		</p>
		<form name "searchform" action="admin.php" method="post">
        <table width="95%" border="0" align="center" cellpadding="2" cellspacing="0">
          <tr bgcolor="#E6F3F9">
          	<td width="10px"></td>
            <td width="4%"><strong>ID</strong></td>
            <td width="70px"> <strong>Date</strong></td>
            <td><div align="center"><strong>User Name</strong></div></td>
            <td width="24%"><strong>Email</strong></td>
            <td width="70px"><strong>Approval</strong></td>
            <td width="70px"> <strong>Banned</strong></td>
            <td width="25%">&nbsp;</td>
          </tr>
          <?php while ($rrows = mysql_fetch_array($rs_results)) {?>
          <tr> 
            <td><input name="u[]" type="checkbox" value="<?php echo $rrows['id']; ?>" id="u[]"></td>
            <td><a href="getuser.php?user=<?php echo $rrows['id']; ?>"><?php echo $rrows['id']; ?></a></td>
            <td><?php echo $rrows['date']; ?></td>
            <td> <div align="center"><?php echo $rrows['user_name'];?></div></td>
            <td><?php echo $rrows['user_email']; ?></td>
            <td> <span id="approve<?php echo $rrows['id']; ?>"> 
              <?php if(!$rrows['approved']) { echo "Pending"; } else {echo "Active"; }?>
              </span> </td>
            <td><span id="ban<?php echo $rrows['id']; ?>"> 
              <?php if(!$rrows['banned']) { echo "no"; } else {echo "yes"; }?>
              </span> </td>
            <td> <font size="2"><a href="javascript:void(0);" onclick='$.get("do.php",{ cmd: "approve", id: "<?php echo $rrows['id']; ?>" } ,function(data){ $("#approve<?php echo $rrows['id']; ?>").html(data); });'>Approve</a> 
              <a href="javascript:void(0);" onclick='$.get("do.php",{ cmd: "ban", id: "<?php echo $rrows['id']; ?>" } ,function(data){ $("#ban<?php echo $rrows['id']; ?>").html(data); });'>Ban</a> 
              <a href="javascript:void(0);" onclick='$.get("do.php",{ cmd: "unban", id: "<?php echo $rrows['id']; ?>" } ,function(data){ $("#ban<?php echo $rrows['id']; ?>").html(data); });'>Unban</a> 
              <a href="javascript:void(0);" onclick='$("#edit<?php echo $rrows['id'];?>").show("slow");'>Edit</a> 
              </font> </td>
          </tr>
          <tr> 
            <td colspan="8">
			
			<div style="display:none;font: normal 11px arial; padding:10px; background: #e6f3f9" id="edit<?php echo $rrows['id']; ?>">
			
			<input type="hidden" name="id<?php echo $rrows['id']; ?>" id="id<?php echo $rrows['id']; ?>" value="<?php echo $rrows['id']; ?>">
			User Name: <input name="user_name<?php echo $rrows['id']; ?>" id="user_name<?php echo $rrows['id']; ?>" type="text" size="10" value="<?php echo $rrows['user_name']; ?>" >
			User Email:<input id="user_email<?php echo $rrows['id']; ?>" name="user_email<?php echo $rrows['id']; ?>" type="text" size="20" value="<?php echo $rrows['user_email']; ?>" >
			Level: <input id="user_level<?php echo $rrows['id']; ?>" name="user_level<?php echo $rrows['id']; ?>" type="text" size="5" value="<?php echo $rrows['user_level']; ?>" > 1->user,5->admin
			<br><br>New Password: <input id="pass<?php echo $rrows['id']; ?>" name="pass<?php echo $rrows['id']; ?>" type="text" size="20" value="" > (leave blank)
			<input name="doSave" type="button" id="doSave" value="Save" 
			onclick='$.get("do.php",{ cmd: "edit", pass:$("input#pass<?php echo $rrows['id']; ?>").val(),user_level:$("input#user_level<?php echo $rrows['id']; ?>").val(),user_email:$("input#user_email<?php echo $rrows['id']; ?>").val(),user_name: $("input#user_name<?php echo $rrows['id']; ?>").val(),id: $("input#id<?php echo $rrows['id']; ?>").val() } ,function(data){ $("#msg<?php echo $rrows['id']; ?>").html(data); });'> 
			<a  onclick='$("#edit<?php echo $rrows['id'];?>").hide();' href="javascript:void(0);">close</a>
		 
		  <div style="color:red" id="msg<?php echo $rrows['id']; ?>" name="msg<?php echo $rrows['id']; ?>"></div>
		  </div>
		  
		  </td>
          </tr>
          <?php } ?>
        </table>
	    <p><br>
          <input name="doApprove" type="submit" id="doApprove" value="Approve">
          <input name="doBan" type="submit" id="doBan" value="Ban">
          <input name="doUnban" type="submit" id="doUnban" value="Unban">
          <input name="doDelete" type="submit" id="doDelete" value="Delete">
          <input name="query_str" type="hidden" id="query_str" value="<?php echo $_SERVER['QUERY_STRING']; ?>">
          <strong>Note:</strong> If you delete the user can register again, instead 
          ban the user. </p>
        <p><strong>Edit Users:</strong> To change email, user name or password, 
          you have to delete user first and create new one with same email and 
          user name.</p>
      </form>
	  
	  <?php } ?>
      &nbsp;</p>
	  <?php

	  
if(@$_POST['doSubmit'] == 'Create')
{

foreach($_POST as $key => $value) {
	$data[$key] = filter($value);
}

$rs_dup = mysql_query("select count(*) as total from users where user_name='$data[user_name]' OR user_email='$data[user_email]'") or die(mysql_error());
list($dups) = mysql_fetch_row($rs_dup);

if($dups > 0) {
	echo "<div class=\"error\">The user name or email already exists in the system</div>";
	}
else{
if(!empty($_POST['pwd'])) {
  $pwd = $data['pwd'];	
  $hash = PwdHash($data['pwd']);
 }  
 else
 {
  $pwd = GenPwd();
  $hash = PwdHash($pwd);
  
 }

mysql_query("INSERT INTO users (`user_name`,`user_email`,`pwd`,`approved`,`date`,`user_level`)
			 VALUES ('$data[user_name]','$data[user_email]','$hash','1',now(),'$data[user_level]')
			 ") or die(mysql_error()); 




$message = 
"Thank you for registering with us. Here are your login details...\n
User Email: $post[user_email] \n
Passwd: $pwd \n

*****LOGIN LINK*****\n
http://$host$path/

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";

if($_POST['send'] == '1') {

	mail($post['user_email'], "Login Details", $message,
    "From: \"Member Registration\" <auto-reply@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion()); 
 }
echo "<div class=\"success\">User created with password $pwd....done.</div>"; 
}
}
	  ?>
	  
      <h2><font color="#59AC22">Create New User</font></h2>
      <table width="80%" border="0" cellpadding="5" cellspacing="2" class="myaccount">
        <tr>
          <td><form name="form1" method="post" action="admin.php">
              <p>User ID 
                <input name="user_name" type="text" id="user_name">
                (Type the username)</p>
              <p>Email 
                <input name="user_email" type="text" id="user_email">
              </p>
              <p>User Level 
                <select name="user_level" id="user_level">
                  <option value="1">User</option>
                  <option value="5">Admin</option>
                </select>
              </p>
              <p>Password 
                <input name="pwd" type="text" id="pwd">
                (if empty a password will be auto generated)</p>
              <p> 
                <input name="send" type="checkbox" id="send" value="1" checked>
                Send Email</p>
              <p> 
                <input name="doSubmit" type="submit" id="doSubmit" value="Create">
              </p>
            </form>
            <p>**All created users will be approved by default.</p></td>
        </tr>
      </table>
      
</div>
<br>



<div id="footer">
<div class="fleft"></div>
<div class="fcenter">All rights reserved</div>
<div class="fright"></div>
</div>
</div>

<!-- ------------------------------------------ -->
<div id="popup_login" class="popup_login">
<form action="index.php" method="post" name="logForm" id="logForm">
<p align="center">Login ID or Email address:<br>
<input name="usr_email" type="text"
	class="validate[required] text-input" id="usr_email" size="35">
<br>
<br>
Password:<br>
<input name="pwd" type="password" class="validate[required] text-input"
	id="pwd" size="35"> <br>
<br>

<input name="remember" type="checkbox" id="remember" class="remember"> Remember me<br><br>

<div class="buttons"><input class="regular button login"
	name="doLogin" type="submit" id="doLogin" value="Login"></div>
</p>
</form>
<br>
New to Opti-Lawn.com <a href="#">Register</a> | <a href="forgot.php">Forgot
Password</a>
<p></p>
</div>

<!-- ------------------------------------------ --></center>
</body>
</body>
</html>
