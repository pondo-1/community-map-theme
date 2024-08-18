<?

class Themesetting
{
  public function __construct()
  {
    // Hook into the admin menu
    add_action('admin_menu', [$this, 'add_admin_menu']);
    // Add Map Taxonomie
    add_action('init', [$this, 'create_map_taxonomy']);
    // Add custom field for taxonomie & Icon upload 
    add_action('admin_enqueue_scripts', [$this, 'enqueue_media_uploader']);
    add_action('maptax_add_form_fields', [$this, 'add_taxonomy_custom_fields']);
    // To allow users to add an icon and color to a custom taxonomy in your WordPress theme,
    add_action('maptax_edit_form_fields', [$this, 'edit_taxonomy_custom_fields'], 10, 2);
    add_action('created_maptax', [$this, 'save_taxonomy_custom_fields']);
    add_action('edited_maptax', [$this, 'save_taxonomy_custom_fields']);

    add_filter('manage_edit-maptax_columns', [$this, 'add_taxonomy_custom_columns']); // Replace 'genre' with your taxonomy slug
    add_filter('manage_maptax_custom_column', [$this, 'populate_taxonomy_custom_columns'], 10, 3); // Replace 'genre' with your taxonomy slug

  }

  public function add_admin_menu()
  {
    add_menu_page(
      'Theme Einstellung',          // Page title
      'Theme Einstellung',          // Menu title
      'manage_options',         // Capability
      'theme_setting',          // Menu slug
      [$this, 'render_admin_page'], // Function to display the page content
      '',
      61
    );
  }
  public function render_admin_page()
  {
    echo "here";
  }

  // Add a new taxonomy (e.g., 'Genres' for a 'Book' post type)
  function create_map_taxonomy()
  {
    // Labels for the taxonomy
    $labels = array(
      'name'              => _x('Karte Taxonomie', 'taxonomy general name'),
      'singular_name'     => _x('Karte Taxonomie', 'taxonomy singular name')
    );

    // Register the taxonomy
    register_taxonomy('maptax', array('post'), array(
      'hierarchical'      => false,
      'labels'            => $labels,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'rewrite'           => array('slug' => 'maptax'),
    ));
  }

  function enqueue_media_uploader()
  {
    if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'maptax') { // Replace 'genre' with your taxonomy slug
      wp_enqueue_media();
      wp_enqueue_script('taxonomy-media-uploader', get_template_directory_uri() . '/js/taxonomy-media-uploader.js', array('jquery'), null, true);
    }
  }

  // Step 1: Add custom columns to the taxonomy list
  function add_taxonomy_custom_columns($columns)
  {
    $columns['icon'] = __('Icon', 'your-text-domain');
    $columns['color'] = __('Color', 'your-text-domain');
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
    } elseif ($column_name == 'color') {
      $color = get_term_meta($term_id, 'taxonomy-color', true);
      if ($color) {
        $content = '<div style="width: 40px; height: 20px; background-color:' . esc_attr($color) . ';"></div>';
      } else {
        $content = __('No color set', 'your-text-domain');
      }
    }
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
    <div class="form-field">
      <label for="taxonomy-color"><?php _e('Color', 'your-text-domain'); ?></label>
      <input type="color" name="taxonomy-color" id="taxonomy-color" value="#000000">
      <p class="description"><?php _e('Select the color for this taxonomy.', 'your-text-domain'); ?></p>
    </div>
  <?php
  }

  // Add fields to the taxonomy edit form
  function edit_taxonomy_custom_fields($term, $taxonomy)
  {
    $icon = get_term_meta($term->term_id, 'taxonomy-icon', true);
    $color = get_term_meta($term->term_id, 'taxonomy-color', true);
  ?>
    <tr class="form-field">
      <th scope="row"><label for="taxonomy-icon"><?php _e('Icon', 'your-text-domain'); ?></label></th>
      <td>
        <input type="text" name="taxonomy-icon" id="taxonomy-icon" value="<?php echo esc_attr($icon); ?>" style="width:70%;">
        <button class="button taxonomy-icon-upload-button"><?php _e('Upload Icon', 'your-text-domain'); ?></button>
        <p class="description"><?php _e('Select an icon from the media library.', 'your-text-domain'); ?></p>
      </td>
    </tr>
    <tr class="form-field">
      <th scope="row"><label for="taxonomy-color"><?php _e('Color', 'your-text-domain'); ?></label></th>
      <td>
        <input type="color" name="taxonomy-color" id="taxonomy-color" value="<?php echo esc_attr($color); ?>">
        <p class="description"><?php _e('Select the color for this taxonomy.', 'your-text-domain'); ?></p>
      </td>
    </tr>
<?php
  }

  function save_taxonomy_custom_fields($term_id)
  {
    if (isset($_POST['taxonomy-icon'])) {
      update_term_meta($term_id, 'taxonomy-icon', esc_url($_POST['taxonomy-icon']));
    }
    if (isset($_POST['taxonomy-color'])) {
      update_term_meta($term_id, 'taxonomy-color', sanitize_hex_color($_POST['taxonomy-color']));
    }
  }
}

new Themesetting();
