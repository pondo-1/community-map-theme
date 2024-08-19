async function fetchJSON($endpoint) {
  let url = $endpoint;
  let response = await fetch(url);
  let json = await response.json();
  return json;
}
async function initializeMapApp() {
  // const geojson_endpoint = "/wp-json/ILEK-Map-App/geojson";
  const info_json_endpoint = "/wp-json/community-map-theme/infojson";

  // const json_w_geocode = await fetchGeoJSON(geojson_endpoint);
  const info_json = await fetchJSON(info_json_endpoint);

  // if (!json_w_geocode || !info_json) {
  //   console.error("Failed to load necessary geoJSON data.");
  //   return;
  // }

  // const map = initializeMap();
  const map = initializeMap(info_json);

  // Further map setup continues...
}

initializeMapApp();
