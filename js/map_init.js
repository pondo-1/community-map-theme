function initializeMap(info_json) {
  // function initializeMap() {
  var main_map_options = {
    center: info_json.map_center,
    zoomSnap: 0.1,
    zoom: 12.5,
    zoomControl: false,
  };

  const map = L.map("main_page_map", main_map_options);
  L.tileLayer(
    "https://api.mapbox.com/styles/v1/{id}/tiles/256/{z}/{x}/{y}?access_token={accessToken}",
    {
      maxZoom: 18,
      minZoom: 11,
      attribution:
        '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a> | © <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://www.mapbox.com/map-feedback/">Improve this map</a>',
      id: "pondelek/cl9fbuboj000e14o2xcxw3oom",
      accessToken:
        "pk.eyJ1IjoicG9uZGVsZWsiLCJhIjoiY2w5Zm1tc3h4MGphODNvbzBkM29jdWRlaCJ9.j64kLJQP_RmwAccN1jGKrw",
    }
  ).addTo(map);

  return map;
}
