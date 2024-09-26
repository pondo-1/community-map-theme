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
    <div class="main_map_block map_block" id="mapapp_map"></div>
  </div>

  <div class="content sidebar">

    <div class="scrolldown_wrapper"><a href="#content_start" aria-label="scrolldown"><span class="scrolldown icon"></span></a></div>
    <div id="content_start" class="post_content_block">
      <div class="post_content">
        <a aria-label="zurück" href="/" class="close"><span class="close close_icon"><?php echo file_get_contents(get_template_directory_uri() . '/assets/mapapp/icon_x.svg'); ?></span></a>
        <h1><?php echo  get_the_title(); ?></h1>
        <div class="entry_category">

          <?php
          $post_id = get_the_ID();
          $terms = get_the_terms($post_id, 'markertax');
          $category_name = $terms ? $terms[0]->name : '';
          $category_icon_url = $terms ? get_term_meta($terms[0]->term_id, 'taxonomy-icon', true) : '';

          ?>
          <img src="<?php echo $category_icon_url ?>" /><b><?php echo $category_name; ?></b>
          <span><?php echo get_the_date(); ?></span>
        </div>
        <?php
        //echo get_the_content();
        the_content()
        ?>
        <!-- //   </div>  -->
        <div class="content_footer">
          <p class="content_footer_text"><?php
                                          global $post;
                                          $author = get_the_author_meta('display_name', $post->post_author);
                                          $date = get_the_date('d.m.Y');
                                          $string = 'Eintrag erstellt von ' .  $author . ' am ' . $date . '.';
                                          echo $string; ?>
          </p>

          <a class="share mobile" href="whatsapp://send?text=Ein Beitrag aus der Kulturdatenbank Sinngrund, den ich teilen möchte: <?php echo get_permalink() ?>">Eintrag teilen per WhatsApp</a>
          <a class="share desktop" href="mailto:?subject=Eintrag der Kulturdatenbank Sinngrund&amp;body=Ein Beitrag aus der Kulturdatenbank Sinngrund, den ich teilen möchte: <?php echo get_permalink() ?>">Eintrag teilen per Email</a>
          share icon
        </div>
        <?php //echo do_shortcode('[gravityform id="4" title="true"]'); 
        ?>
        <?php //include 'nav_footer.php'; 
        ?>
        <?php
        // echo $sinngrundKultureBank->add_author_in_content(get_the_content()) . 'this';
        wp_reset_postdata();
        ?>
      </div> <!-- // closeing class post_content -->
    </div> <!-- // closeing class datenbank list block -->

    <nav class="footer_nav">
      <ul>
        <li><a href="/impressum/">Impressum</a></li>
        <li><a href="/datenschutz">Datenschutzerklärung</a></li>
      </ul>
    </nav>

  </div>

</div>
<?php get_footer(); ?>