<?

class Post_meta_setting
{
  public function __construct()
  {
    add_action('init', array($this, 'create_marker_post_type'));

    // Add Map Taxonomie
    add_action('init', [$this, 'create_map_taxonomy']);

    // Add custom field for taxonomie & Icon upload 
    add_action('admin_enqueue_scripts', [$this, 'enqueue_media_uploader']);
    add_action('markertax_add_form_fields', [$this, 'add_taxonomy_custom_fields']);

    // To allow users to add an icon and color to a custom taxonomy in your WordPress theme,
    add_action('markertax_edit_form_fields', [$this, 'edit_taxonomy_custom_fields'], 10, 2);
    add_action('created_markertax', [$this, 'save_taxonomy_custom_fields']);
    add_action('edited_markertax', [$this, 'save_taxonomy_custom_fields']);

    add_filter('manage_edit-markertax_columns', [$this, 'add_taxonomy_custom_columns']); // Replace 'genre' with your taxonomy slug
    add_filter('manage_markertax_custom_column', [$this, 'populate_taxonomy_custom_columns'], 10, 3); // Replace 'genre' with your taxonomy slug



    //////------------Amdin Post list columns ----------------//
    /////////------- Add custom column, to see if the post has a right Geocode
    add_filter('manage_posts_columns',                      array($this, 'custom_posts_table_head'));
    add_action('manage_posts_custom_column',                array($this, 'plugin_custom_column'), 10, 2);
  }



  function create_marker_post_type()
  {
    $args = array(
      'labels' => array(
        'name' => __('Markers'),
        'singular_name' => __('Marker'),
        'add_new_item' => __('Add New Marker'),
        'edit_item' => __('Edit Marker'),
        'new_item' => __('New Marker'),
        'view_item' => __('View Marker'),
        'search_items' => __('Search Markers'),
        'not_found' => __('No Markers found'),
        'not_found_in_trash' => __('No Markers found in Trash'),
      ),
      'public' => true,
      'show_in_menu' => true,
      'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
      'has_archive' => true,
      'rewrite' => array('slug' => 'points'),
      'show_in_rest' => false, // Disable the block editor (Gutenberg)
      'taxonomies' => array(), // Only use the custom taxonomy 'markertax'

    );

    register_post_type('marker', $args);
  }


  // Add a new taxonomy 
  function create_map_taxonomy()
  {
    // Labels for the taxonomy
    $labels = array(
      'name'              => __('Kategorien'),
      'singular_name'     => __('Kategorie'),
      'search_items'      => __('Search Kategorien'),
      'all_items'         => __('All Kategorien'),
      'edit_item'         => __('Edit Kategorie'),
      'update_item'       => __('Update Kategorie'),
      'add_new_item'      => __('Add New Kategorie'),
      'new_item_name'     => __('New Kategorie Name'),
      'menu_name'         => __('Kategorie'),
    );

    // Register the taxonomy
    register_taxonomy('markertax', array('marker'), array(
      'hierarchical'      => false,
      'labels'            => $labels,
      'show_ui'           => true,
      'show_admin_column' => true,
      'meta_box_cb'       => false, // disable default taxonomie custom box, Use ACF instead for single selection
      'query_var'         => true,
      'rewrite'           => array('slug' => 'markertax'),
    ));
  }


  function enqueue_media_uploader()
  {
    if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'markertax') { // Replace 'genre' with your taxonomy slug
      wp_enqueue_media();
      wp_enqueue_script('taxonomy-media-uploader', get_template_directory_uri() . '/js/taxonomy-media-uploader.js', array('jquery'), null, true);
    }
  }

  // Step 1: Add custom columns to the taxonomy list
  function add_taxonomy_custom_columns($columns)
  {
    $columns['icon'] = __('Icon', 'your-text-domain');
    // $columns['color'] = __('Color', 'your-text-domain');
    return $columns;
  }

  // Step 2: Populate the custom columns with data
  function populate_taxonomy_custom_columns($content, $column_name, $term_id)
  {
    if ($column_name == 'icon') {
      $icon = get_term_meta($term_id, 'taxonomy-icon', true);
      if ($icon) {
        $content = '<img src="' . esc_url($icon) . '" alt="Icon" style="max-width: 40px; height: auto;">';
      } else {
        $content = __('No icon set', 'your-text-domain');
      }
    }
    // elseif ($column_name == 'color') {
    //   $color = get_term_meta($term_id, 'taxonomy-color', true);
    //   if ($color) {
    //     $content = '<div style="width: 40px; height: 20px; background-color:' . esc_attr($color) . ';"></div>';
    //   } else {
    //     $content = __('No color set', 'your-text-domain');
    //   }
    // }
    return $content;
  }



  // Add fields to the taxonomy add form
  function add_taxonomy_custom_fields($taxonomy)
  {
?>
    <div class="form-field">
      <label for="taxonomy-icon"><?php _e('Icon', 'your-text-domain'); ?></label>
      <input type="text" name="taxonomy-icon" id="taxonomy-icon" value="" style="width:70%;">
      <button class="button taxonomy-icon-upload-button"><?php _e('Upload Icon', 'your-text-domain'); ?></button>
      <p class="description"><?php _e('Select an icon from the media library.', 'your-text-domain'); ?></p>
    </div>
    <!-- <div class="form-field">
      <label for="taxonomy-color"><?php _e('Color', 'your-text-domain'); ?></label>
      <input type="color" name="taxonomy-color" id="taxonomy-color" value="#000000">
      <p class="description"><?php _e('Select the color for this taxonomy.', 'your-text-domain'); ?></p>
    </div> -->
  <?php
  }

  // Add fields to the taxonomy edit form
  function edit_taxonomy_custom_fields($term, $taxonomy)
  {
    $icon = get_term_meta($term->term_id, 'taxonomy-icon', true);
    // $color = get_term_meta($term->term_id, 'taxonomy-color', true);
  ?>
    <tr class="form-field">
      <th scope="row"><label for="taxonomy-icon"><?php _e('Icon', 'your-text-domain'); ?></label></th>
      <td>
        <input type="text" name="taxonomy-icon" id="taxonomy-icon" value="<?php echo esc_attr($icon); ?>" style="width:70%;">
        <button class="button taxonomy-icon-upload-button"><?php _e('Upload Icon', 'your-text-domain'); ?></button>
        <p class="description"><?php _e('Select an icon from the media library.', 'your-text-domain'); ?></p>
      </td>
    </tr>
    <!-- <tr class="form-field">
      <th scope="row"><label for="taxonomy-color"><?php _e('Color', 'your-text-domain'); ?></label></th>
      <td>
        <input type="color" name="taxonomy-color" id="taxonomy-color" value="<?php echo esc_attr($color); ?>">
        <p class="description"><?php _e('Select the color for this taxonomy.', 'your-text-domain'); ?></p>
      </td>
    </tr> -->
<?php
  }

  function save_taxonomy_custom_fields($term_id)
  {
    if (isset($_POST['taxonomy-icon'])) {
      update_term_meta($term_id, 'taxonomy-icon', esc_url($_POST['taxonomy-icon']));
    }
    // if (isset($_POST['taxonomy-color'])) {
    //   update_term_meta($term_id, 'taxonomy-color', sanitize_hex_color($_POST['taxonomy-color']));
    // }
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
