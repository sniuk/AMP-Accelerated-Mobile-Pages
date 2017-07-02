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
                    <h1 class="entry-title"><?php esc_attr_e( 'Nothing Found', 'amp-accelerated-mobile-pages' ); ?></h1>
              </header><!-- .entry-header -->
              <div class="post-content">
                   <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

                    <p><?php
                    /* translators: 1: link to new post */ 
                    printf( esc_html__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'amp-accelerated-mobile-pages' ), esc_url( admin_url( 'post-new.php' ) ) ); 
                    ?></p>

                  <?php elseif ( is_search() ) : ?>

                    <p><?php esc_attr_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'amp-accelerated-mobile-pages' ); ?></p>
                    

                  <?php else : ?>

                    <p><?php esc_attr_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'amp-accelerated-mobile-pages' ); ?></p>

                  <?php endif; ?>

              </div>
                  
          </article>
      </div>
</div>


