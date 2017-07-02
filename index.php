<?php
/**
 * The main template file
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */ 
get_header(); ?>
<?php 
  if ( have_posts() ) : ?>
<div class="wrap">
  <div class="container main">
      
    <div class="content">
      <article class="post">

        <?php 
        if ( is_home() ) : 
        ?>
          <h1 class="post-title"><?php esc_attr_e('Home', 'amp-accelerated-mobile-pages') ?></h1>
        <?php 
        endif; 
        ?>
        
        <div class="post-content">
          
          <div class="card-container grid">
  						
    				<?php
    				while ( have_posts() ) : the_post();
    					get_template_part('templates/list-content', get_post_format() ); 
    				endwhile;  						
    				?>
          </div>
        </div>
      </article>
      <?php
      wp_amp_the_posts_pagination();
      ?>
    </div>
    <nav class="doc-sidebar">

      <div class="current-sections">
        <?php get_sidebar(); ?>
      </div>
    </nav>
    </div>
</div>
<?php
else :
  get_template_part( 'content', 'none' ); 
endif; 
?> 
<?php 
get_footer(); ?>