<?php
// Geocode input interface 
class Admin_mapapp
{
  public function __construct()
  {
    add_action('admin_enqueue_scripts',  array($this, 'enqueue_admin_script'));
    //////////------------Meta data for new Post page----------------// 
    add_action('add_meta_boxes', array($this, 'standort_boxes'));
    add_action('save_post', array($this, 'save_standort_box'));
  }

  function enqueue_admin_script($hook)
  {
    // Only enqueue on post edit pages
    if ($hook === 'post.php') {
      wp_enqueue_script(
        'admin-geometa-js',
        get_template_directory_uri() . '/admin/js/admin_map_metabox.js',
        array('jquery'),
        null,
        true
      );
      wp_enqueue_script(
        'Map-js',
        get_template_directory_uri() . '/build/index.js',
        array('jquery'),
        null,
        true
      );
    }
  }
  function standort_boxes_display_callback($post)
  {
    include THEMEPATH . '/admin/php/admin_geo_metabox.php';
  }

  function standort_boxes()
  {
    add_meta_box(
      'standort', // name
      __('Standort: geographische Koordinaten'), //display text 
      array($this, 'standort_boxes_display_callback'), // call back function  
      'marker'
    );
  }

  function save_standort_box($post_id)
  {
    //don't autosave lat/long
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if ($parent_id = wp_is_post_revision($post_id)) {
      $post_id = $parent_id;
    }

    // show at/long in form if already exists (when editing existing post)
    $fields = [
      'latitude',
      'longitude',
    ];
    foreach ($fields as $field) {
      if (array_key_exists($field, $_POST)) {
        update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
      }
    }
  }

  //////////end------------Meta data for new Post page----------------// 



}

new Admin_mapapp();
