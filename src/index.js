import "../scss/style.scss";

import MapApp from "./map/MapApp";

document.addEventListener("DOMContentLoaded", () => {
  // Check page type if it is home, post_type = marker, or wp-admin
  let pageType = "unknown";

  if (document.body.classList.contains("home")) {
    pageType = "home";
  } else if (document.body.classList.contains("single-marker")) {
    pageType = "single-marker";
  }

  if (document.getElementById("mapapp_map")) {
    if (pageType === "home") {
      const mapApp = new MapApp(
        "mapapp_map",
        "/wp-json/community-map-theme/geojson",
        {
          pageType: "home",
          filter: true,
          slider: true, // true, get from the addon
          editable: false,
        }
      );
      // Expose the mapApp instance globally
      window.mapApp = mapApp;
      // Listen for the mapReady event
      document.addEventListener("mapReady", () => {
        console.log("Map is ready:", window.mapApp.map);

        fetch(
          "./wp-content/themes/community-map-theme/assets/polygon/ILE-Gebiet-NES.geojson"
        )
          .then((response) => response.json())
          .then((geojsonData) => {
            console.log("GeoJSON data loaded:", geojsonData);
            const multipolygon = L.geoJSON(geojsonData).addTo(
              window.mapApp.map
            );
            window.mapApp.map.fitBounds(multipolygon.getBounds());
            console.log("Multipolygon added and map bounds adjusted.");
          })
          .catch((error) => {
            console.error("Error loading GeoJSON data:", error);
          });
      });
    }
    if (pageType === "single-marker") {
      const mapApp = new MapApp(
        "mapapp_map",
        "/wp-json/community-map-theme/geojson",
        {
          pageType: "single-marker",
          filter: false,
          slider: false,
          editable: false,
        }
      );
      // Expose the mapApp instance globally
      window.mapApp = mapApp;
    }
  }
});
