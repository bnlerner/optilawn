<?php 
include 'dbc.php';
//include 'userdetails.php';
//$queryuser = $_SESSION['user_name'];
//$sql_query1 = "select full_name FROM optilawn_main.users where user_name = '$queryuser'";

//$results1 = mysql_query($sql_query1,$link) or die(mysql_error());
//$querydata = mysql_fetch_array($results1);

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
<script type="text/javascript" src="js/optilawn.js"></script>
<script src="js/jquery.validationEngine-en.js" type="text/javascript"></script>
<script src="js/jquery.validationEngine.js" type="text/javascript"></script>

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
	//$("#logForm").validationEngine('attach',{scroll: false});
	//$("#highchartcontainer").hide();
	
	
	drawDefaultHighchart();
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

<!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        var jsonData = $.ajax({
            url: "getData.php", // "ChartDataQuery.php"
            dataType:"json",
            async: false
            }).responseText;
		var data = new google.visualization.DataTable(jsonData);

        // Set chart options
        var options = {'title':'How Much Pizza I Ate Last Night',
                       'width':800,
                       'height':600,
				   		'legend': 'bottom',};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
		
        //var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        //chart.draw(data, options);
      }
    </script>


	<!--high chart chart -->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script type="text/javascript">
/*$(function () {
        $('#highchartContainer').highcharts({
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: 'Average Monthly Soil Moisture and Rainfall'
            },
            subtitle: {
                text: 'Source: weather.com'
            },
            xAxis: [{
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value}°C',
                    style: {
                        color: '#89A54E'
                    }
                },
                title: {
                    text: 'Temperature',
                    style: {
                        color: '#89A54E'
                    }
                }
            }, { // Secondary yAxis
                title: {
                    text: 'Rainfall',
                    style: {
                        color: '#4572A7'
                    }
                },
                labels: {
                    format: '{value} mm',
                    style: {
                        color: '#4572A7'
                    }
                },
                opposite: true
            }],
            tooltip: {
                shared: true
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                x: 120,
                verticalAlign: 'top',
                y: 100,
                floating: true,
                backgroundColor: '#FFFFFF'
            },
            series: [{
                name: 'Rainfall',
                color: '#4572A7',
                type: 'column',
                yAxis: 1,
                data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
                tooltip: {
                    valueSuffix: ' mm'
                }
    
            }, {
                name: 'Temperature',
                color: '#89A54E',
                type: 'spline',
                data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6],
                tooltip: {
                    valueSuffix: '°C'
                }
            }]
        });
    });
    

		*/</script>

</head>
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

<h2>My Profile</h2>

<h3 class="titlehdr">Welcome <?php echo $_SESSION['user_name'];?> </h3>


<input type="range"  min="0" max="100" />

<!-- high chart script -->
<script src="../../js/highcharts.js"></script>
<script src="../../js/modules/exporting.js"></script>

<div id="controlButtons">
<button onclick="toggleHighchartVisibility()">Hide/Show Higchart</button>
<button onclick="toggleSoilMositure()">Show/Hide Soil Moisture Graph</button>
<button onclick="toggleRainfall()">Show/Hide the Rainfall Graph</button>
<button onclick="getMoisturePH()">Get Moisture</button>
<button onclick="getWateringDays()">Get Watering Days</button>
<button onclick="resetHighchart()">Reset Highchart</button>

</div>
<div id="highchartContainer" style="min-width: 400px; height: 400px; margin: 0 auto;"></div>

<!--Div that will hold the pie chart-->
    <div id="chart_div">
    	
    </div>
	
	<iframe class="youtube-player" type="text/html" width="640" height="385" src="http://www.youtube.com/embed/9bZkp7q19f0" allowfullscreen frameborder="0">
	</iframe>

	</div>
<div id="footer">
	<div class="fleft"></div>
	<div class="fcenter">All rights reserved </div>
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
