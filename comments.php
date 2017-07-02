<?php
/**
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */

if ( post_password_required() ) {
    return;
}
  
?>
<div class="container main">
    <div id="comments">
        <?php wp_amp::comment_form(); ?><hr>
        <?php 
        if ( have_comments() ) : 
        ?>
            <h2 class="comments-title"><?php
                $comments_number = get_comments_number();
                if ( 1 === $comments_number ) {
                    /* translators: %s: post title */
                    printf( esc_attr_x( 'One thought on "%s"', 'comments title', 'amp-accelerated-mobile-pages' ), get_the_title() );
                } else {
                    printf(
                        esc_html(
                            /* translators: 1: number of comments, 2: post title */
                            _n(
                                '%1$s thought on "%2$s"',
                                '%1$s thoughts on "%2$s"',
                                $comments_number,
                                'amp-accelerated-mobile-pages'
                            )
                        ),
                        esc_attr(number_format_i18n( $comments_number )),
                        get_the_title()
                    );
                }
               
            ?></h2>
                    
            <ol class="comment-list">
                    <?php wp_list_comments(array(
                        'callback'    => array('wp_amp_compatibilities', 'comment_list'),
                        'short_ping'  => true,
                        'style'       => 'ol',
                        'avatar_size' => 56,
                        'reverse_top_level' => true,

                    )); ?>
            </ol>
            <?php the_comments_navigation(); ?>
            
        <?php 
        endif; 
        ?>
        <?php
        // If comments are closed and there are comments, let's leave a little note, shall we?
        if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
         ?>
        <p class="no-comments"><?php esc_attr_e( 'Comments are closed.', 'amp-accelerated-mobile-pages' ); ?></p>
    <?php endif; ?>
    </div>
</div>