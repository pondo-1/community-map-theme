<?

class MapAppSettings
{
  private $default_settings = [
    'geocenter' => '50.15489468904496, 9.629545376420513',
    'georadius' => 10,
    'primary_color' => '#407CBF'
  ];

  public function __construct()
  {
    add_action('admin_init', [$this, 'register_settings']);
    add_action('wp_ajax_save_primary_color', [$this, 'save_primary_color_callback']);
    add_action('admin_menu', [$this, 'add_admin_menu']);
  }

  public function add_admin_menu()
  {
    add_menu_page(
      'Theme Einstellung',          // Page title
      'Theme Einstellung',          // Menu title
      'manage_options',         // Capability
      'theme_setting',          // Menu slug
      [$this, 'render_admin_page'], // Function to display the page content
      '',
      61
    );
  }

  /**
   * Register WordPress settings
   */
  public function register_settings()
  {
    register_setting('mapapp_options', 'map_center_point', [
      'sanitize_callback' => [$this, 'sanitize_geocode'],
      'default' => $this->default_settings['geocenter']
    ]);

    register_setting('mapapp_options', 'map_radius', [
      'sanitize_callback' => [$this, 'sanitize_georadius'],
      'default' => $this->default_settings['georadius']
    ]);

    register_setting('mapapp_options', 'mapapp_primary_color', [
      'sanitize_callback' => 'sanitize_hex_color',
      'default' => $this->default_settings['primary_color']
    ]);
  }

  public function render_admin_page()
  {
    if ($this->handle_form_submission()) {
      $this->update_geocode_range();
    }

    settings_errors('mapapp_messages');

    $settings = $this->get_current_settings();
?>
    <div class="wrap">
      <h1><?php echo __('Theme Settings', 'textdomain'); ?></h1>
      <form method="post" action="">
        <?php
        wp_nonce_field('mapapp_settings', 'mapapp_nonce');
        $this->render_map_settings($settings);
        $this->render_color_settings($settings);
        $this->render_other_settings();
        submit_button(__('Save Settings', 'textdomain'));
        ?>
      </form>
    </div>
  <?php
  }

  public function render_map_settings($settings)
  {
  ?>
    <h2>Map Settings</h2>
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><?php echo __('Map Center Geocode', 'textdomain'); ?></th>
        <td>
          <input type="text" name="map_center_point"
            value="<?php echo esc_attr($settings['geocenter']); ?>"
            class="regular-text" />
          <p class="description">
            Format: latitude, longitude (e.g., <?php echo $this->default_settings['geocenter']; ?>)
          </p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><?php echo __('Allowed Range of Marker (km)', 'textdomain'); ?></th>
        <td>
          <input type="number" name="map_radius"
            value="<?php echo esc_attr($settings['georadius']); ?>"
            class="regular-text" />
          <p class="description">Enter the allowed radius from the center (max 1000km)</p>
          <?php $this->display_geocode_range(); ?>
        </td>
      </tr>
    </table>
  <?php
  }

  public function render_color_settings($settings)
  {
  ?>
    <h2>Color Settings</h2>
    <table class="form-table">
      <tr>
        <th scope="row">Primary Color</th>
        <td>
          <input type="color" id="primary_color" name="mapapp_primary_color"
            value="<?php echo esc_attr($settings['primary_color']); ?>">
          <p class="description">Used for category logos and title text color</p>
        </td>
      </tr>
    </table>
  <?php
  }

  public function get_current_settings()
  {
    return [
      'geocenter' => get_option('map_center_point', $this->default_settings['geocenter']),
      'georadius' => get_option('map_radius', $this->default_settings['georadius']),
      'primary_color' => get_option('mapapp_primary_color', $this->default_settings['primary_color'])
    ];
  }

  public function handle_form_submission()
  {
    if (!isset($_POST['submit'])) {
      return false;
    }

    if (!check_admin_referer('mapapp_settings', 'mapapp_nonce')) {
      add_settings_error('mapapp_messages', 'mapapp_nonce_error', 'Security verification failed.');
      return false;
    }

    $this->save_settings();
    return true;
  }

  private function save_settings()
  {
    $has_errors = false;

    if (isset($_POST['map_center_point'])) {
      $geocenter = $this->sanitize_geocode($_POST['map_center_point']);
      if ($geocenter) {
        update_option('map_center_point', $geocenter);
        list($lati, $long) = explode(', ', $geocenter);
        update_option('map_center_long', $long);
        update_option('map_center_lati', $lati);
        add_settings_error('mapapp_messages', 'mapapp_geocode_updated', 'Geocode saved successfully.', 'success');
      } else {
        $has_errors = true;
      }
    }

    if (isset($_POST['map_radius'])) {
      $georadius = $this->sanitize_georadius($_POST['map_radius']);
      if ($georadius) {
        update_option('map_radius', $georadius);
        add_settings_error('mapapp_messages', 'mapapp_radius_updated', 'Radius saved successfully.', 'success');
      } else {
        $has_errors = true;
      }
    }

    if (isset($_POST['mapapp_primary_color'])) {
      $color = sanitize_hex_color($_POST['mapapp_primary_color']);
      if ($color) {
        update_option('mapapp_primary_color', $color);
      } else {
        add_settings_error(
          'mapapp_messages',
          'mapapp_color_error',
          'Invalid color format. Please enter a valid hex color.',
          'error'
        );
        $has_errors = true;
      }
    }

    return !$has_errors;
  }

