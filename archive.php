<?php
/**
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */ 
get_header(); ?>

  <div class="wrap">
  <div class="container main">
      <nav class="doc-sidebar">

        <div class="current-sections">
          <?php get_sidebar(); ?>
        </div>

    </nav>
    <div class="content">
      <article class="post">

   
        <?php
            the_archive_title( '<h1 class="post-title">', '</h1>' );
            the_archive_description( '<div class="taxonomy-description">', '</div>' );
        ?>
        <div class="post-content">
          


            <div class="card-container grid">


    
                    <?php 
                      if ( have_posts() ) : 

                        while ( have_posts() ) : the_post();
                          get_template_part('templates/list-content', get_post_format()); 
                        endwhile;
                        // Previous/next page navigation.
                        
                      endif; 
                    ?> 
            </div>
                    
                

            </div>
       </article>
        <?php
          wp_amp_the_posts_pagination();
        ?>
        </div>
    </div>
</div>


<?php get_footer(); ?>