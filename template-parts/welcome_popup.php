<div
  class="popup_wrapper <?php echo (isset($_COOKIE['KDB_visitor_visit_time']) || is_user_logged_in()) ? '' : 'show'; ?>">
  <div class="popup info">
    <div class="before_content">
      <?php $image = get_field('welcome_image', 'option'); ?>
      <?php if ($image): ?>
        <?php echo wp_get_attachment_image($image["ID"], 'full'); ?>
      <?php endif ?>
    </div>
    <div class="content_wrapper">
      <div class="content">
        <?php echo get_field('welcome_text', 'option'); ?>
      </div>
      <div class="popup_buttons">
        <a class="button" target="_blank" href="<?php echo site_url("zugang") ?>"><button>Daten bearbeiten</button>
        </a>
        <button class="close button" aria-label="Close">Zur Karte</button>
      </div>
    </div>
    <div class="close d-button" id="d-close-button">
    </div>
  </div>
</div>