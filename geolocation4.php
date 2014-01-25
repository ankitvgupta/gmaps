<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0; position: relative; }

      #map-canvas { height: 100%;}
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDoy4V9-XPLR4x9uLqb6CKpT9VAeBxl-jY&sensor=true">
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places"></script>

    <script type="text/javascript">


traveltype = 'walk';
var markers = [];
google.maps.visualRefresh = true;
type = 'store';
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var userlocation;
var currloc;
var radius;
var rad_override = 0;


function initialize() {
  google.maps.visualRefresh = true;
  directionsDisplay = new google.maps.DirectionsRenderer();
  if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(function(position){
    myLatlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
    currloc = myLatlng; 
    userlocation = myLatlng; //
    var mapOptions = 
      {
        zoom: 17,
        center: myLatlng
      };
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions); 
    directionsDisplay.setMap(map);  
      $( "#walk" ).trigger( "click" );
      $( "#walk1" ).trigger( "click" );
      $('#destinationtype').val("anything");
    });
  }
}

function callback(results, status) {
  if (status == google.maps.places.PlacesServiceStatus.OK) {
    for (var i = 0; i < results.length; i++) {
      createMarker(results[i]);
    }
  }

}

function createMarker(place) {
  var placeLoc = place.geometry.location;
  console.log(place);
  var marker = new google.maps.Marker({
    map: map,
    position: place.geometry.location
  });
  markers.push(marker);

  google.maps.event.addListener(marker, 'click', function() {
    infowindow.setContent("<b>" + place.name + "</b>" + ', ' + place.vicinity + "<br/>" + "Rating: " + place.rating+"/5" + "</br>" + "<a href=# onclick='calcRoute(" + place.geometry.location.d +  ", " + place.geometry.location.e + ");'>Directions to here</a>" );
    infowindow.open(map, this);
  });

}

function setAllMap(map) {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

function changeRadius(newRadius) {
  radius = newRadius * 1000;
  rad_override = 1;
  newRequest();
}

function newRequest(){

  setAllMap(null);
  // Set the default radius based on the mode of transport
  if (rad_override == 0)
  {


  if (traveltype == 'walk'){
    radius = 500;
  }
  if (traveltype == 'bike'){
    radius = 2000;
  }
  if (traveltype == 'drive'){
    radius = 5000;
  }
}
  // Set the default place type
   var request = {
      location: myLatlng,
      radius: radius,
      types: [type],
      rankBy: google.maps.places.RankBy.PROMINENCE
    };

    if(type == ""){
      delete request.types;
    }
    
    infowindow = new google.maps.InfoWindow();
    var service = new google.maps.places.PlacesService(map);
    service.nearbySearch(request, callback);
}

function changeMode(newtype){
  traveltype = newtype;
  $("#walkdistance").hide();
  $("#bikedistance").hide();
  $("#drivedistance").hide();

  if (newtype == "walk"){
    $("#walkdistance").show();
  }
  else if (newtype == "bike"){
    $("#bikedistance").show();
  }
  else if (newtype == "drive"){
    $("#drivedistance").show();
  }
  rad_override = 0;
  newRequest();
}

function gotoCurrentLocation()
{
  currloc = userlocation;
}

function changeCurrentLocation(newLoc){

  currloc = newLoc;
}

function changeDestination(newDest){

  if (newDest == 'anything')
  {
    type = "";
  } 
  else{
    type = newDest;
  }
  newRequest();
}

function calcRoute(input1, input2) {

  // Set the starting and ending location.
  start = currloc;
  var end = "(" + input1 + ", " + input2 + ")";

  // Make the request, and then change it if it is walking or biking instead of driving.
  var request = {
      origin:start,
      destination:end,
      travelMode: google.maps.TravelMode.DRIVING
  };
  if (traveltype == "walk"){
    request.travelMode = google.maps.TravelMode.WALKING;
  }
  else if (traveltype == "bike"){
    request.travelMode = google.maps.TravelMode.BICYCLING;
  }

  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(response);
    }
  });
}

$(document).ready(function () {
      initialize();
      $("#panelselect").submit(function (event) {
        event.preventDefault();
        newRequest();
      });
      //$( "#walk" ).trigger( "click" );

 });
</script>


  </head>
  <body>
  

