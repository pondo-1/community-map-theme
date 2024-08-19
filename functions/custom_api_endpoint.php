<?
// // In order to generage API endpoint

// //-------------- Rest API ---------------------//
// ////// Rest API /wp-json/ILEK-Map-App/infojson
// add_action('rest_api_init', array($this, 'infojson_generate_api'));

// function infojson_generate_api()
// {
//   $plugin_folder_name = reset(explode('/', str_replace(WP_PLUGIN_DIR . '/', '', __DIR__)));
//   register_rest_route($plugin_folder_name, '/infojson/', array(
//     'methods' => WP_REST_SERVER::READABLE,
//     'callback' => array($this, 'infojson_generator')
//   ));
// }

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
