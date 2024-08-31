<?

class Marker_list
{
  public function __construct()
  {
    // Add filter js
    add_action('wp_enqueue_scripts', [$this, 'show_marker_list'], 20, 1);
  }

  function show_marker_list()
  {
    // filter
    wp_enqueue_script('filter_buttons',                         get_template_directory_uri() . '/js/mapapp_sidebar.js', array('map_init', 'mapapp_init'), false, true);
  }
}

new Marker_list();
