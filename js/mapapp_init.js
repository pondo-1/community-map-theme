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
  setupZoomControl(map);
  handleScreenResize(map);
  const mcgLayerSupportGroupAuto = await setupCluster(map);
  const { categoryIconArray, categoryLayergroupArray, groupAll } =
    setupCategories(infoJson);

  const markers = populateMarkers(
    jsonWithGeocode,
    categoryIconArray,
    categoryLayergroupArray,
    groupAll
  );

  mcgLayerSupportGroupAuto.checkIn(groupAll);
  groupAll.addTo(map);

  // only for main, Homepage, Filter and Search
  // setupSorting();
  setupCategoryFilter(
    mcgLayerSupportGroupAuto,
    groupAll,
    categoryLayergroupArray
  );
  // setupSearch(map, groupAll, saveLayerIdInHtml, buildLink);
  saveLayerIdInHtml(groupAll);
  buildLink(map, groupAll);
  map.invalidateSize(); // Fix Chrome bugo

  // Only for home
  var allmarekrs = new L.featureGroup(markers);
  map.fitBounds(allmarekrs.getBounds(), { padding: [50, 50], maxZoom: 14 });

  // only for single-marker
  var current_postid = document.body.getAttribute("data-post-id");
  if (current_postid) {
    let route_json = has_route_json(current_postid, jsonWithGeocode);
    let this_marker = find_marker_by_post_id(map, groupAll, current_postid);

    if (route_json) {
      let drawnroute = L.geoJson(route_json).addTo(map);
      map.fitBounds(drawnroute.getBounds(), { padding: [100, 100] });
    } else {
      zoom_in_to_marker(map, this_marker);
    }
    this_marker.openPopup();
    map.on("zoomend", function (e) {
      // console.log(e.target.getZoom());
    });
  }
}

function has_route_json(current_postid, jsonWithGeocode) {
  jsonWithGeocode.features.forEach((feature) => {
    if (feature.id == current_postid && !(feature.route.length == 0)) {
      let route_json = JSON.parse(decodeURIComponent(feature.route[0]));
      return route_json;
    } else return null;
  });
}

function find_marker_by_post_id(map, markers, post_id) {
  var this_post_marker;
  markers.eachLayer((marker) => {
    if (post_id == marker["options"]["post_id"]) {
      marker["options"]["zIndexOffset"] = 99;
      var map_id = markers.getLayerId(marker);
      var marker = markers.getLayer(map_id);
      let popuptext =
        '<div class="hier_bin_ich"><div class="popup_title">' +
        marker["options"]["name"] +
        "</div></div>";

      let customicon = L.divIcon({
        className: "here-bin-ich",
        iconSize: [60, 60],
        html:
          '<img src="' +
          marker["options"]["icon"]["options"]["iconUrl"] +
          '" style ="filter: drop-shadow(#124054 0px 0px 15px);">',
      });

      let bigIcon = L.icon({
        iconUrl: marker["options"]["icon"]["options"]["iconUrl"],
        iconSize: [60, 60],
      });

      marker.setIcon(customicon);
      //marker.setIcon(bigIcon);
      marker.bindPopup(popuptext);
      this_post_marker = marker;
    }
  });
  return this_post_marker;
}

function zoom_in_to_marker(map, marker) {
  var markerBounds = L.latLngBounds([marker.getLatLng()]);
  map.fitBounds(markerBounds);
  map.setZoom(16);
}

function handleScreenResize(map) {
  function myFunction(screenWidth) {
    const position = screenWidth.matches ? "topright" : "bottomright";
    map.attributionControl.setPosition(position);
  }

  const screenWidth = window.matchMedia("(max-width: 980px)");
  myFunction(screenWidth);
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

function populateMarkers(
  jsonWithGeocode,
  categoryIconArray,
  categoryLayergroupArray,
  groupAll
) {
  const markers = [];
  jsonWithGeocode.features.forEach((feature) => {
    const category = feature.taxonomy.category.name;
    const marker = createMarker(feature, categoryIconArray[category]);

    marker.addTo(categoryLayergroupArray[category]);
    marker.addTo(groupAll);
    markers.push(marker);
  });
  return markers;
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
    [feature.geometry.coordinates[0], feature.geometry.coordinates[1]],
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
          const categoryName = checkbox.getAttribute("category_name");
          const group = categoryLayergroupArray[categoryName];
          mcgLayerSupportGroupAuto.addLayer(group);
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
