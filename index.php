<?php

/**
 * Defult template
 *
 */
?>

<?php get_header(); ?>
<?php get_template_part('template-parts/nav-top'); ?>

<div class="content">
  <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
  <?php echo get_the_content(); ?>
</div>

<nav class="footer_nav">
  <ul>
    <li><a href="/impressum/">Impressum</a></li>
    <li><a href="/datenschutz">Datenschutzerklärung</a></li>
  </ul>
</nav>
<?php get_footer(); ?>