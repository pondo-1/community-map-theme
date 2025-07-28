import MapApp from "./map/MapApp";

// Admin-specific initialization
document.addEventListener("DOMContentLoaded", () => {
  console.log("Admin DOM loaded");

  // Check if we're on the correct admin page
  if (
    typeof MapAppData !== "undefined" &&
    MapAppData.isAdmin &&
    MapAppData.postType === "marker"
  ) {
    console.log("Admin marker edit page detected");

    // Wait for the metabox to be fully rendered
    const initAdminMap = () => {
      const mapElement = document.getElementById("mapapp_map");

      if (mapElement) {
        console.log("Map element found, initializing MapApp");

        try {
          const mapApp = new MapApp("mapapp_map", null, {
            pageType: "wp_admin",
            editable: true,
          });

          // Expose globally for debugging
          window.mapApp = mapApp;

          console.log("Admin MapApp initialized successfully");
        } catch (error) {
          console.error("Error initializing admin MapApp:", error);
        }
      } else {
        console.log("Map element not found, retrying...");
        setTimeout(initAdminMap, 200); // Retry every 200ms
      }
    };

    // Start trying to initialize the map
    initAdminMap();
  } else {
    console.log("Not an admin marker page or MapAppData not available");
  }
});

// Also try initialization after a short delay in case DOM is already loaded
setTimeout(() => {
  if (
    typeof MapAppData !== "undefined" &&
    MapAppData.isAdmin &&
    MapAppData.postType === "marker"
  ) {
    const mapElement = document.getElementById("mapapp_map");
    if (mapElement && !window.mapApp) {
      console.log("Fallback initialization");
      try {
        const mapApp = new MapApp("mapapp_map", null, {
          pageType: "wp_admin",
          editable: true,
        });
        window.mapApp = mapApp;
      } catch (error) {
        console.error("Fallback initialization error:", error);
      }
    }
  }
}, 1000);
