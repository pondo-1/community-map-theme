<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('THEMEPATH', get_template_directory());
define('THEMENAME', wp_get_theme()->get_stylesheet());
define('FUNCTIONSPATH', THEMEPATH . '/functions/');

// dev -> After dev it need to be deleted
require_once(FUNCTIONSPATH . 'dev.php');
// dependency & Essential check, Acf pro plugin check 
require_once(FUNCTIONSPATH . 'dep_check.php');
//Custom admin menu page 
require_once(FUNCTIONSPATH . 'admin_menu.php');

// Data Handling 
// -- Prepare cpt(mappoints), , Karte taxonomie(markertax)  & Admin columns -> Data  & View 
require_once(FUNCTIONSPATH . 'cpt_post_meta_init.php');
// -- API 
require_once(FUNCTIONSPATH . 'custom_api_endpoint.php');

// View 
// -- HTMl Structure(Template), Leaflet Javascript, Data für Leaflet and List
// admin template for the metabox 
require_once(FUNCTIONSPATH . 'admin_map_metabox_prep.php');

// -- Enque leaflet for front & Backend, where it needs
require_once(FUNCTIONSPATH . 'leaflet.php');
require_once(FUNCTIONSPATH . 'marker_list.php');

// -- Welcome popup 
require_once(FUNCTIONSPATH . 'welcome_popup.php');

/* Disable WordPress Admin Bar for all users */

// Initial settings/ Admin / Acf
// Save the Categories(icon, color, text)/ main color / Logo / 


class CommunityMap
{
  private $categories_string = "";
  function __construct()
  {
    add_action('wp_enqueue_scripts', [$this, 'theme_files']);
  }

  // Enqueue style and js
  function theme_files()
  {
    //front end
    wp_enqueue_style('theme_main_styles', get_theme_file_uri('/build/style-index.css'));
    // wp_enqueue_style('theme_main_styles_2', get_theme_file_uri('/build/index.css'));

    // Javascript need to be loaded in footer: last variable need to be true
    wp_enqueue_script('theme_js', get_template_directory_uri() . '/build/index.js', array('jquery'), '', true);
  }
}

new CommunityMap();
