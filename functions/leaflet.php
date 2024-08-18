<?

class Leaflet_setting
{
  public function __construct()
  {
    // Add Leaflet dependency for frontend
    add_action('wp_enqueue_scripts', [$this, 'leaflet_dependency'], 20, 1);
    add_action('wp_enqueue_scripts', [$this, 'leaflet_frontend'], 20, 1);
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
  function leaflet_frontend()
  {
    wp_enqueue_script('map_init',                         get_template_directory_uri() . '/js/map_init.js', array(), false, true);
    wp_enqueue_script('mapapp_init',                         get_template_directory_uri() . '/js/mapapp_init.js', array('map_init'), false, true);
  }
}

new Leaflet_setting();
