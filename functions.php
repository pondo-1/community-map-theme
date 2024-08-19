<?php
// npm install leaflet
//   npm i leaflet.markercluster
//   npm i leaflet.markercluster.layersupport
//   npm i leaflet-draw


if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('THEMEPATH', get_template_directory());
define('FUNCTIONSPATH', THEMEPATH . '/functions/');

// dev -> After dev it need to be deleted
require_once(FUNCTIONSPATH . 'dev.php');
// initial settings 
require_once(FUNCTIONSPATH . 'dep_check.php');
//Custom admin menu page, Karte taxonomie
require_once(FUNCTIONSPATH . 'admin_menu.php');
// Geocode, Taxonomie für Map -> maptax
require_once(FUNCTIONSPATH . 'post_meta_setting.php');

// View 
// HTMl Structure(Template), Leaflet Javascript, Data für Leaflet and List
// leaflet
require_once(FUNCTIONSPATH . 'leaflet.php');
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
