<ul class="menu top">
  <li>
    <!--  if the options of get_field('Title_option', 'option') == text or == logo -->

    <a class="logo" href="/">
      <?php
      $title_option = get_field('title_option', 'option');
      if ($title_option == 'text') {
        echo '<h1 class="logo"><span>' . bloginfo('title') . '</span></h1>';
      } elseif ($title_option == 'logo') {
        $logo = get_field('logo', 'option');
        echo '<img src="' . esc_url($logo['url']) . '" alt="' . esc_attr($logo['alt']) . '" />';
      }
      ?>
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