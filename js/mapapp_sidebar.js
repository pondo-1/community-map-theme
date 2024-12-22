/**
 * CheckboxHandler Class
 * Manages category filtering functionality through checkboxes
 * Controls visibility of map markers and list items based on category selection
 */
class CheckboxHandler {
  constructor() {
    this.init();
  }

  init() {
    if (document.querySelector(".home .mapapp .sidebar")) {
      this.initListStyle();
      this.addEventListeners();
    }
  }

  /**
   * Sets initial visibility state of list items based on checkbox states
   * Applies 'show' class to elements matching checked categories
   */
  initListStyle() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      const targetClass = `category_${checkbox.value}`;
      const currentCategory = document.getElementsByClassName(targetClass);
      if (checkbox.checked) {
        Array.from(currentCategory).forEach((el) => el.classList.add("show"));
      } else {
        Array.from(currentCategory).forEach((el) =>
          el.classList.remove("show")
        );
      }
    });
  }

  /**
   * Sets up event listeners for:
   * - Individual category checkboxes
   * - "Select All" button
   * - "Select None" button
   * Updates visibility of items when checkboxes change
   */
  addEventListeners() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.addEventListener("change", () => {
        const targetClass = `category_${checkbox.value}`;
        const currentCategory = document.getElementsByClassName(targetClass);
        if (checkbox.checked) {
          Array.from(currentCategory).forEach((el) => el.classList.add("show"));
        } else {
          Array.from(currentCategory).forEach((el) =>
            el.classList.remove("show")
          );
        }
        // this.updateListStyles();
      });
    });

    const noneButton = document.querySelector(".category_filter .none");
    const allButton = document.querySelector(".category_filter .all");

    if (noneButton) {
      noneButton.addEventListener("click", () => this.uncheckAll());
    }
    if (allButton) {
      allButton.addEventListener("click", () => this.checkAll());
    }
  }

  /**
   * Utility method to uncheck all category checkboxes
   * Triggers change events to update visibility
   */
  uncheckAll() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.checked = false;
      this.triggerChange(checkbox);
      // this.updateListStyles();
    });
  }

  /**
   * Utility method to check all category checkboxes
   * Triggers change events to update visibility
   */
  checkAll() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.checked = true;
      this.triggerChange(checkbox);
      // this.updateListStyles();
    });
  }

  /**
   * Updates the striped background styling of visible list items
   * Adds 'bg' class to every second visible item for alternating background effect
   */
  updateListStyles() {
    const listItems = document.querySelectorAll(".marker--entry"); // Modify selector to match your list entries
    // First, remove the 'bg' class from all list items
    listItems.forEach((item) => {
      item.classList.remove("bg");
    });

    // Filter visible items and apply 'bg' class to every second visible item
    let visibleItems = Array.from(listItems).filter(
      (item) => item.style.display !== "none"
    );

    visibleItems.forEach((item, index) => {
      if (index % 2 === 1) {
        // Apply 'bg' class to every second visible item
        item.classList.add("bg");
      }
    });
  }

  /**
   * Helper method to programmatically trigger change events on checkboxes
   * @param {HTMLElement} checkbox - The checkbox element to trigger the event on
   */
  triggerChange(checkbox) {
    const event = new Event("change", { bubbles: true });
    checkbox.dispatchEvent(event);
  }
}

/**
 * SortFnHandler Class
 * Manages sorting functionality for the list of items
 * Supports sorting by:
 * - Date (option 0)
 * - title (option 1)
 */
class SortFnHandler {
  constructor() {
    this.init();
  }

  init() {
    if (document.querySelector(".home .mapapp .sidebar")) {
      this.addEventListeners();
    }
  }

  addEventListeners() {
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
}

/**
 * Add this new class for handling the marker list
 */
class MarkerListHandler {
  constructor() {
    this.markerListElement = document.getElementById("marker_list");
    this.init();
  }

  async init() {
    if (this.markerListElement) {
      await this.fetchAndRenderMarkers();
    }
  }

  async fetchAndRenderMarkers() {
    try {
      const response = await fetch("/wp-json/community-map-theme/geojson");
      const data = await response.json();
      this.renderMarkerList(data.features);
    } catch (error) {
      console.error("Error fetching markers:", error);
    }
  }

  renderMarkerList(features) {
    const html = features
      .map(
        (feature) => `
      <div class="show marker--entry map_link_point category_${feature.taxonomy.category.slug}" 
           id="map_id_${feature.id}" 
           category="${feature.taxonomy.category.slug}"
           date="${feature.properties.date}"
           author="${feature.properties.author}">
        <div class="entry_title">${feature.properties.name}</div>
        <div class="entry_date">${feature.properties.date}</div>
        <div class="entry_author">${feature.properties.author}</div>
        <div class="entry_category">
          <img src="${feature.taxonomy.category.icon_url}" />
          ${feature.taxonomy.category.name}
        </div>
        <a class="dn button main-page-button" href="${feature.properties.url}">Eintrag ansehen</a>
      </div>
    `
      )
      .join("");

    this.markerListElement.innerHTML = html;
  }
}

/**
 * SearchHandler Class
 * Manages search functionality for the list of items
 * Supports searching by:
 * - Name (option 0)
 */
class SearchHandler {
  constructor() {
    this.searchInput = document.getElementById("search");
    this.markerListElement = document.getElementById("marker_list");
    this.timeoutId = null;
    this.init();
  }

  init() {
    if (this.searchInput) {
      this.addEventListeners();
    }
  }

  addEventListeners() {
    this.searchInput.addEventListener("keyup", () => {
      if (this.timeoutId) {
        clearTimeout(this.timeoutId);
      }

      this.timeoutId = setTimeout(async () => {
        const searchTerm = this.searchInput.value.trim();

        try {
          const response = await fetch(
            `/wp-json/community-map-theme/geojson${
              searchTerm ? `?search=${searchTerm}` : ""
            }`
          );
          const data = await response.json();

          // Get all marker entries in the list
          const markerEntries =
            this.markerListElement.querySelectorAll(".marker--entry");

          // Create a Set of matching IDs from the API response
          const matchingIds = new Set(
            data.features.map((feature) => `map_id_${feature.id}`)
          );

          // Show/hide entries based on ID matches
          markerEntries.forEach((entry) => {
            if (matchingIds.has(entry.id)) {
              entry.style.display = ""; // Show matching entries
            } else {
              entry.style.display = "none"; // Hide non-matching entries
            }
          });
        } catch (error) {
          console.error("Search error:", error);
        }
      }, 300); // 300ms delay
    });
  }
}

/**
 * Initialize handlers when DOM is fully loaded
 * Creates instances of CheckboxHandler, SortFnHandler, and MarkerListHandler
 */
document.addEventListener("DOMContentLoaded", () => {
  new SearchHandler();
  new CheckboxHandler();
  new SortFnHandler();
});
