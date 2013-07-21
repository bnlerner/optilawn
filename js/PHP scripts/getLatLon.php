<?php
var geocoder = new google.maps.Geocoder();
var address = document.getElementById("address").value;
geocoder.geocode( { 'address': address}, function(results, status) {
  if (status == google.maps.GeocoderStatus.OK)
  {
      var myLat = results[0].geometry.location.latitude;
      var myLong = results[0].geometry.location.longitude;
	  
      // do something with the geocoded result
      //
	  
  } else {
	  alert("Geocode was not successful for the following reason: " + status);
  }
});



// http://maps.googleapis.com/maps/api/geocode/json?address=104NobleCreekDriveAtlantaGA30327&sensor=true
?>



<!-- 
var geo = new google.maps.Geocoder;
geo.geocode({'address':address},function(results, status){
    if (status == google.maps.GeocoderStatus.OK) {              
        var myLatLng = results[0].geometry.location;

        // Add some code to work with myLatLng              

    } else {
        alert("Geocode was not successful for the following reason: " + status);
    }
});
-->