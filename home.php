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
      <select name="sort_options" id="list_sort_options">
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
        <?php
        $html = '';
        $args = array(
          'post_type' => 'marker', // Custom post type
          'posts_per_page' => -1, // Retrieve all posts
          'post_status' => 'publish' // Only published posts
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
          while ($query->have_posts()) {
            $query->the_post();

            $post_id = get_the_ID();
            $title = get_the_title();
            $date = get_the_date(); // Default format, or use get_the_date('Y-m-d') for specific formatting
            $author = get_the_author();

            // Assume 'markertax' is the taxonomy and 'category_icon_url' is stored in term meta
            $terms = get_the_terms($post_id, 'markertax');
            $category_name = $terms ? $terms[0]->name : '';
            $category_slug = $terms ? $terms[0]->slug : '';
            $category_icon_url = $terms ? get_term_meta($terms[0]->term_id, 'taxonomy-icon', true) : '';

            $url = get_permalink();

            // Create HTML structure
            $html .= '<div class="marker--entry map_link_point category_' . esc_attr($category_slug) . '" id="map_id_' . esc_attr($post_id) . '" category="' . esc_attr($category_slug) . '" date="' . esc_attr($date) . '" author="' . esc_attr($author) . '">';
            $html .= '<div class="entry_title">' . esc_html($title) . '</div>';
            $html .= '<div class="entry_date">' . esc_html($date) . '</div>';
            $html .= '<div class="entry_author">' . esc_html($author) . '</div>';
            $html .= '<div class="entry_category">';
            if (!empty($category_icon_url)) {
              $html .= '<img src="' . esc_url($category_icon_url) . '" />';
            }
            $html .= esc_html($category_name);
            $html .= '</div>';
            $html .= '<a class="dn button main-page-button" href="' . esc_url($url) . '">Eintrag ansehen</a>';
            $html .= '</div>';
          }
        } else {
          $html = '<p>No markers found.</p>';
        }
        wp_reset_postdata(); // Reset the post data to avoid conflicts elsewhere
        echo $html;
        ?>
        <!-- only the javascript part need to be printed with javascript -->
        <!-- <div class="marker--entry map_link_point category_ile-projekt" id="map_id_96" echo ="ile-projekt" date="22. August 2024" author="Okto" value="131">
          <div class="entry_title">W1pYdiGhEG5SrL</div>
          <div class="entry_date">22. August 2024</div>
          <div class="entry_author">Okto</div>
          <div class="entry_category">
            <img src="http://localhost:10059/wp-content/themes/community-map-theme/assets/markertax/ILE-Projekt.svg">
            ILE-Projekt
          </div>
          <a class="dn button main-page-button" href="http://localhost:10059/marker/w1pydigheg5srl/">Eintrag ansehen</a>
        </div> -->
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