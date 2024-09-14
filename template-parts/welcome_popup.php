<div
  class="popup_wrapper <?php echo (isset($_COOKIE['KDB_visitor_visit_time']) || is_user_logged_in()) ? '' : 'show'; ?>">
  <div class="popup info " id="geeting_info_popup">
    <div class="content_wrapper">
      <div class="slide one show">
        <?php $brand = get_field('welcome_text', 'option'); ?>
      </div>
    </div>
    <div class="close d-button" id="d-close-button">
      <div class="close_x_mark"></div>
    </div>
  </div>
</div>