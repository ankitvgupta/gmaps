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
function initialize() {
  google.maps.visualRefresh = true;
  if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(function(position){
    myLatlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);  //
    var mapOptions = 
      {
        zoom: 17,
        center: myLatlng
      };
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);    

    // Default request
    //newRequest();


    /*
    var request = {
      location: myLatlng,
      radius: 500,
      types: ['store'],
      rankBy: google.maps.places.RankBy.PROMINENCE
    };

    infowindow = new google.maps.InfoWindow();
    var service = new google.maps.places.PlacesService(map);
    service.nearbySearch(request, callback);
    */
  

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
    infowindow.setContent("<b>" + place.name + "</b>" + ', ' + place.vicinity + "<br/>" + "Rating: " + place.rating+"/5");
    infowindow.open(map, this);
  });

}

function setAllMap(map) {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

function newRequest(){

  setAllMap(null);
  // Set the default radius based on the mode of transport
  if (traveltype == 'walk'){
    radius = 500;
  }
  if (traveltype == 'bike'){
    radius = 2000;
  }
  if (traveltype == 'drive'){
    radius = 5000;
  }

  // Set the default place type
  type = 'store';


  //alert(radius);
   var request = {
      location: myLatlng,
      radius: radius,
      types: [type],
      rankBy: google.maps.places.RankBy.PROMINENCE
    };

    infowindow = new google.maps.InfoWindow();
    var service = new google.maps.places.PlacesService(map);
    service.nearbySearch(request, callback);
   

}

function changeMode(newtype){
  //alert(traveltype);
  traveltype = newtype;
  //alert(newtype);
  newRequest();
}

$(document).ready(function () {
      initialize();
      $("#panelselect").submit(function (event) {

        event.preventDefault();
        newRequest();
      });
 });
</script>


  </head>
  <body>
  <div id ="sidediv" style ="position:absolute; z-index: 100;top: 100px;right: 10px;width: 270px;height: 400px; background: white;border-radius: 15px">
  <br/>
  <form id="panelselect">
    <div id="selectorbuttons" style="position: relative; margin-left:40px; width = 100px">
      <button type="button" onclick = "changeMode('walk');" class="btn btn-default" id= "walk">Walk</button>
      <button type="button" onclick = "changeMode('bike');" class="btn btn-default" id = "bike">Bike</button>
      <button type="button" onclick = "changeMode('drive');" class="btn btn-default" id = "drive">Drive</button>
    </div> 
    <br/>
    <select class="form-control" style = "width: 100px; margin-left: 100px">
      <option value="anything">Anything</option>
      <option value="tourist">Tourist Destinations</option>
      <option value="food">Food</option>
      <option value="museums">Museums</option>
    </select>
    <input type="submit" class="btn btn-success" value = "Go!"></input>
  </form>
  </div>
   <div id ="bottomdiv" style ="position:absolute; z-index: 100;bottom: 100px;left: 200px; width: 500px;height: 100px; background: white;border-radius: 15px">
      TESTESTTEST
  </div>
  <div id="map-canvas" style="position: relative"/> 
   
  </body>
</html>