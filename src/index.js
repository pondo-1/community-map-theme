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
  // Check if we are on the wp-admin edit post page
  const isAdminEditPage =
    document.body.classList.contains("wp-admin") &&
    window.location.pathname.includes("post.php") &&
    new URLSearchParams(window.location.search).get("action") === "edit";

  if (isAdminEditPage) {
    const postId = new URLSearchParams(window.location.search).get("post");
    console.log(`Editing post with ID: ${postId}`);

    // Example: Add custom logic for the admin edit page
    const adminContainer = document.createElement("div");
    adminContainer.id = "custom-admin-message";
    adminContainer.style.margin = "20px 0";
    adminContainer.style.padding = "10px";
    adminContainer.style.backgroundColor = "#f9f9f9";
    adminContainer.style.border = "1px solid #ddd";
    adminContainer.textContent = `You are editing post ID: ${postId}`;

    const adminContent = document.getElementById("post-body-content");
    if (adminContent) {
      adminContent.insertBefore(adminContainer, adminContent.firstChild);
    }

    // Add additional functionality here
    // Example: Initialize a MapApp instance for admin
    new MapApp("mapapp_map", "/wp-json/community-map-theme/geojson", {
      pageType: "wp_admin",
      editable: true,
    });
  }
});
