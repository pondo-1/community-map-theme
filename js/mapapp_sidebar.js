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

  uncheckAll() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.checked = false;
      this.triggerChange(checkbox);
      // this.updateListStyles();
    });
  }

  checkAll() {
    document.querySelectorAll(".cat_checkbox").forEach((checkbox) => {
      checkbox.checked = true;
      this.triggerChange(checkbox);
      // this.updateListStyles();
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
    var sort_option_box, i, switching, b, shouldSwitch, option;
    sort_option_box = document.getElementById("list_sort_options");
    sort_option_box.addEventListener("change", (event) => {
      option = event.target.value;
      switching = true;
      /* Make a loop that will continue until
            no switching has been done: */
      while (switching) {
        // start by saying: no switching is done:
        switching = false;
        //b = list.getElementsByTagName("LI");
        b = document.getElementsByClassName("marker--entry");
        // Loop through all list-items:
        for (i = 0; i < b.length - 1; i++) {
          // start by saying there should be no switching:
          shouldSwitch = false;
          /* check if the next item should
                switch place with the current item: */
          var check;
          if (option == 0) {
            let x = new Date(b[i].getAttribute("date"));
            let y = new Date(b[i + 1].getAttribute("date"));
            check = x < y;
          } else if (option == 1) {
            check =
              b[i].innerHTML.toLowerCase() > b[i + 1].innerHTML.toLowerCase();
          }
          if (option == 2) {
            check =
              b[i].getAttribute("author").toLowerCase() >
              b[i + 1].getAttribute("author").toLowerCase();
          }
          if (check) {
            /* if next item is alphabetically
                  lower than current item, mark as a switch
                  and break the loop: */
            shouldSwitch = true;
            break;
          }
        }
        if (shouldSwitch) {
          /* If a switch has been marked, make the switch
                and mark the switch as done: */
          b[i].parentNode.insertBefore(b[i + 1], b[i]);
          switching = true;
        }
      }
    });
  }
}

// Instantiate the CheckboxHandler class

function updateSecondElementStyles() {
  // First, remove the 'bg' class from all entries
  document.querySelectorAll(".entry.show").forEach((el) => {
    el.classList.remove("bg-second");
  });

  // Get only visible elements with the 'show' class
  const visibleEntries = Array.from(document.querySelectorAll(".entry.show"));

  // Add 'bg-second' class to every second element in the list of visible entries
  visibleEntries.forEach((el, index) => {
    if (index % 2 === 1) {
      // index % 2 === 1 for 0-based index to style the second, fourth, sixth, etc., elements
      el.classList.add("bg-second");
    }
  });
}

// list style

document.addEventListener("DOMContentLoaded", () => {
  new CheckboxHandler();
  new SortFnHandler();
});
