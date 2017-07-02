<?php 
/**
* single - template to display pages content
* @package AMP - Accelerated Mobile Pages
* @since 0.1
*/
get_header(); ?>
<div class="wrap">
    <?php get_template_part( 'content', 'page'); ?>
</div>
<?php get_footer(); ?>