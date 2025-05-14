// MapApp.js
import L from "leaflet";
// Corresponding CSS is added in leaflet,php
import "leaflet.markercluster";
import "leaflet.markercluster.layersupport"; // make sure this is included

import { fetchSVG } from "../utils/fetchSVG"; // adjust the path

export default class MapApp {
  constructor(mapId, dataUrl, options = {}) {
    this.mapId = mapId;
    this.dataUrl = dataUrl;
    this.options = Object.assign(
      {
        filter: false,
        slider: false,
        editable: false,
      },
      options
    );

    this.markers = [];
    this.map = null;
    this.year = new Date().getFullYear();
    this.markerGroup = null;
    this.svgContent = null;

    this.init();
  }

  async init() {
    this.initMap();
    this.svgContent = await fetchSVG(
      "/wp-content/themes/community-map-theme/assets/mapapp/icon-star.svg"
    );
    this.initClusterGroup();

    if (this.options.editable) {
      this.initEditableMode();
    } else {
      await this.loadMarkers();
    }

    if (this.options.filter) {
      this.initControls();
      this.initSidebarHandlers();
    }
  }

  initMap() {
    this.map = L.map(this.mapId, {
      center: [49.64541, 9.949025],
      zoomSnap: 0.1,
      zoom: 11.5,
      zoomControl: false,
    });
    L.tileLayer(
      "https://api.mapbox.com/styles/v1/{id}/tiles/256/{z}/{x}/{y}?access_token={accessToken}",
      {
        maxZoom: 18,
        minZoom: 1,
        attribution:
          '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a> | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://www.mapbox.com/map-feedback/">Improve this map</a>',
        id: "pondelek/cl9fbuboj000e14o2xcxw3oom",
        accessToken:
          "pk.eyJ1IjoicG9uZGVsZWsiLCJhIjoiY2w5Zm1tc3h4MGphODNvbzBkM29jdWRlaCJ9.j64kLJQP_RmwAccN1jGKrw",
      }
    ).addTo(this.map);
  }

  initClusterGroup() {
    this.markerGroup = L.markerClusterGroup.layerSupport({
      maxClusterRadius: (zoom) => (zoom > 15 ? 5 : 40),
      iconCreateFunction: (cluster) => {
        const count = cluster.getChildCount();

        return L.divIcon({
          html: this.svgContent + `<span class="cluster-count">${count}</span>`,
          className: "custom-cluster-marker custom-cluster",
          iconSize: L.point(60, 60),
        });
      },
    });

    this.map.addLayer(this.markerGroup);
  }

  initControls() {
    console.log("Initializing controls...");
    if (this.options.filter) {
      document.querySelectorAll(".cat_checkbox").forEach((cb) => {
        cb.addEventListener("change", () => {
          this.updateMarkers();
        });
      });
    }

    if (this.options.slider) {
      const sliderElement = document.getElementById("slider-container");
      if (sliderElement) {
        sliderElement.style.display = "block";
      }
      const slider = document.getElementById("yearRange");
      // sliderer element display
      if (slider) {
        slider.addEventListener("input", () => {
          console.log("Slider value:", slider.value);
          this.year = parseInt(slider.value);
          document.getElementById("yearValue").textContent = this.year;
          this.updateMarkers();
        });
        this.year = parseInt(slider.value);
      }
    }
  }

  async loadMarkers() {
    try {
      const res = await fetch(this.dataUrl);
      const geojson = await res.json();
      const data = geojson.features;
      this.markers = data.map((feature) => {
        const coords = feature.geometry.coordinates;
        const props = feature.properties;

        const marker = L.marker([coords[0], coords[1]], {
          name: props.name,
          icon: L.icon({
            iconUrl: feature.taxonomy.category.icon_url,
            iconSize: [30, 30],
          }), // Optional
        });

        marker.post = {
          id: props.post_id,
          title: props.name,
          published: props.date,
          category: feature.taxonomy.category.name,
          category_slug: feature.taxonomy.category.slug,
        };
        marker.category = feature.taxonomy.category.slug;
        marker.start_year = feature.period.start_year;
        marker.end_year = feature.period.end_year;
        let popupText = "";
        if (props.thumbnail_url) {
          popupText += `<img src="${props.thumbnail_url}" alt="${props.name} thumbnail image" width="50px" height="50px">`;
        }
        popupText += `
        <div class="text_wrapper">
          <div class="popup_title">${props.name}</div>
          <div class="popupcategory">${feature.taxonomy.category.name}</div>
          <p>${props.excerpt || ""}</p>
          <a class="popup_button button" href="${props.url}">Eintrag ansehen</a>
        </div>`;
        marker.bindPopup(popupText);
        return marker;
      });

      this.updateMarkers();
    } catch (err) {
      console.error("Error loading marker data:", err);
    }
  }

  updateMarkers(searchTerm = "") {
    if (!this.markerGroup) return;
    // Clear existing markers
    this.markerGroup.clearLayers();
    let filtered = null;
    if (this.searchedMarkers) {
      filtered = this.searchedMarkers;
    } else {
      filtered = this.markers;
    }

    if (this.options.filter) {
      const selectedCategories = Array.from(
        document.querySelectorAll("input[type=checkbox]:checked")
      ).map((cb) => cb.value);
      filtered = filtered.filter((m) =>
        selectedCategories.includes(m.category)
      );
    }

    if (this.options.slider) {
      filtered = filtered.filter(
        (m) => this.year >= m.start_year && this.year <= m.end_year
      );
    }

    this.updateMarkerlist(filtered);
    this.markerGroup.checkIn(filtered);
    this.markerGroup.addLayers(filtered);
  }

