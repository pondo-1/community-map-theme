<ul class="menu top">
  <li>
    <a class="logo" href="/">
      <h1 class="logo"><span><?php echo bloginfo('title'); ?></span></h1>
    </a>
  </li>
  <?php $welcome_popup = get_field('use_welcome_popup', 'option');  ?>
  <?php if ($welcome_popup == true): ?>
    <li>
      <a class="info" href="#">
        <?php echo file_get_contents(get_template_directory_uri() . '/assets/mapapp/icon-info.svg');
        ?>
        Info
      </a>
    </li>
  <?php endif; ?>
</ul>