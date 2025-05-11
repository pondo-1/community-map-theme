import "../scss/style.scss";

import MapApp from "./map/MapApp";
document.addEventListener("DOMContentLoaded", () => {
  // Check if the page is home
  const pageType = document.body.classList.contains('home') ? 'home' : 'other';

  if (document.getElementById("mapapp_map")) {
    if (pageType === "home") {
      new MapApp("mapapp_map", "/wp-json/community-map-theme/geojson", {
        filter: true,
        slider: false,
        editable: false,
      });
    }
  }
  //   if (pageType === 'view-only') {
  //     new MapApp('map', '/wp-content/themes/dein-theme/assets/data/marker-data.json', {
  //       filter: false,
  //       slider: false,
  //       editable: false
  //     });
  //   }

  //   if (pageType === 'edit') {
  //     new MapApp('map', null, {
  //       editable: true
  //     });
  //   }
});
