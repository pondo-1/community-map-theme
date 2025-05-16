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
      new MapApp("mapapp_map", "/wp-json/community-map-theme/geojson", {
        pageType: "home",
        filter: true,
        slider: true,
        editable: false,
      });
    }
  }
  if (pageType === "single-marker") {
    new MapApp("mapapp_map", "/wp-json/community-map-theme/geojson", {
      pageType: "single-marker",
      filter: false,
      slider: false,
      editable: false,
    });
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
