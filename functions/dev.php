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

  //   $term_id = $term->term_id; // Replace with actual term ID
  // $svg_file = get_term_meta($term_id, 'markertax_icon', true);

  // if ($svg_file) {
  //     $svg_url = get_template_directory_uri() . '/assets/markertax/' . $svg_file;
  //     echo '<img src="' . esc_url($svg_url) . '" alt="' . esc_attr($term->name) . '">';
  // }

  public function add_admin_menu()
  {
    add_menu_page(
      'Import Map Taxonomy',         // Page title
      'Import Map Taxonomy',         // Menu title
      'manage_options',              // Capability
      'import_markertax_submenu',       // Submenu slug
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
      Markers
      <form method="post" action="">
        <input type="hidden" name="cba_custom_action" value="generates_markers">
        <?php submit_button('Preview Posts to Delete'); ?>
      </form>
      <br>
      Map markers categories
      <form method="post" action="">
        <?php submit_button('Import Map Taxonomy', 'primary', 'import_markertax'); ?>
        <?php submit_button('Delete All Map Taxonomies', 'secondary', 'delete_markertax'); ?>
      </form>
    </div>
<?php

    // Handle the actions based on the button clicked
    if (isset($_POST['cba_custom_action'])) {
      if ($_POST['cba_custom_action'] === 'generates_markers') {
        $this->generates_markers();
      }
    }
  }

  public function generates_markers()
  {
    // Generate 10 post with post_type="marker" with longitute and latitude in the range of -- and take a one of the taxonomie from "markertax"
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