  updateMarkerlist(filteredMarkers) {
    const markerList = document.getElementById("marker_list");
    markerList.innerHTML = ""; // Clear existing list

    filteredMarkers.forEach((marker) => {
      const div = document.createElement("div");
      div.className = `show marker--entry map_link_point category_${marker.post.category_slug}`;
      div.id = `map_id_${marker.post.id}`;
      div.setAttribute("category", marker.post.category_slug);
      div.setAttribute("date", marker.post.published);

      div.innerHTML = `
      <div class="entry_title">${marker.post.title}</div>
      <div class="entry_category">
        <img src="${marker.options.icon.options.iconUrl}" />
        ${marker.post.category}
      </div>
      <a class="dn button main-page-button" href="${marker.options.icon.iconUrl}">Eintrag ansehen</a>
      `;

      div.addEventListener("click", () => {
        this.map
          .flyTo(marker.getLatLng(), 15, {
            animate: true,
            //duration: 2, // Adjust duration as needed
          })
          .once("moveend", () => {
            marker.openPopup();
          });
      });

      markerList.appendChild(div);
    });
  }

  initEditableMode() {
    const drawnItems = new L.FeatureGroup();
    this.map.addLayer(drawnItems);

    const drawControl = new L.Control.Draw({
      edit: { featureGroup: drawnItems },
      draw: {
        polygon: false,
        polyline: false,
        circle: false,
        rectangle: false,
        circlemarker: false,
        marker: true,
      },
    });
    this.map.addControl(drawControl);

    this.map.on("draw:created", (e) => {
      const marker = e.layer;
      drawnItems.addLayer(marker);

      const { lat, lng } = marker.getLatLng();
      console.log("New marker:", { lat, lng });

      marker
        .bindPopup(`Lat: ${lat.toFixed(4)}<br>Lng: ${lng.toFixed(4)}`)
        .openPopup();
    });
  }

  initSidebarHandlers() {
    this.initCheckboxHandler();
    this.initSortHandler();
    this.initSearchHandler();
  }

  initCheckboxHandler() {
    const noneButton = document.querySelector(".category_filter .none");
    const allButton = document.querySelector(".category_filter .all");

    if (noneButton) {
      noneButton.addEventListener("click", () => this.uncheckAll());
    }
    if (allButton) {
      allButton.addEventListener("click", () => this.checkAll());
    }
  }

  uncheckAll() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.checked = false;
      this.triggerChange(checkbox);
    });
  }

  checkAll() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.checked = true;
      this.triggerChange(checkbox);
    });
  }

  triggerChange(checkbox) {
    const event = new Event("change", { bubbles: true });
    checkbox.dispatchEvent(event);
  }

  initSortHandler() {
    const sortOptionBox = document.getElementById("list_sort_options");
    sortOptionBox.addEventListener("change", (event) => {
      const option = parseInt(event.target.value);
      const markerList = document.getElementById("marker_list");
      const markers = Array.from(
        document.getElementsByClassName("marker--entry")
      );

      markers.sort((a, b) => {
        switch (option) {
          case 0: // Date sorting
            return (
              new Date(b.getAttribute("date")) -
              new Date(a.getAttribute("date"))
            );
          case 1: // Title sorting
            return a
              .querySelector(".entry_title")
              .textContent.localeCompare(
                b.querySelector(".entry_title").textContent
              );
          default:
            return 0;
        }
      });

      // Reappend sorted elements
      markers.forEach((marker) => markerList.appendChild(marker));
    });
  }
  initSearchHandler() {
    const searchInput = document.getElementById("search");
    let timeoutId = null;
    console.log("Initializing search handler...");
    if (searchInput) {
      searchInput.addEventListener("keyup", () => {
        if (timeoutId) {
          clearTimeout(timeoutId);
        }
        timeoutId = setTimeout(async () => {
          const searchTerm = searchInput.value.trim();
          this.SearchHandler(searchTerm);
        }, 200); // 200ms delay
      });
    }
  }

  async SearchHandler(searchTerm) {
    // if searchTerm is empty, reset the markers
    if (searchTerm === "") {
      this.searchedMarkers = null;
      this.updateMarkers();
      return;
    }
    console.log(searchTerm);

    try {
      const response = await fetch(
        `/wp-json/community-map-theme/geojson${
          searchTerm ? `?search=${searchTerm}` : ""
        }`
      );
      const data = await response.json();

      // Filter the markers based on the search results
      const filteredMarkers = this.markers.filter((marker) =>
        data.features.some(
          (feature) => feature.properties.post_id === marker.post.id
        )
      );
      this.searchedMarkers = filteredMarkers;
      // Update the marker list
      this.updateMarkerlist(filteredMarkers);

      // Update the markers on the map
      this.markerGroup.clearLayers();
      this.markerGroup.addLayers(filteredMarkers);
    } catch (error) {
      console.error("Search error:", error);
    }
  }
}
