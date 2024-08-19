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

    $info_array = array(
      'map_center' => $map_center_geo,
    );
    return $info_array;
  }
}

new Infojson_API(); // endpoint: /wp-json/community-map-theme/infojson


//-------------- Rest API ---------------------//
////// Rest API /wp-json/ILEK-Map-App/infojson


// public function infojson_generator()
// {
//   //$info_array=array();
//   $plugin_folder_name = reset(explode('/', str_replace(WP_PLUGIN_DIR . '/', '', __DIR__)));
//   $path_of_icons =  './wp-content/plugins/' . $plugin_folder_name . '/icons';
//   $icon_files = array_diff(scandir($path_of_icons), array('.', '..'));

//   // if we need to make a custom section for center 
//   // $longi = "50.15489468904496";
//   // settype ($longi, "float");
//   // $lati = "9.629545376420513";
//   // settype ($lati, "float");

//   $map_center_geo = array_map("floatval", explode(',', esc_attr(get_option('sad_map_center_point'))));
//   $myarray = $this->category_shortname_array;

//   $info_array = array(
//     'map_center' => $map_center_geo,
//     'icons_directory' => $path_of_icons,
//     //'icons'=> $icon_files,
//     'icons' => $myarray
//     //'geo_code'=>$geo_code
//   );
//   return $info_array;
// }
// //////end-------------------------------- Rest API /wp-json/ILEK-Map-App/infojson
