document.addEventListener("DOMContentLoaded", function () {
  const colorInput = document.getElementById("primary_color");
  if (colorInput) {
    colorInput.addEventListener("change", function () {
      document.getElementById("submit").click();
    });
  }
});
