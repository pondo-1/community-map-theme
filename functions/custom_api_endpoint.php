<?
// // In order to generage API endpoint
class RestAPI_Base
{
  protected $base;

  function __construct()
  {
    $this->base = THEMENAME;
  }

  // A method to register a REST route
  protected function register_route($route, $callback, $methods = WP_REST_SERVER::READABLE)
  {
    register_rest_route($this->base, $route, array(
      'methods' => $methods,
      'callback' => $callback
    ));
  }

  public function register_api()
  {
    // Get the class name (e.g., 'Geojson_API')
    $class_name = get_class($this);

    // Remove the suffix '_API' if it exists
    $base_name = str_replace('_API', '', $class_name);

    // Convert the class name to lowercase (or any other desired format)
    $route = strtolower($base_name);

    // Register the route dynamically
    $this->register_route('/' . $route . '/', array($this, 'generator'));
  }

  public function generator($data)
  {
    // Implement your logic here
  }
}

class Infojson_API extends RestAPI_Base
{
  public function __construct()
  {
    parent::__construct();
    add_action('rest_api_init', array($this, 'register_api'));
  }

  public function generator($data)
  {
    $map_center_geo = array_map("floatval", explode(',', esc_attr(get_option('map_center_point'))));

    $map_info = [
      "center"        => array_map("floatval", explode(',', esc_attr(get_option('map_center_point')))),
      "center_long"   => esc_attr(get_option('map_center_long')),
      "center_lati"   => esc_attr(get_option('map_center_lati')),
      "radius"        => esc_attr(get_option('map_radius')),
      "min_longitude" => esc_attr(get_option('min_longitude')),
      "max_longitude" => esc_attr(get_option('max_longitude')),
      "min_latitude"  => esc_attr(get_option('min_latitude')),
      "max_latitude"  => esc_attr(get_option('max_latitude'))
    ];

    $terms = get_terms(array(
      'taxonomy' => 'markertax',
      'hide_empty' => false, // Show all terms, even those without posts
    ));

    // Initialize an empty array to hold the formatted terms
    $marker_category = [];

    // Loop through each term and format it
    foreach ($terms as $term) {
      $marker_category[] = array(
        'name' => $term->name,
        'icon' => get_term_meta($term->term_id, 'taxonomy-icon', true), // Assuming you store the icon URL in term meta
        'slug' => $term->slug,
      );
    }

    $final_array = [
      "map" => $map_info,
      "marker_category" => $marker_category
    ];

    return $final_array;
  }
}

new Infojson_API(); // endpoint: /wp-json/community-map-theme/infojson
