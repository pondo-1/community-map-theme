<?

class Post_meta_setting
{
  public function __construct()
  {
    //////------------Amdin Post list columns ----------------//
    /////////------- Add custom column, to see if the post has a right Geocode
    add_filter('manage_posts_columns',                      array($this, 'custom_posts_table_head'));
    add_action('manage_posts_custom_column',                array($this, 'plugin_custom_column'), 10, 2);
  }

  /////////------- Add custom column, to see if the post has a right Geocode
  function custom_posts_table_head($columns)
  {
    $columns['geocode'] = 'geocode';
    $columns['valid'] = 'valid';
    $columns['route'] = 'Route?';
    return $columns;
  }


  function plugin_custom_column($name, $post_id)
  {
    switch ($name) {
      case 'geocode':
        $geocode = get_post_meta($post_id, 'latitude', true) . '<br>' . get_post_meta($post_id, 'longitude', true);
        echo $geocode;
        break;
      case 'valid':
        //if (!array_key_exists($category,$category_array) || empty(get_post_meta( $post_id , 'latitude' , true )) || empty(get_post_meta( $post_id , 'longitude' , true ) )){
        $lati = get_post_meta($post_id, 'latitude', true);
        $longi = get_post_meta($post_id, 'longitude', true);
        $category_name = get_the_category()[0]->name;
        if ($this->post_valid_check($category_name, $lati, $longi)) {
          echo "O";
        } else echo "X: Geocode befindet sich nicht in Europa oder/and  Error in Kategory";
        break;
      case 'route':
        $array = get_post_meta(get_the_ID(), $key = "route");
        if (isset($array)) {
          echo !empty($array[0]) ? 'ja' : '';
        }
        break;
    }
  }

  // Avoid a post with unknown category 
  function post_valid_check($category_name, $lati, $longi)
  {
    // $valid_category = (array_key_exists($category_name, $this->category_shortname_array)) ? 1 : 0;
    // $valid_geocode = ((30 < $lati &&  $lati < 65) && (-15 < $longi && $longi < 45)) ? 1 : 0;
    // return $valid_category * $valid_geocode;
    return 1;
  }
}
new Post_meta_setting();
