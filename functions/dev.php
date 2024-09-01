<?
add_filter('show_admin_bar', '__return_false');

class Dev_custom_button
{
  public function __construct()
  {
    add_action('admin_menu', [$this, 'add_admin_menu']);

    // Import map marker categories, which is in /assets/markertax
    add_action('admin_init', [$this, 'handle_import_button']);
    add_action('admin_init', [$this, 'handle_delete_button']);
  }


  public function add_admin_menu()
  {
    add_menu_page(
      'DEV',         // Page title
      'DEV',         // Menu title
      'manage_options',              // Capability
      'dev',       // Submenu slug
      [$this, 'custom_buttons'],  // Function to display the submenu page content
      '',
      62
    );
  }

  public function custom_buttons()
  {
?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

      <form method="post" action="">
        Markers
        <input type="hidden" name="cba_custom_action" value="generates_markers">
        <?php submit_button('generates 20 markers'); ?>
      </form>
      <br>

      <form method="post" action="">
        <?php submit_button('Delete All markers', 'secondary', 'delete_all_markers'); ?>
      </form>
      <br>

      <form method="post" action="">
        Map markers categories
        <?php submit_button('Import Map Taxonomy', 'primary', 'import_markertax'); ?>
        <?php submit_button('Delete All Map Taxonomies', 'secondary', 'delete_markertax'); ?>
      </form>
    </div>
<?php

    $post_type_query = new WP_Query(array(
      'post_type' => 'marker',
      'posts_per_page' => -1
    ));

    $i = 0;

    while ($post_type_query->have_posts() && $i == 0) {
      $post_type_query->the_post();
      $i++;

      var_dump(get_post_meta(get_the_ID(), $key = "latitude", true));
      var_dump(get_the_ID());
    }

    // Handle the actions based on the button clicked
    if (isset($_POST['cba_custom_action'])) {
      if ($_POST['cba_custom_action'] === 'generates_markers') {
        $this->generate_random_marker_posts();
      }
    } elseif (isset($_POST['delete_all_markers'])) {
      $this->delete_all_markers();
    }
  }

  function generate_random_marker_posts()
  {
    // Number of posts to generate
    $number_of_posts = 20;

    // Range for latitude and longitude

    $min_longitude = get_option('min_longitude');
    $max_longitude = get_option('max_longitude');
    $min_latitude = get_option('min_latitude');
    $max_latitude = get_option('max_latitude');

    // Available taxonomy terms (replace with your actual terms)
    $taxonomy_key = 'markertax';
    $available_terms = get_terms(array(
      'taxonomy' => $taxonomy_key,
      'hide_empty' => false,
    ));

    $end_date = time();  // Current time
    $start_date = strtotime("-3 months", $end_date); // Time three months ago

    for ($i = 0; $i < $number_of_posts; $i++) {
      // Generate a random title (2-3 words)
      $title = wp_generate_password(rand(8, 15), false);

      // Generate random latitude and longitude
      $latitude = rand($min_latitude * 10000, $max_latitude * 10000) / 10000;
      $longitude = rand($min_longitude * 10000, $max_longitude * 10000) / 10000;

      // Select a random taxonomy term
      if (!empty($available_terms) && !is_wp_error($available_terms)) {
        $random_term = $available_terms[array_rand($available_terms)];
      }

      $rand_timestamp = mt_rand($start_date, $end_date);
      $random_date = date('Y-m-d H:i:s', $rand_timestamp);


      // Create the post
      $post_id = wp_insert_post(array(
        'post_title'    => $title,
        'post_type'     => 'marker',
        'post_status'   => 'publish',
        'post_date'     => $random_date // Set the random post date
      ));

      // Add post meta data (geocode)
      if ($post_id) {
        update_post_meta($post_id, 'latitude', $latitude);
        update_post_meta($post_id, 'longitude', $longitude);

        // Assign the taxonomy term
        if (isset($random_term)) {
          wp_set_object_terms($post_id, $random_term->term_id, $taxonomy_key);
        }
      }
    }
  }
  public function delete_all_markers()
  {
    global $wpdb;
    $result_posts = $wpdb->query(
      "
            DELETE a,b,c
            FROM {$wpdb->posts} a
            LEFT JOIN {$wpdb->term_relationships} b ON (a.ID = b.object_id)
            LEFT JOIN {$wpdb->postmeta} c ON (a.ID = c.post_id)
            WHERE a.post_type = 'marker';
      "
    );
    $wpdb->show_errors();
    $wpdb->print_error();

    // Check if the query was successful
    if ($result_posts !== false) {
      echo '<div class="notice notice-success"><p>Custom code executed successfully! Rows affected: ' . ($result_posts) . '</p></div>';
      echo var_dump($wpdb->last_error);
    } else {
      echo '<div class="notice notice-error"><p>An error occurred while executing the custom code.</p></div>';
    }
  }