  /**
   * Updates the geocode range based on current center point and radius
   * Stores min/max latitude and longitude in WordPress options
   */
  private function update_geocode_range()
  {
    $long = get_option('map_center_long');
    $lati = get_option('map_center_lati');
    $radius = get_option('map_radius');

    if ($long && $lati && $radius) {
      $geocode_range = $this->get_geocoderange($long, $lati, $radius);
      foreach ($geocode_range as $key => $value) {
        update_option($key, $value);
      }
    }
  }

  /**
   * Displays the current geocode range values
   */
  private function display_geocode_range()
  {
    $range_options = [
      'min_longitude' => 'Minimum Longitude',
      'max_longitude' => 'Maximum Longitude',
      'min_latitude' => 'Minimum Latitude',
      'max_latitude' => 'Maximum Latitude'
    ];

    echo '<div class="geocode-range-display">';
    foreach ($range_options as $key => $label) {
      $value = esc_attr(get_option($key));
      echo "<div><strong>{$label}:</strong> {$value}</div>";
    }
    echo '</div>';
  }

  /**
   * Renders additional settings section
   */
  private function render_other_settings()
  {
  ?>
    <h2>Other Settings</h2>
    <table class="form-table">
      <tr>
        <th>Website Title</th>
        <td>
          Change the website title (Map App top left text)
          <a href="/wp-admin/options-general.php">here</a>
        </td>
      </tr>
      <tr>
        <th>Media Settings</th>
        <td>
          <select name="media_enabled">
            <option value="1">Enabled</option>
            <option value="0">Disabled</option>
          </select>
        </td>
      </tr>
    </table>
<?php
  }

  /**
   * Sanitizes the geocode input
   * @param string $input The input geocode string
   * @return string Sanitized geocode or empty string if invalid
   * 1. Longitude (first part, before comma):
   * Can be negative (-) or positive
   * Must be between -180 and 180
   * Must have decimal points
   * Exactly 180 can only have zeros after decimal point
   * 2. Latitude (second part, after comma):
   * Can be negative (-) or positive
   * Must be between -90 and 90
   * Must have decimal points
   * Exactly 90 can only have zeros after decimal point
   */
  public function sanitize_geocode($input)
  {
    $input = trim($input);
    // Validates format: latitude,longitude (e.g., 50.15489468904496, 9.629545376420513)
    $pattern = '/^-?(180(\.0+)?|(1[0-7][0-9]|[1-9]?[0-9])\.\d+),\s*-?([1-8]?[0-9]\.\d+|90\.0+)$/';

    if (preg_match($pattern, $input)) {
      return $input;
    }
    add_settings_error(
      'mapapp_messages',
      'mapapp_geocode_error',
      'Invalid geocode format. Please enter valid latitude and longitude.',
      'error'
    );
    return '';
  }

  /**
   * Sanitizes the radius input
   * @param string|int $input The input radius value
   * @return int|string Sanitized radius or empty string if invalid
   */
  public function sanitize_georadius($input)
  {
    $input = trim($input);
    if (is_numeric($input) && $input < 1000 && $input > 0) {
      return (int)$input;
    }
    add_settings_error(
      'mapapp_messages',
      'mapapp_radius_error',
      'Invalid radius. Please enter a number between 1 and 1000.',
      'error'
    );
    return '';
  }

  /**
   * Calculates the geocode range based on center point and radius
   * @param float $long Longitude of center point
   * @param float $lati Latitude of center point
   * @param float $radius_km Radius in kilometers
   * @return array Array containing min/max latitude and longitude
   */
  private function get_geocoderange($long, $lati, $radius_km)
  {
    // Earth's radius in kilometers
    $earth_radius_km = 6371;

    // Latitude calculation
    $latitude_diff = $radius_km / 111.32;
    $min_latitude = $lati - $latitude_diff;
    $max_latitude = $lati + $latitude_diff;

    // Longitude calculation (adjusted by latitude)
    $longitude_diff = $radius_km / (111.32 * cos(deg2rad($lati)));
    $min_longitude = $long - $longitude_diff;
    $max_longitude = $long + $longitude_diff;

    return [
      'min_longitude' => $min_longitude,
      'max_longitude' => $max_longitude,
      'min_latitude' => $min_latitude,
      'max_latitude' => $max_latitude
    ];
  }

  /**
   * AJAX callback for saving primary color
   */
  public function save_primary_color_callback()
  {
    check_ajax_referer('mapapp_settings', 'nonce');

    if (!current_user_can('manage_options')) {
      wp_send_json_error('Insufficient permissions');
      return;
    }

    $color = sanitize_hex_color($_POST['color']);
    if ($color) {
      update_option('mapapp_primary_color', $color);
      wp_send_json_success(['color' => $color]);
    } else {
      wp_send_json_error('Invalid color format');
    }
  }
}

new MapAppSettings();

// function init_mapapp_settings()
// {
//   $mapapp_settings = new MapAppSettings();
// }
// add_action('admin_menu', 'init_mapapp_settings');
