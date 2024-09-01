<?

class Themesetting
{
  public function __construct()
  {
    // Custom Admin menu: Theme Einstellungen. 
    // --Map center point
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
  public function render_admin_page()
  {

    // Default value for the geocode (latitude,longitude)
    $default_geocenter = '50.15489468904496, 9.629545376420513'; // 
    $default_georadius = 10;
    // Check if the user has submitted the form
    if (isset($_POST['submit'])) {
      // Verify the nonce for security
      if (!isset($_POST['geocode_nonce']) || !wp_verify_nonce($_POST['geocode_nonce'], 'save_geocode')) {
        return;
      }

      // Sanitize and save the geocode
      $geocenter = $this->sanitize_geocode($_POST['map_center_point']);
      if ($geocenter) {
        update_option('map_center_point', $geocenter);
        list($lati, $long) = explode(', ', $geocenter);
        update_option('map_center_long', $long);
        update_option('map_center_lati', $lati);

        echo '<div class="notice notice-success is-dismissible"><p>Geocode saved successfully.</p></div>';
      } else {
        echo '<div class="notice notice-error is-dismissible"><p>Invalid geocode. Please enter a valid latitude and longitude.</p></div>';
      }

      $georadius = $this->sanitize_georadius($_POST['map_radius']);
      if ($georadius) {
        update_option('map_radius', $georadius);
        echo '<div class="notice notice-success is-dismissible"><p>Radius saved successfully.</p></div>';
      } else {
        echo '<div class="notice notice-error is-dismissible"><p>Invalid Radius. Please enter a valid latitude and longitude.</p></div>';
      }
    }

    // Retrieve the current value of the geocode
    $geocenter = esc_attr(get_option('map_center_point',  $default_geocenter));
    $georadius = esc_attr(get_option('map_radius',  $default_georadius));
    $long = esc_attr(get_option('map_center_long',  $default_georadius));
    $lati = esc_attr(get_option('map_center_lati',  $default_georadius));
    $geocode_range = $this->get_geocoderange($long, $lati, $georadius);
    foreach ($geocode_range as $key => $value) {
      update_option($key,  $value);
    }
?>
    <div class="wrap">
      <h1><?php echo __('Theme Settings', 'textdomain'); ?></h1>
      <form method="post" action="">
        <?php wp_nonce_field('save_geocode', 'geocode_nonce'); ?>
        <table class="form-table">
          <tr valign="top">
            <th scope="row"><?php echo __('Map Center Geocode', 'textdomain'); ?></th>
            <td>
              <p>input need to be seperated by comma(,): You can get this value from google map easily</p>
              <p>latitude, longitude</p>
              <p>default : 50.15489468904496, 9.629545376420513</p>
              <input type="text" name="map_center_point" value="<?php echo $geocenter; ?>" class="regular-text" />
              <p class="description"><?php echo __('Enter the geocode for the map center point.', 'textdomain'); ?></p>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php echo __('Allowed Range of Marker (km)', 'textdomain'); ?></th>
            <td>
              <input type="number" name="map_radius" value="<?php echo $georadius; ?>" class="regular-text" />
              <p class="description"><?php echo __('Enter the allowed radius from the center, under 1000km', 'textdomain'); ?></p>
              <?php
              foreach ($geocode_range as $key => $value) {
                echo $key . ' : ' . esc_attr(get_option($key)) . "<br>";
              }
              ?>
            </td>
          </tr>
          <tr>
            <th>Info</th>
            <td> change the website title (Map App top left text) <a href="http://localhost:10059/wp-admin/options-general.php">here</a>
            </td>
          </tr>
          <tr>
            <th>Color</th>
            <td>primary(category logos, title text color), secondary, highlight
            </td>
          </tr>
          <tr>
            <th>Media Ja oder Nein</th>
            <td>Selection</td>
          </tr>
        </table>
        <?php submit_button(__('Save Settings', 'textdomain')); ?>
      </form>
    </div>
<?php
  }

  function sanitize_geocode($input)
  {
    $input = trim($input); // Remove unnecessary whitespace
    $pattern = '/^-?(180(\.0+)?|(1[0-7][0-9]|[1-9]?[0-9])\.\d+),\s*-?([1-8]?[0-9]\.\d+|90\.0+)$/';

    if (preg_match($pattern, $input)) {
      return $input;
    } else {
      return ''; // Return empty if the input is not valid
    }
  }
  function sanitize_georadius($input)
  {
    $input = trim($input); // Remove unnecessary whitespace
    if (is_numeric($input) &&  $input < 1000 &&  0 < $input) {
      return $input;
    } else {
      return ''; // Either input is not numeric or greater than or equal to 50
    }
  }

  function get_geocoderange($long, $lati, $radius_km)
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

    return array(
      'min_longitude' => $min_longitude,
      'max_longitude' => $max_longitude,
      'min_latitude' => $min_latitude,
      'max_latitude' => $max_latitude
    );
  }
}

new Themesetting();