  public function handle_import_button()
  {
    if (isset($_POST['import_markertax'])) {
      $file_path = get_template_directory() . '/assets/markertax/markertax.txt';

      if (file_exists($file_path)) {
        $this->import_markertax_terms($file_path);
        $this->attach_svg_to_markertax_terms();

        add_action('admin_notices', function () {
          echo '<div class="notice notice-success is-dismissible"><p>Map Taxonomy terms and SVG icons have been imported successfully.</p></div>';
        });
      } else {
        add_action('admin_notices', function () {
          echo '<div class="notice notice-error is-dismissible"><p>Error: The markertax.txt file was not found. Please ensure it is located in the /assets/markertax/ directory.</p></div>';
        });
      }
    }
  }

  public function handle_delete_button()
  {
    if (isset($_POST['delete_markertax'])) {
      $this->delete_markertax_terms();

      add_action('admin_notices', function () {
        echo '<div class="notice notice-warning is-dismissible"><p>All Map Taxonomy terms and their metadata have been deleted.</p></div>';
      });
    }
  }

  // Your existing functions to read file, create taxonomy, import terms, and attach SVGs
  public function read_markertax_file($file_path)
  {
    if (file_exists($file_path)) {
      $terms = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      return $terms;
    } else {
      return array();
    }
  }


  // function create_markertax_taxonomy()
  // {
  //   $labels = array(
  //     'name'              => _x('Map Taxonomies', 'taxonomy general name', 'textdomain'),
  //     'singular_name'     => _x('Map Taxonomy', 'taxonomy singular name', 'textdomain'),
  //     'search_items'      => __('Search Map Taxonomies', 'textdomain'),
  //     'all_items'         => __('All Map Taxonomies', 'textdomain'),
  //     'edit_item'         => __('Edit Map Taxonomy', 'textdomain'),
  //     'update_item'       => __('Update Map Taxonomy', 'textdomain'),
  //     'add_new_item'      => __('Add New Map Taxonomy', 'textdomain'),
  //     'new_item_name'     => __('New Map Taxonomy Name', 'textdomain'),
  //     'menu_name'         => __('Map Taxonomy', 'textdomain'),
  //   );

  //   $args = array(
  //     'hierarchical'      => true, // true for categories, false for tags
  //     'labels'            => $labels,
  //     'show_ui'           => true,
  //     'show_admin_column' => true,
  //     'query_var'         => true,
  //     'rewrite'           => array('slug' => 'markertax'),
  //   );

  //   register_taxonomy('markertax', array('post'), $args);
  // }


  public function import_markertax_terms($file_path)
  {
    $terms = $this->read_markertax_file($file_path);
    foreach ($terms as $term) {
      if (!term_exists($term, 'markertax')) {
        wp_insert_term($term, 'markertax');
      }
    }
  }

  public function attach_svg_to_markertax_terms()
  {
    $terms = get_terms(array(
      'taxonomy' => 'markertax',
      'hide_empty' => false,
    ));

    foreach ($terms as $term) {
      $term_name = $term->name;
      // $svg_file = sanitize_title($term_name) . '.svg';
      $svg_file = $term_name . '.svg';
      $svg_path = get_template_directory_uri() . '/assets/markertax/' . rawurlencode($svg_file);

      update_term_meta($term->term_id, 'taxonomy-icon', $svg_path);
    }
  }

  public function delete_markertax_terms()
  {
    // Logic to delete all "markertax" terms and their metadata
    $terms = get_terms(array(
      'taxonomy' => 'markertax',
      'hide_empty' => false,
    ));

    foreach ($terms as $term) {
      // Delete the term
      wp_delete_term($term->term_id, 'markertax');

      // Optionally, delete associated metadata if stored separately
      delete_term_meta($term->term_id, 'markertax_icon');
    }
  }
}

// Initialize your settings page
new Dev_custom_button();
