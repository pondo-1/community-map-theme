<?php

/**
 * The header.
 *
 * This is the template that displays all of the <head> section and everything up until main.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- no more pingback at least since Twenty Twenty-One 1.0 and some recommends disable pingpack https://themeisle.com/blog/wordpress-pingbacks/#gref-->
  <?php wp_head(); ?>
</head>


<body
  <?php body_class(); ?>
  <?php if (get_post_type() == 'marker') : ?> data-post-id="<?php echo get_the_ID(); ?>" <?php endif; ?>>

  <?php
  wp_body_open(); //do_action( 'wp_body_open' ); ex add_action( 'wp_body_open', 'google_tags_manager_body_open_scripts' );
  ?>

  <!-- Header Before main     -->
  <!-- <?php //get_template_part('template-parts/header/header');
        ?> -->

  <!-- main  -->
  <main class="main">