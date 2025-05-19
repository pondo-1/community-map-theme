import "../scss/style.scss";

import MapApp from "./map/MapApp";

document.addEventListener("DOMContentLoaded", () => {
  // Check page type if it is home, post_type = marker, or wp-admin
  let pageType = "unknown";

  if (document.body.classList.contains("home")) {
    pageType = "home";
  } else if (document.body.classList.contains("single-marker")) {
    pageType = "single-marker";
  } else if (document.body.classList.contains("wp-admin")) {
    pageType = "wp_admin";
  }

  if (document.getElementById("mapapp_map")) {
    if (pageType === "home") {
      const mapApp = new MapApp(
        "mapapp_map",
        "/wp-json/community-map-theme/geojson",
        {
          pageType: "home",
          filter: true,
          slider: true,
          editable: false,
        }
      );
      // Expose the mapApp instance globally
      window.mapApp = mapApp;
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

    // // add multiploygon
    // fetch(
    //   "/wp-content/themes/community-map-theme/assets/polygon/ILE-Gebiet-NES.geojson"
    // )
    //   .then((response) => response.json())
    //   .then((geojsonData) => {
    //     const multipolygon = L.geoJSON(geojsonData).addTo(window.mapApp.map);
    //     window.mapApp.map.fitBounds(multipolygon.getBounds());
    //     console.log("Multipolygon added and map bounds adjusted.");
    //   })
    //   .catch((error) => {
    //     console.error("Error loading GeoJSON data:", error);
    //   });
  }
});
document.addEventListener("DOMContentLoaded", () => {
  const isAdminEditPage =
    document.body.classList.contains("wp-admin") &&
    window.location.pathname.includes("post.php") &&
    new URLSearchParams(window.location.search).get("action") === "edit";

  if (isAdminEditPage && document.body.classList.contains("post-type-marker")) {
    const mapApp = new MapApp("mapapp_map", null, {
      pageType: "wp_admin",
      editable: true,
    });
    // initialize leaflet map for editing here
  }
});
