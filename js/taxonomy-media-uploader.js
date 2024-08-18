jQuery(document).ready(function ($) {
  var mediaUploader;

  $(".taxonomy-icon-upload-button").click(function (e) {
    e.preventDefault();

    // If the uploader object has already been created, reopen it
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Icon",
      button: {
        text: "Choose Icon",
      },
      multiple: false,
    });

    // When an image is selected, grab the URL and set it as the value of the input field
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $("#taxonomy-icon").val(attachment.url);
    });

    // Open the uploader dialog
    mediaUploader.open();
  });
});
