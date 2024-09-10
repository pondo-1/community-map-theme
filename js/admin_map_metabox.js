document.addEventListener("DOMContentLoaded", function () {
  // Check if the body element has the specific classes

  adminMapApp();
});

function adminMapApp() {
  const map = initializeMapApp_forAll();
}
function adminMapSetting() {
  var myMarker = L.marker([startlat, startlon], {
    title: "Coordinates",
    alt: "Coordinates",
    draggable: true,
  })
    .addTo(map)
    .on("dragend", function () {
      var lat = myMarker.getLatLng().lat.toFixed(8);
      var lon = myMarker.getLatLng().lng.toFixed(8);
      document.getElementById("lat").value = lat;
      document.getElementById("lon").value = lon;
      myMarker.bindPopup("Lat " + lat + "<br />Lon " + lon).openPopup();
    });

  function chooseAddr(lat1, lng1) {
    myMarker.closePopup();
    map.setView([lat1, lng1], 18);
    myMarker.setLatLng([lat1, lng1]);
    lat = lat1.toFixed(8);
    lon = lng1.toFixed(8);
    document.getElementById("lat").value = lat;
    document.getElementById("lon").value = lon;
    myMarker.bindPopup("Lat " + lat + "<br />Lon " + lon).openPopup();
  }

  function myFunction(arr) {
    var out = "<br />";
    var i;

    if (arr.length > 0) {
      for (i = 0; i < arr.length; i++) {
        out +=
          "<div class='address' title='Show Location and Coordinates' onclick='chooseAddr(" +
          arr[i].lat +
          ", " +
          arr[i].lon +
          ");return false;'>" +
          arr[i].display_name +
          "</div>";
      }
      document.getElementById("results").innerHTML = out;
    } else {
      document.getElementById("results").innerHTML = "Sorry, no results...";
    }
  }

  function addr_search() {
    var inp = document.getElementById("addr");
    var xmlhttp = new XMLHttpRequest();
    var url =
      "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=" +
      inp.value;
    xmlhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        var myArr = JSON.parse(this.responseText);
        myFunction(myArr);
      }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }

  setTimeout(function () {
    map.invalidateSize();
  }, 1000);
}
