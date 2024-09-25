<?

class Leaflet_setting
{
  public function __construct()
  {
    // Add Leaflet dependency for frontend
    add_action('wp_enqueue_scripts', [$this, 'leaflet_dependency'], 20, 1);
    add_action('wp_enqueue_scripts', [$this, 'map_initialize'], 20, 1);
    add_action('wp_enqueue_scripts', [$this, 'leaflet_frontend'], 20, 1);


    //////////------------Geocode searching for new Post page----------------//
    ////////// for Admin page/ backend dependecy admin_enqueue_scripts, for Frontend dependency wp_enqueue_scripts
    add_action('admin_enqueue_scripts', [$this, 'leaflet_dependency'], 10, 1);
    add_action('admin_enqueue_scripts', [$this, 'map_initialize'], 10, 1);
    add_action('admin_enqueue_scripts', [$this, 'metabox_javascript'], 10, 1);
  }

  function leaflet_dependency()
  {
    wp_enqueue_script('leaflet-js',                         get_template_directory_uri() . '/node_modules/leaflet/dist/leaflet.js', array(), false, false);
    wp_enqueue_script('leaflet-marker-cluster-js',          get_template_directory_uri() . '/node_modules/leaflet.markercluster/dist/leaflet.markercluster.js', array('leaflet-js'), false, false);
    wp_enqueue_script('leaflet-marker-cluster-group-js',    get_template_directory_uri() . '/node_modules/leaflet.markercluster.layersupport/dist/leaflet.markercluster.layersupport.js', array('leaflet-marker-cluster-js'), false, false);
    wp_enqueue_script('leaflet-draw-js',                    get_template_directory_uri() . '/node_modules/leaflet-draw/dist/leaflet.draw.js', array('leaflet-marker-cluster-group-js'), false, false);

    wp_enqueue_style('leaflet-main-css',                    get_template_directory_uri() . '/node_modules/leaflet/dist/leaflet.css', array(), false, false);
    wp_enqueue_style('leaflet-marker-cluster-css',          get_template_directory_uri() . '/node_modules/leaflet.markercluster/dist/MarkerCluster.css', array(), false, false);
    wp_enqueue_style('leaflet-marker-cluster-default-css',  get_template_directory_uri() . '/node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css', array(), false, false);
    wp_enqueue_style('leaflet-draw-css',                    get_template_directory_uri() . '/node_modules/leaflet-draw/dist/leaflet.draw.css', array(), false, false);
  }

  function map_initialize()
  {
    wp_enqueue_script('map_init',                         get_template_directory_uri() . '/js/map_init.js', array('leaflet-marker-cluster-js'), false, true);
  }

  function leaflet_frontend()
  {
    if (get_post_type() == 'marker' || is_home()) {
      wp_enqueue_script('mapapp_frontend',                         get_template_directory_uri() . '/js/mapapp_frontend.js', array('map_init'), false, true);
    }
  }

  function metabox_javascript($hook_suffix)
  {
    global $post_type;
    // only call the function for adding coorodinates in Backend, when editing posts
    if ('post.php' == $hook_suffix || 'post-new.php' == $hook_suffix) {
      if ($post_type == 'marker') {
        wp_enqueue_script('admin-map',                         get_template_directory_uri() . '/js/admin_map_metabox.js', array('map_init', 'leaflet-draw-js'), false, true);
      }
    }
  }
}

new Leaflet_setting();
