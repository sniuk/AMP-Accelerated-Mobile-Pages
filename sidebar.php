<?php
/**
* The Sidebar containing the main widget areas.
* @package AMP - Accelerated Mobile Pages
* @since 0.1
*/
if ( ! dynamic_sidebar( 'sidebar_amp' ) ) : ?>
    

<?php else : ?>
    <?php get_sidebar( 'sidebar_amp' ); ?>

<?php endif;  ?>