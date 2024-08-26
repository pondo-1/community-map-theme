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
          <h1 class="logo"><span class="d_blue">ILEK Online</span></h1>
        </a>
      </li>
      <li>
        <a class="info" href="#">
          <svg width="25px" height="25px" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <title>Info-Icon</title>
            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <g id="01-willkommen" transform="translate(-593.000000, -38.000000)">
                <g id="Info-Icon" transform="translate(593.000000, 38.000000)">
                  <path d="M19.542,10 C19.542,15.2877778 15.2553333,19.5733333 9.96866667,19.5733333 C4.68088889,19.5733333 0.394222222,15.2877778 0.394222222,10 C0.394222222,4.71222222 4.68088889,0.426666667 9.96866667,0.426666667 C15.2553333,0.426666667 19.542,4.71222222 19.542,10" id="Fill-1" fill="#FFFFFF"></path>
                  <path d="M10,0 C4.47666667,0 0,4.47777778 0,10 C0,15.5222222 4.47666667,20 10,20 C15.5222222,20 20,15.5222222 20,10 C20,4.47777778 15.5222222,0 10,0 M10,2.22222222 C14.2888889,2.22222222 17.7777778,5.71111111 17.7777778,10 C17.7777778,14.2888889 14.2888889,17.7777778 10,17.7777778 C5.71111111,17.7777778 2.22222222,14.2888889 2.22222222,10 C2.22222222,5.71111111 5.71111111,2.22222222 10,2.22222222" id="Fill-3" fill="#414798"></path>
                  <path d="M8.65288889,15.6503333 L8.65288889,7.837 C8.65288889,7.72588889 8.73177778,7.64588889 8.84288889,7.64588889 L11.0928889,7.64588889 C11.204,7.64588889 11.284,7.72588889 11.284,7.837 L11.284,15.6503333 C11.284,15.7614444 11.204,15.8403333 11.0928889,15.8403333 L8.84288889,15.8403333 C8.73177778,15.8403333 8.65288889,15.7614444 8.65288889,15.6503333 M8.60511111,5.53811111 C8.60511111,4.71366667 9.17511111,4.15922222 9.984,4.15922222 C10.7928889,4.15922222 11.3628889,4.71366667 11.3628889,5.53811111 C11.3628889,6.33144444 10.7762222,6.917 9.984,6.917 C9.17511111,6.917 8.60511111,6.33144444 8.60511111,5.53811111" id="Fill-5" fill="#414798"></path>
                </g>
              </g>
            </g>
          </svg>
          Info
        </a>
      </li>
    </ul>
    <div class="main_map_block map_block" id="main_page_map"></div>
  </div>

  <div class="content sidebar" id="side_bar?">
    <div class="scrolldown_wrapper"><a href="#checkboxes" aria-label="scrolldown"><span class="scrolldown icon"></span></a></div>

    <div id="checkboxes" class="category_filter_section">
      <!--div id checkboxes  -->
      <h2 class="category">Kategorie</h2>
      <button class="all">Select all</button>
      <button class="none">Deselect all</button>
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
    </div> <!-- closing div id checkboxes  -->

    <div class="sort_options_block">
      <span>Sortieren nach</span>
      <select name="sort_options" id="main_page_list_sort_options">
        <option value="0" selected="">Aktuellste zuerst</option>
        <option value="1">Alpabetisch nach Title</option>
        <!-- <option value="2">Alpabetisch nach Autor</option> -->
      </select>
    </div>


    <div class="search">
      <input type="search" id="search" name="search" class="searchTerm" placeholder="Einträge durchsuchen">
      <button type="submit" class="searchButton">
        <svg viewBox="0 0 1024 1024">
          <path class="path1" d="M848.471 928l-263.059-263.059c-48.941 36.706-110.118 55.059-177.412 55.059-171.294 0-312-140.706-312-312s140.706-312 312-312c171.294 0 312 140.706 312 312 0 67.294-24.471 128.471-55.059 177.412l263.059 263.059-79.529 79.529zM189.623 408.078c0 121.364 97.091 218.455 218.455 218.455s218.455-97.091 218.455-218.455c0-121.364-103.159-218.455-218.455-218.455-121.364 0-218.455 97.091-218.455 218.455z">
          </path>
        </svg>
      </button>
    </div>

    <!-- <div id="livesearch"></div> -->
    <div class="marker_list_wrapper">
      <div class="marker_list" id="datenbank_list">
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