<div id ="sidediv" style ="position:absolute; z-index: 100;top: 100px;right: 10px;width: 330px; height: 450px; background: white;border-radius: 15px">
  <br/>
  <center>Choose Mode</center>
  <center>
  <div id="selectorbuttons" style="position:relative; margin-left:auto; margin-right: auto; width = 100px; margin-left:auto; margin-right:auto;">
      <button type="button" onclick = "changeMode('walk'); $(this).addClass('active'); $('#bike').removeClass('active'); $('#drive').removeClass('active'); " class="btn btn-default" id= "walk"><img id ="walk" src="walk.png" width="60" height="60" /></button>
      <button type="button" onclick = "changeMode('bike'); $(this).addClass('active'); $('#walk').removeClass('active'); $('#drive').removeClass('active');" class="btn btn-default" id = "bike"><img id ="bike" src="bicycle.png" width="60" height="60"/></button>
      <button type="button" onclick = "changeMode('drive');$(this).addClass('active'); $('#bike').removeClass('active'); $('#walk').removeClass('active');" class="btn btn-default" id = "drive"><img id ="drive" src="car.png" width="60" height="60"/></button>
    </div> 
  </center>
    <br/>
    <center>Choose Starting Location</center>
    <center><div style="position: relative; margin-left:auto; margin-right:auto">
      <button type="button" id = "currentlocationbut" onclick="$(this).addClass('active'); $('#pickownloc').removeClass('active'); $('#locationform').hide(); gotoCurrentLocation();" class="btn btn-default active">Current Location</button>
      <button type="button" id = "pickownloc" onclick="$(this).addClass('active'); $('#currentlocationbut').removeClass('active'); $('#locationform').show(); " class="btn btn-default">Pick a Location</button>
    </div></center>
    <br/>
      <div id = "locationform" style = "position:relative; width: 87%; margin-left: auto; margin-right:auto; display:none">
        <input type="text" onchange = "changeCurrentLocation(this.value);" class="form-control" placeholder="Type address or location name here">
    </div>
    <br/>
    <center>Choose Distance (in miles)</center>
    <center>
    <div id="walkdistance">
      <button type="button" onclick = "changeRadius(.5); $('#customradius').hide(); $(this).addClass('active'); $('#walk2').removeClass('active'); $('#walk3').removeClass('active'); $('#walkcustom').removeClass('active');" class="btn btn-default" id= "walk1">.5</button>
      <button type="button" onclick = "changeRadius(1); $('#customradius').hide(); $(this).addClass('active'); $('#walk1').removeClass('active'); $('#walk3').removeClass('active'); $('#walkcustom').removeClass('active');" class="btn btn-default" id = "walk2">1</button>
      <button type="button" onclick = "changeRadius(1.5); $('#customradius').hide();$(this).addClass('active'); $('#walk2').removeClass('active'); $('#walk1').removeClass('active'); $('#walkcustom').removeClass('active');" class="btn btn-default" id = "walk3">1.5</button>
      <button type="button" onclick = "$('#customradius').show();$(this).addClass('active'); $('#walk2').removeClass('active'); $('#walk3').removeClass('active'); $('#walk1').removeClass('active');" class="btn btn-default" id = "walkcustom">Custom</button>
  </div>

  <div id="bikedistance" style="display:none">
      <button type="button" onclick = "changeRadius(1); $('#customradius').hide(); $(this).addClass('active'); $('#bike2').removeClass('active'); $('#bike3').removeClass('active'); $('#bikecustom').removeClass('active');" class="btn btn-default" id = "bike1">1</button>
      <button type="button" onclick = "changeRadius(2); $('#customradius').hide(), $(this).addClass('active'); $('#bike1').removeClass('active'); $('#bike3').removeClass('active'); $('#bikecustom').removeClass('active');" class="btn btn-default" id = "bike2">2</button>
      <button type="button" onclick = "changeRadius(5); $('#customradius').hide(), $(this).addClass('active'); $('#bike2').removeClass('active'); $('#bike1').removeClass('active'); $('#bikecustom').removeClass('active');" class="btn btn-default" id = "bike3">5</button>
      <button type="button" onclick = "$('#customradius').show(); $(this).addClass('active'); $('#bike2').removeClass('active'); $('#bike3').removeClass('active'); $('#bike1').removeClass('active');" class="btn btn-default"  id="bikecustom">Custom</button>
  </div>
  <div id="drivedistance" style="display:none">
      <button type="button" onclick = "changeRadius(2); $('#customradius').hide(); $(this).addClass('active'); $('#drive2').removeClass('active'); $('#drive3').removeClass('active'); $('#drivecustom').removeClass('active');" class="btn btn-default" id="drive1">2</button>
      <button type="button" onclick = "changeRadius(5); $('#customradius').hide(); $(this).addClass('active'); $('#drive1').removeClass('active'); $('#drive3').removeClass('active'); $('#drivecustom').removeClass('active');" class="btn btn-default" id="drive2">5</button>
      <button type="button" onclick = "changeRadius(10); $('#customradius').hide(); $(this).addClass('active'); $('#drive2').removeClass('active'); $('#drive1').removeClass('active'); $('#drivecustom').removeClass('active');" class="btn btn-default" id="drive3">10</button>
      <button type="button" onclick = "$('#customradius').show(); $(this).addClass('active'); $('#drive2').removeClass('active'); $('#drive3').removeClass('active'); $('#drive1').removeClass('active');" class="btn btn-default" id="drivecustom">Custom</button>
  </div>
  </center>
<br/>
    <div id = "customradius" style = "position:relative; width: 60%; margin-left: auto; margin-right:auto; display:none">
        <input type="text" class="form-control" onchange = "changeRadius(this.value);" placeholder="New distance (in miles)">
</div>
<br/>
<center>Where do you want to go?</center>
<div style="position:relative; ">
  <select id = "destinationtype" onchange = "changeDestination(this.value)" class="form-control input-sm" style = "margin-left: auto; margin-right:auto; width: 200px">
      <option value="anything">Anything</option>
      <option value="food">Food</option>
      <option value="museum">Museums</option>
      <option value="amusement_park">Amusement Park</option>
      <option value="bank">Bank</option>
      <option value="doctor">Doctor</option>
      <option value="gym">Gym</option>
      <option value="library">Library</option>
      <option value="movie_rental">Movie Rental</option>
      <option value="park">Park</option>
      <option value="pharmacy">Pharmacy</option>
      <option value="post_office">Post Office</option>
      <option value="school">School</option>
      <option value="subway_station">Subway Station</option>
    </select>
</div>
</div>
  <!-- <div id ="bottomdiv" style ="position:absolute; z-index: 100;bottom: 100px;left: 200px; width: 500px;height: 100px; background: white;border-radius: 15px">
      TESTESTTEST
  </div> -->
  <div id="map-canvas" style="position: relative"/> 
   
  </body>
</html>