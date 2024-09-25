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
