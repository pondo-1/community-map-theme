async function fetchJSON(endpoint) {
  const response = await fetch(endpoint);
  return await response.json();
}

async function initializeMapApp() {
  // do if there is map app
  const geojsonEndpoint = "/wp-json/community-map-theme/geojson";
  const infojsonEndpoint = "/wp-json/community-map-theme/infojson";

  const [jsonWithGeocode, infoJson] = await Promise.all([
    fetchJSON(geojsonEndpoint),
    fetchJSON(infojsonEndpoint),
  ]);
  // const infoJson = await fetchJSON(infojsonEndpoint);
  const map = initializeMap(infoJson);
  handleScreenResize(map);
  setupZoomControl(map);
  const mcgLayerSupportGroupAuto = await setupCluster(map);
  const { categoryIconArray, categoryLayergroupArray, groupAll } =
    setupCategories(infoJson);

  // populateMarkersAndList(
  //   jsonWithGeocode,
  //   categoryIconArray,
  //   categoryLayergroupArray,
  //   groupAll,
  //   map
  // );
  populateMarkers(
    jsonWithGeocode,
    categoryIconArray,
    categoryLayergroupArray,
    groupAll
  );

  mcgLayerSupportGroupAuto.checkIn(groupAll);
  groupAll.addTo(map);

  // only for main, Homepage, Filter and Search
  setupSorting();
  setupCategoryFilter(
    mcgLayerSupportGroupAuto,
    groupAll,
    categoryLayergroupArray
  );
  // setupSearch(map, groupAll, saveLayerIdInHtml, buildLink);
  saveLayerIdInHtml(groupAll);
  buildLink(map, groupAll);
  map.invalidateSize(); // Fix Chrome bug
}

function handleScreenResize(map) {
  function myFunction(screenWidth) {
    const position = screenWidth.matches ? "topright" : "bottomright";
    map.attributionControl.setPosition(position);
  }

  const screenWidth = window.matchMedia("(max-width: 980px)");
  myFunction(screenWidth);
}

function setupZoomControl(map) {
  L.control
    .zoom({
      position: "bottomright",
    })
    .addTo(map);
}

async function fetchSVG(url) {
  try {
    const response = await fetch(url);
    return await response.text();
  } catch (error) {
    console.error("Error loading the SVG:", error);
    return ""; // Return empty string or a fallback in case of error
  }
}

async function setupCluster(map) {
  // Preload SVG content
  const svgContent = await fetchSVG(
    "/wp-content/themes/community-map-theme/assets/mapapp/icon-star.svg"
  );

  const mcgLayerSupportGroupAuto = L.markerClusterGroup.layerSupport({
    maxClusterRadius: (mapZoom) => (mapZoom > 15 ? 5 : 40),
    iconCreateFunction: function (cluster) {
      // Custom cluster icon using the preloaded SVG content
      const count = cluster.getChildCount();

      return L.divIcon({
        html: svgContent + `<span class="cluster-count">${count}</span>`,
        className: "custom-cluster-marker custom-cluster",
        iconSize: L.point(60, 60), // size of the icon
      });
    },
  });

  mcgLayerSupportGroupAuto.addTo(map);
  return mcgLayerSupportGroupAuto;
}

function setupCategories(infoJson) {
  const categoryIconArray = {};
  const categoryLayergroupArray = {};
  const groupAll = L.layerGroup();

  Object.entries(infoJson.marker_category).forEach((element) => {
    let name = element[1]["name"];
    let icon_url = element[1]["icon"];
    let slug = element[1]["slug"];
    const optionArray = {
      iconUrl: icon_url,
      iconSize: [40, 40],
    };

    categoryIconArray[name] = L.icon(optionArray);
    categoryLayergroupArray[name] = L.layerGroup();
  });
  return { categoryIconArray, categoryLayergroupArray, groupAll };
}

function populateMarkersAndList(
  jsonWithGeocode,
  categoryIconArray,
  categoryLayergroupArray,
  groupAll,
  map
) {
  jsonWithGeocode.features.forEach((feature) => {
    const category = feature.taxonomy.category.name;
    const categorySlug = feature.taxonomy.category.slug;

    const datenbankList = document.querySelector("#marker_list");

    const marker = createMarker(feature, categoryIconArray[category]);
    marker.addTo(categoryLayergroupArray[category]);
    marker.addTo(groupAll);
  });
}

function populateMarkers(
  jsonWithGeocode,
  categoryIconArray,
  categoryLayergroupArray,
  groupAll
) {
  jsonWithGeocode.features.forEach((feature) => {
    const category = feature.taxonomy.category.name;
    const marker = createMarker(feature, categoryIconArray[category]);

    marker.addTo(categoryLayergroupArray[category]);
    marker.addTo(groupAll);
  });
}

