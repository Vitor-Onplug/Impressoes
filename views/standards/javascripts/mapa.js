var geocoder;
var map;
var marker;

function initialize(latitude, longitude) {
  var latlng  = new google.maps.LatLng(latitude, longitude);
  var options = {
    zoom:      16,
    center:    latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

  map = new google.maps.Map(document.getElementById("mapa"), options);

  geocoder = new google.maps.Geocoder();

  marker = new google.maps.Marker({
    map:       map,
    draggable: true,
  });

  marker.setPosition(latlng);
}

function carregarEnderecoMapa(endereco) {
  if (!endereco) {
    endereco = $('#logradouro').val() + ', ' + $('#numero').val() + ' - ' + $('#bairro').val() + ' - ' + $('#cidade').val() + '/' + $('.estado').val() + ' - ' + $('.postal').val() + ' - ' + $('.pais').val();
  }
  geocoder.geocode({'address': endereco + ', Brasil', 'region': 'BR'}, function (results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      if (results[0]) {
        var latitude  = results[0].geometry.location.lat();
        var longitude = results[0].geometry.location.lng();

        $('#latitude').val(latitude);
        $('#longitude').val(longitude);

        var location = new google.maps.LatLng(latitude, longitude);
        marker.setPosition(location);
        map.setCenter(location);
        map.setZoom(16);
      }
    }
  });
}

$(document).ready(function($){
  var latitude  = $('#latitude').val();
  var longitude = $('#longitude').val();

  if (latitude != "" && longitude != "") {
    initialize(latitude, longitude);
  } else {
    initialize(-20.5628854, -48.5755012);
  }

  google.maps.event.addListener(marker, 'drag', function () {
    geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[0]) {
          $('#latitude').val(marker.getPosition().lat());
          $('#longitude').val(marker.getPosition().lng());
        }
      }
    });
  });
});