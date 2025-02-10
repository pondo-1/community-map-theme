document.addEventListener("DOMContentLoaded", async function () {
  if (document.getElementById("mapapp_map")) {
    await adminMapApp();
  }
});

async function adminMapApp() {
  const map = await initializeMapApp_forAll();

  let saved_lati = document.getElementById("latitude").value;
  let saved_longi = document.getElementById("longitude").value;

  const [admin_map, marker] = adminMapSetting([saved_lati, saved_longi], map);
}

function adminMapSetting([startlat, startlon], map) {
  var marker = L.marker([startlat, startlon], {
    title: "Coordinates",
    alt: "Coordinates",
    draggable: true,
  })
    .addTo(map)
    .bindPopup(
      "Lat " +
        parseFloat(startlat).toFixed(9) +
        "<br />Lon " +
        parseFloat(startlon).toFixed(9)
    )
    .openPopup()
    .on("dragend", function () {
      var lat = marker.getLatLng().lat.toFixed(8);
      var lon = marker.getLatLng().lng.toFixed(8);
      document.getElementById("lat").value = lat;
      document.getElementById("lon").value = lon;
      console.log(lat + "" + startlat);
      marker.bindPopup("Lat " + lat + "<br />Lon " + lon).openPopup();
    });

  map.setView(new L.LatLng(startlat, startlon), 12.5);

  setTimeout(function () {
    map.invalidateSize();
  }, 1000);

  return [map, marker];
}

function save_geocode_metadata() {
  document.getElementById("longitude").value =
    document.getElementById("lon").value;
  document.getElementById("latitude").value =
    document.getElementById("lat").value;
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
      displayAddressResults(myArr);
    }
  };
  xmlhttp.open("GET", url, true);
  xmlhttp.send();
}

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

function displayAddressResults(arr) {
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
