$(document).ready(function () {
  
  $('#startWatering').click(function(){
	showStartWateringBox();
 });

	$('#stopWatering').click(function(){
		stopwatering();
	});
 
  	showWateringCalendar();

  
  
});	

	function stopwatering(){
		console.log( "stopping");
		$.ajax({
                url: "/api/stopWatering.php",
                type: "POST",
				dataType: 'json',
                success: function(data) {
                	//console.log("success");              
					console.log(data);
					//onCancelstartWatering();
					setTimeout(function(){reloadPage()}, 1000);
			},
                error: function(xhr, ajaxOptions, thrownError) {
                	console.log(thrownError);
                }
        });
	}
	
	function showWateringCalendar(){

	
	}
	
	var startWateringBoxVisible = false;
    function showStartWateringBox() {
        	if(!startWateringBoxVisible) {
                    $("body").append('<div class="startWateringBox" id="StartWateringBox">'+
                        '<p>Please select for how long you would like to water your lawn.</p>'+
                        'Duration: <select name="duration" id="duration">'+
						'<option value="10"> 10 minutes </option>'+
						'<option value="20"> 20 minutes </option>'+
						'<option value="30"> 30 minutes </option>'+
						'<option value="40"> 40 minutes </option>'+
						'<option value="50"> 50 minutes </option>'+
						'<option value="60"> 60 minutes </option>'+
						'</select>'+
            '<div id="feedbackResponse" /><br/>'+
                        '<input id="submitFeedbackButton" type="button" onclick="onSubmitstartWatering()" value="start!"/>'+
                        '<input id="cancelFeedbackButton" type="button" onclick="onCancelstartWatering()" value="cancel"/>'+
			'</div>');
				$("#StartWateringBox").fadeIn("slow", function() {
                    startWateringBoxVisible = true;
                });
                }
            }
	function reloadPage(){
		location.reload();
	}	
	function onSubmitstartWatering() {
			var zoneValue = $("#zone").val();
			var durationValue = $("#duration").val();
			console.log(zoneValue);
        	$.ajax({
                url: "/api/startWatering.php",
                type: "POST",
				dataType: 'json',
                data: { duration : durationValue },
                success: function(data) {
                	//console.log("success");              
					console.log(data);
					onCancelstartWatering();
					setTimeout(function(){reloadPage()}, 1000);
			},
                error: function(xhr, ajaxOptions, thrownError) {
                	console.log(thrownError);
                }
        });

                $("#feedback").attr("readonly", "true");
                //$("#submitButton").attr("disabled", "true");
                $("#submitFeedbackButton").remove();
            }

            function onCancelstartWatering() {
                $(".startWateringBox").fadeOut("slow", function() {
                    $(".startWateringBox").remove();
                    startWateringBoxVisible = false;
                });
            } 