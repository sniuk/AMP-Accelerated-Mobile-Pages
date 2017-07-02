<?php
/**
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */
?>
<div class="post-item">
   
    <a href="<?php echo esc_url(get_permalink()); ?>" class="post-title">
    <h4><?php the_title(); ?></h4>
  </a>
    <p class="post-excerpt small"><?php the_excerpt();?></p>
    <div class="event-meta">
      <span class="event-time smaller">
       <?php 
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
          $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf( $time_string,
          esc_attr( get_the_date( 'c' ) ),
          get_the_date(),
          esc_attr( get_the_modified_date( 'c' ) ),
          get_the_modified_date()
        );
       echo $time_string;
       ?>
      </span>
      
    </div>
</div>
