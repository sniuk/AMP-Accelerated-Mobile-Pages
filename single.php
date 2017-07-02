<?php 
/**
 * Single - template to display single posts
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */

/**
* Get the scripts and styles from post content.
* @since 0.2.0
*/
while (have_posts()) : the_post();
	wp_amp::post_scripts_styles();   			
endwhile;



get_header(); ?>
<div class="wrap">
	  
		<?php
			// Start the loop.
			while ( have_posts() ) : the_post();
	   			get_template_part( 'content', get_post_format()); 
	   			// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

	    	endwhile;
	    ?>

</div>
<?php get_footer(); ?>