jQuery(document).ready(function ($) {
  $(".popup.info .close").click(function () {
    $(".popup_wrapper").removeClass("show");
  });

  $(".menu.top .info").click(function (event) {
    event.preventDefault();
    $(".popup_wrapper").addClass("show");
  });

  // console.log(document.cookie.indexOf("KDB_visitor_visit_time="));
  // console.log(document.cookie);
});