function createMarker(feature, icon) {
  const popupText = `
    ${
      feature.properties.thumbnail_url
        ? `<img src="${feature.properties.thumbnail_url}" alt="${feature.properties.title} thumbnail image" width="50px" height="50px">`
        : ""
    }
    <div class="text_wrapper">
      <div class="popup_title">${feature.properties.name}</div>
      <div class="popupcategory">${feature.taxonomy.category.name}</div>
      <p>${feature.properties.excerpt || ""}</p>
      <a class="popup_button button" href="${
        feature.properties.url
      }">Eintrag ansehen</a>
    </div>`;

  return L.marker(
    [feature.geometry.coordinates[1], feature.geometry.coordinates[0]],
    { icon, name: feature.properties.name, post_id: feature.properties.post_id }
  ).bindPopup(popupText);
}

function saveLayerIdInHtml(markers) {
  markers.eachLayer((marker) => {
    const postId = marker.options.post_id;
    const mapId = markers.getLayerId(marker);
    const element = document.getElementById(`map_id_${postId}`);
    if (element) {
      element.setAttribute("value", mapId);
    }
  });
}

function buildLink(map, markers) {
  document.querySelectorAll(".map_link_point").forEach((el) =>
    el.addEventListener("click", (event) => {
      const mapId = parseInt(event.target.parentNode.getAttribute("value"));
      const marker = markers.getLayer(mapId);

      map.flyTo(marker.getLatLng(), 16);
      map.once("moveend", () => marker.openPopup());

      document
        .querySelectorAll(".map_link_point .button")
        .forEach((btn) => btn.classList.remove("db"));
      event.target.parentNode.querySelector(".button").classList.add("db");

      document
        .querySelectorAll(".marker--entry")
        .forEach((entry) => entry.classList.remove("marked"));
      event.target.parentNode.classList.add("marked");
    })
  );
}

function setupSorting() {
  document
    .getElementById("main_page_list_sort_options")
    .addEventListener("change", (event) => {
      sortList(event.target.value);
    });
}

function sortList(option) {
  const list = document.getElementById("marker_list");
  let switching = true;

  while (switching) {
    switching = false;
    const items = document.getElementsByClassName("marker--entry");

    for (let i = 0; i < items.length - 1; i++) {
      let shouldSwitch = false;
      let check;

      if (option == 0) {
        check =
          new Date(items[i].getAttribute("date")) <
          new Date(items[i + 1].getAttribute("date"));
      } else if (option == 1) {
        check =
          items[i].innerHTML.toLowerCase() >
          items[i + 1].innerHTML.toLowerCase();
      } else if (option == 2) {
        check =
          items[i].getAttribute("author").toLowerCase() >
          items[i + 1].getAttribute("author").toLowerCase();
      }

      if (check) {
        items[i].parentNode.insertBefore(items[i + 1], items[i]);
        switching = true;
        break;
      }
    }
  }
}

function setupCategoryFilter(
  mcgLayerSupportGroupAuto,
  groupAll,
  categoryLayergroupArray
) {
  document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      mcgLayerSupportGroupAuto.removeLayer(groupAll);
      document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
        const targetClass = `category_${checkbox.value}`;
        const currentCategory = document.getElementsByClassName(targetClass);

        if (checkbox.checked) {
          Array.from(currentCategory).forEach(
            (el) => (el.style.display = "block")
          );
          const categoryName = checkbox.getAttribute("category_name");
          const group = categoryLayergroupArray[categoryName];
          mcgLayerSupportGroupAuto.addLayer(group);
        } else {
          Array.from(currentCategory).forEach(
            (el) => (el.style.display = "none")
          );
        }
      });
    });
  });
}

function setupSearch(map, groupAll, saveLayerIdInHtml, buildLink) {
  const searchInput = document.getElementById("map_search_input");

  searchInput.addEventListener("input", (event) => {
    const searchValue = event.target.value.toLowerCase();
    const allMarkers = [];

    groupAll.eachLayer((layer) => {
      const markerTitle = layer.options.name.toLowerCase();
      if (markerTitle.includes(searchValue)) {
        layer.setOpacity(1);
        allMarkers.push(layer);
      } else {
        layer.setOpacity(0);
      }
    });

    if (allMarkers.length === 1) {
      map.flyTo(allMarkers[0].getLatLng(), 16);
      allMarkers[0].openPopup();
    }

    saveLayerIdInHtml(allMarkers);
    buildLink(map, allMarkers);
  });
}

document.addEventListener("DOMContentLoaded", initializeMapApp);
