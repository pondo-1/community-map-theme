<?php

/**
 * The template for displaying the footer
 *
 */
?>

</main><!-- .site-main -->

<?php $welcome_popup = get_field('use_welcome_popup', 'option');  ?>
<?php ($welcome_popup == true) ? get_template_part('template-parts/welcome_popup') : "" ?>

<footer class="footer">
</footer>
<?php wp_footer(); ?>
</body>

</html>