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
    $default_geocode = '50.15489468904496, 9.629545376420513'; // Example: San Francisco, CA

    // Check if the user has submitted the form
    if (isset($_POST['submit'])) {
      // Verify the nonce for security
      if (!isset($_POST['geocode_nonce']) || !wp_verify_nonce($_POST['geocode_nonce'], 'save_geocode')) {
        return;
      }

      // Sanitize and save the geocode
      $geocode = $this->sanitize_geocode($_POST['map_center_point']);
      if ($geocode) {
        update_option('map_center_point', $geocode);
        echo '<div class="notice notice-success is-dismissible"><p>Geocode saved successfully.</p></div>';
      } else {
        echo '<div class="notice notice-error is-dismissible"><p>Invalid geocode. Please enter a valid latitude and longitude.</p></div>';
      }
    }

    // Retrieve the current value of the geocode
    $geocode = esc_attr(get_option('map_center_point',  $default_geocode));

    // Output the form
?>
    <div class="wrap">
      <h1><?php _e('Theme Settings', 'your-text-domain'); ?></h1>
      <form method="post" action="">
        <?php wp_nonce_field('save_geocode', 'geocode_nonce'); ?>
        <table class="form-table">
          <tr valign="top">
            <th scope="row"><?php _e('Map Center Geocode', 'your-text-domain'); ?></th>
            <td>
              <p>input need to be seperated by comma(,)</p>
              <p>longitude, latitude </p>
              <p> default : 50.15489468904496, 9.629545376420513</p>
              <input type="text" name="map_center_point" value="<?php echo $geocode; ?>" class="regular-text" />
              <p class="description"><?php _e('Enter the geocode for the map center point.', 'your-text-domain'); ?></p>
            </td>
          </tr>
        </table>
        <?php submit_button(__('Save Settings', 'your-text-domain')); ?>
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
}

new Themesetting();
