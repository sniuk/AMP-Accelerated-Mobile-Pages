<?php
/**
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */
?>

<div class="container main">
      <nav class="doc-sidebar">

          <div class="current-header">
          </div>

          <div class="current-sections">
            <?php get_sidebar(); ?>
          </div>
      </nav>
      <div class="content">

          <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
             
              <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
              </header><!-- .entry-header -->
              <div class="post-content">
                    <?php
                      // Post thumbnail.
                      wp_amp_post_thumbnail();
                    ?>
                    <?php

                    the_content(); 

                    wp_link_pages( array(
                      'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'amp-accelerated-mobile-pages' ) . '</span>',
                      'after'       => '</div>',
                      'link_before' => '<span>',
                      'link_after'  => '</span>',
                      'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'amp-accelerated-mobile-pages' ) . ' </span>%',
                      'separator'   => '<span class="screen-reader-text">, </span>',
                    ) );
                    ?> 


              </div>
                  <footer class="entry-footer">
                    <?php wp_amp_theme_entry_meta(); ?>
                    <?php edit_post_link( esc_attr_e( 'Edit', 'amp-accelerated-mobile-pages' ), '<span class="edit-link">', '</span>' ); ?>
                  </footer><!-- .entry-footer -->
          </article>
      </div>
</div>


