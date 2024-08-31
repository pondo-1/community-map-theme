class CheckboxHandler {
  constructor() {
    this.init();
  }

  init() {
    if (document.querySelector(".mapapp .sidebar")) {
      this.addEventListeners();
      this.updateListStyles();
    }
  }

  addEventListeners() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.addEventListener("change", () => {
        const targetClass = `category_${checkbox.value}`;
        const currentCategory = document.getElementsByClassName(targetClass);
        if (checkbox.checked) {
          Array.from(currentCategory).forEach(
            (el) => (el.style.display = "block")
          );
        } else {
          Array.from(currentCategory).forEach(
            (el) => (el.style.display = "none")
          );
        }
        this.updateListStyles();
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

  uncheckAll() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.checked = false;
      this.triggerChange(checkbox);
      this.updateListStyles();
    });
  }

  checkAll() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.checked = true;
      this.triggerChange(checkbox);
      this.updateListStyles();
    });
  }

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
  triggerChange(checkbox) {
    const event = new Event("change", { bubbles: true });
    checkbox.dispatchEvent(event);
  }
}

// Instantiate the CheckboxHandler class
document.addEventListener("DOMContentLoaded", () => new CheckboxHandler());
