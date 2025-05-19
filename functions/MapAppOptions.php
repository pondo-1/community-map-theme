<?

class MapAppOptions
{
  private $default_settings = [
    'geocenter' => '50.15489468904496, 9.629545376420513',
    'georadius' => 10,
    // 'primary_color' => '#407CBF'
  ];

  public function __construct()
  {
    add_action('wp_head', [$this, 'add_custom_colors']);
    // add_action('wp_ajax_save_primary_color', [$this, 'save_primary_color_callback']);
  }
  function add_custom_colors()
  {
    $primary_color = get_option('mapapp_primary_color', '#407CBF');
?>
    <style>
      :root {
        --primary-color: <?php echo esc_attr($primary_color); ?>;
      }
    </style>
<?php
  }
}

new MapAppOptions();
