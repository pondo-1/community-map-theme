<?php

/**
 * Defult template
 *
 */
?>

<?php get_header(); ?>
<div class="mapapp">
  <div class="map-block">
    <ul class="menu top">
      <li>
        <a class="logo" href="/">
          <h1 class="logo"><span><?php echo bloginfo('title'); ?></span></h1>
        </a>
      </li>
      <li>
        <a class="info" href="#">
          <?php echo file_get_contents(get_template_directory_uri() . '/assets/mapapp/icon-info.svg'); ?>
          Info
        </a>
      </li>
    </ul>
    <div class="main_map_block map_block" id="main_page_map"></div>
  </div>

  <div class="content sidebar">
    <div class="scrolldown_wrapper">
      <a href="#checkboxes" aria-label="scrolldown">
        <span class="scrolldown icon">
        </span>
      </a>
    </div>

    <div id="checkboxes" class="category_filter">
      <!--div id checkboxes  -->
      <div class="category_filter--title">
        Kategorie
      </div>
      <div class="category_filter--button">
        <button class="all">Select all</button>
        <button class="none">Deselect all</button>
      </div>
      <div class="category_filter--list">

        <?php
        $terms = get_terms(array(
          'taxonomy' => 'markertax',
          'hide_empty' => false,
        ));
        ?>
        <?php foreach ($terms as $term) : ?>
          <div class="single_category_wrapper"><input class="cat_checkbox" type="checkbox" id="<?php echo $term->slug ?>" value="<?php echo $term->slug ?>" category_name="<?php echo $term->name ?>" name="kategory_filter" checked="true">
            <label class="cat_label" for="<?php echo $term->slug ?>"><img class="cat_icon" src="<?php echo get_term_meta($term->term_id, 'taxonomy-icon', true) ?>"><span class="cat_name"><?php echo $term->name ?></span></label>
          </div>
        <?php endforeach ?>
      </div>
    </div> <!-- closing div id checkboxes  -->

    <div class="sort_options_block">
      <span>Sortieren nach</span>
      <select name="sort_options" id="main_page_list_sort_options">
        <option value="0" selected="">Aktuellste zuerst</option>
        <option value="1">Alpabetisch nach Title</option>
        <!-- <option value="2">Alpabetisch nach Autor</option> -->
      </select>
    </div>


    <div class="search_wrapper">
      <input type="search" id="search" name="search" class="searchTerm" placeholder="Einträge durchsuchen">
      <button type="submit" class="searchButton">
        <?php echo file_get_contents(get_template_directory_uri() . '/assets/mapapp/icon-search.svg'); ?>
      </button>
    </div>

    <!-- <div id="livesearch"></div> -->
    <div class="marker_list_wrapper">
      <div class="marker_list" id="marker_list">
      </div>
    </div>

    <div class="legal">
      <nav class="footer_nav">
        <ul>
          <li><a href="/impressum/">Impressum</a></li>
          <li><a href="/datenschutz">Datenschutzerklärung</a></li>
        </ul>

      </nav>
    </div>
  </div>
</div>
<?php get_footer(); ?>